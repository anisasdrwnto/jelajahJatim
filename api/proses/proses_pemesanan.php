<?php
session_start();
header('Content-Type: application/json');
require_once '../koneksi.php'; // Pastikan file ini berisi koneksi $pdo

class Tiket {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /* Ambil daftar jenis tiket untuk satu event (dipakai saat modal "Pesan Tiket" dibuka) */
    public function ambilTiket($event_id) {
        $stmt = $this->db->prepare("SELECT * FROM mst_booking_tiket WHERE mbt_id_event = ?");
        $stmt->execute([$event_id]);
        return [
            'success' => true,
            'data'    => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    private function generateIdBooking() {
        return 'BK' . date('ymd') . strtoupper(substr(uniqid(), -6));
    }

    private function generateBookingCode() {
        return strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    }

    /* Simpan pesanan baru ke mst_transaksi_booking.
       Kuota terjual (mbt_kuota_terjual) langsung bertambah begitu booking
       berhasil disimpan -- tidak peduli metode bayar Cash maupun QRIS.
       Status pembayaran (Lunas/Pending) hanya dipakai untuk keperluan
       verifikasi panitia di lokasi, bukan untuk menentukan kuota. */
    public function pesanTiket($data) {
        // PENTING: mtbk_id_user bertipe varchar (contoh: "USR001"), jadi JANGAN
        // di-cast ke (int) -- itu akan selalu menghasilkan 0 untuk string
        // yang tidak diawali angka. Cukup trim sebagai string.
        $userId = $data['user_id'] ?? null;
        $userId = ($userId !== null && $userId !== '') ? trim((string)$userId) : null;

        $eventId = $data['event_id'] ?? '';
        $tiketId = $data['tiket_id'] ?? '';
        $jumlah  = (int)($data['jumlah_tiket'] ?? 0);
        $metode  = $data['metode_bayar'] ?? '';
        $nama    = trim($data['nama_pemesan']  ?? '') ?: null;
        $email   = trim($data['email_pemesan'] ?? '') ?: null;
        $noHp    = trim($data['no_hp_pemesan']  ?? '') ?: null;

        if (!$eventId || !$tiketId || $jumlah <= 0 || !$metode || $userId === null) {
            return ['success' => false, 'message' => 'Data pemesanan tidak lengkap (pastikan Anda sudah login).'];
        }

        $this->db->beginTransaction();
        try {
            // Lock baris tiket ini supaya tidak ada pemesanan lain yang
            // baca kuota di waktu yang sama (mencegah race condition saat
            // kuota tersisa sedikit dan dipesan banyak orang bersamaan).
            $stmt = $this->db->prepare(
                "SELECT * FROM mst_booking_tiket WHERE mbt_id_tiket = ? AND mbt_id_event = ? FOR UPDATE"
            );
            $stmt->execute([$tiketId, $eventId]);
            $tiket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tiket) {
                throw new Exception('Tiket tidak ditemukan.');
            }

            $kuota     = (int) ($tiket['mbt_kuota'] ?? 0);
            $terjual   = (int) ($tiket['mbt_kuota_terjual'] ?? 0);
            $sisaKuota = $kuota - $terjual;

            if ($jumlah > $sisaKuota) {
                throw new Exception('Jumlah tiket melebihi sisa kuota (sisa: ' . $sisaKuota . ').');
            }

            $totalHarga  = $tiket['mbt_harga'] * $jumlah;
            $statusBayar = ($metode === 'QRIS') ? 'Lunas' : 'Pending';
            $idBooking   = $this->generateIdBooking();
            $bookingCode = $this->generateBookingCode();

            // 1. Insert transaksi booking
            $stmt = $this->db->prepare("
                INSERT INTO mst_transaksi_booking (
                    mtbk_id_booking, mtbk_booking_code, mtbk_id_event, mtbk_id_tiket, mtbk_id_user,
                    mtbk_jumlah_tiket, mtbk_total_harga, mtbk_nama_pemesan, mtbk_email_pemesan,
                    mtbk_no_hp_pemesan, mtbk_status_bayar, mtbk_metode_bayar, mtbk_status_booking
                ) VALUES (:id_booking, :booking_code, :id_event, :id_tiket, :id_user,
                          :jumlah_tiket, :total_harga, :nama_pemesan, :email_pemesan,
                          :no_hp_pemesan, :status_bayar, :metode_bayar, 'Aktif')
            ");
            $stmt->bindValue(':id_booking', $idBooking, PDO::PARAM_STR);
            $stmt->bindValue(':booking_code', $bookingCode, PDO::PARAM_STR);
            $stmt->bindValue(':id_event', $eventId, PDO::PARAM_STR);
            $stmt->bindValue(':id_tiket', $tiketId, PDO::PARAM_STR);
            // mtbk_id_user adalah varchar -> selalu bind sebagai string
            $stmt->bindValue(':id_user', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':jumlah_tiket', $jumlah, PDO::PARAM_INT);
            $stmt->bindValue(':total_harga', $totalHarga);
            $stmt->bindValue(':nama_pemesan', $nama, $nama === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':email_pemesan', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':no_hp_pemesan', $noHp, $noHp === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':status_bayar', $statusBayar, PDO::PARAM_STR);
            $stmt->bindValue(':metode_bayar', $metode, PDO::PARAM_STR);
            $stmt->execute();

            // 2. Update kuota terjual -- langsung jalan untuk Cash maupun QRIS
            $update = $this->db->prepare("
                UPDATE mst_booking_tiket
                SET mbt_kuota_terjual = mbt_kuota_terjual + :jumlah
                WHERE mbt_id_tiket = :id
                  AND (mbt_kuota - mbt_kuota_terjual) >= :jumlah2
            ");
            $update->bindValue(':jumlah', $jumlah, PDO::PARAM_INT);
            $update->bindValue(':jumlah2', $jumlah, PDO::PARAM_INT);
            $update->bindValue(':id', $tiketId, PDO::PARAM_STR);
            $update->execute();

            if ($update->rowCount() === 0) {
                // Jaga-jaga kalau kuota berubah di antara pengecekan dan update
                throw new Exception('Kuota tiket tidak mencukupi.');
            }

            $this->db->commit();
            return [
                'success'      => true,
                'message'      => 'Pesanan berhasil disimpan.',
                'booking_code' => $bookingCode,
                'status'       => $statusBayar
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage()];
        }
    }

    /* Dipakai panitia/admin untuk konfirmasi pembayaran Cash di lokasi.
       Kuota SUDAH berkurang sejak booking dibuat, jadi fungsi ini sekarang
       hanya mengubah status pembayaran, tidak menyentuh kuota lagi. */
    public function verifikasiPembayaran($idBooking) {
        $stmt = $this->db->prepare("SELECT * FROM mst_transaksi_booking WHERE mtbk_id_booking = ?");
        $stmt->execute([$idBooking]);
        $trx = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trx) {
            return ['success' => false, 'message' => 'Booking tidak ditemukan.'];
        }
        if ($trx['mtbk_status_bayar'] === 'Lunas') {
            return ['success' => false, 'message' => 'Booking ini sudah lunas sebelumnya.'];
        }

        $stmt = $this->db->prepare("UPDATE mst_transaksi_booking SET mtbk_status_bayar = 'Lunas' WHERE mtbk_id_booking = ?");
        $stmt->execute([$idBooking]);

        return ['success' => true, 'message' => 'Pembayaran berhasil diverifikasi.'];
    }

    /* Daftar booking untuk satu event, dipakai panitia/admin untuk melihat
       siapa saja yang sudah pesan + status pembayarannya. */
    public function daftarBookingEvent($eventId) {
        $stmt = $this->db->prepare("
            SELECT * FROM mst_transaksi_booking
            WHERE mtbk_id_event = ?
            ORDER BY mtbk_createDate DESC
        ");
        $stmt->execute([$eventId]);
        return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
}

/* ============================================================
   ROUTER
============================================================ */
$tiket  = new Tiket($pdo);
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'ambil_tiket':
        echo json_encode($tiket->ambilTiket($_GET['event_id'] ?? ''));
        break;

    case 'pesan': // dipanggil oleh form #formPesanTiket (POST)
    if (!isset($_SESSION['mus_id_users'])) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login untuk memesan tiket.']);
        break;
    }
    $payload = $_POST;
    $payload['user_id']      = $_SESSION['mus_id_users'];
    $payload['nama_pemesan'] = $payload['nama_pemesan'] ?? ($_SESSION['mus_name'] ?? null);
    echo json_encode($tiket->pesanTiket($payload));
    break;

    case 'verifikasi': // dipanggil dari dashboard admin
        echo json_encode($tiket->verifikasiPembayaran($_POST['id_booking'] ?? ''));
        break;

    case 'daftar_booking_event': // dipanggil dari dashboard admin
        echo json_encode($tiket->daftarBookingEvent($_GET['event_id'] ?? ''));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
}