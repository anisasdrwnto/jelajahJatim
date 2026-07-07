<?php

header('Content-Type: application/json');

require_once '../koneksi.php';

class DaftarPemesanan {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Ambil semua data pemesanan (transaksi booking)
    public function ambilSemua() {
        $stmt = $this->db->prepare(
            "SELECT mtbk_id_booking, mtbk_booking_code, mtbk_id_event, mtbk_id_tiket,
                    mtbk_id_user, mtbk_jumlah_tiket, mtbk_total_harga, mtbk_nama_pemesan,
                    mtbk_email_pemesan, mtbk_no_hp_pemesan, mtbk_status_bayar,
                    mtbk_metode_bayar, mtbk_status_booking, mtbk_createDate, mtbk_updateDate
             FROM mst_transaksi_booking
             ORDER BY mtbk_createDate DESC"
        );
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['success' => true, 'data' => $data];
    }

    // Cari pemesanan berdasarkan id booking, kode booking, atau nama pemesan
    public function cariPemesanan($keyword) {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $this->ambilSemua();
        }

        $cari = '%' . $keyword . '%';

        $stmt = $this->db->prepare(
            "SELECT mtbk_id_booking, mtbk_booking_code, mtbk_id_event, mtbk_id_tiket,
                    mtbk_id_user, mtbk_jumlah_tiket, mtbk_total_harga, mtbk_nama_pemesan,
                    mtbk_email_pemesan, mtbk_no_hp_pemesan, mtbk_status_bayar,
                    mtbk_metode_bayar, mtbk_status_booking, mtbk_createDate, mtbk_updateDate
             FROM mst_transaksi_booking
             WHERE mtbk_id_booking     LIKE :cari
                OR mtbk_booking_code   LIKE :cari2
                OR mtbk_nama_pemesan   LIKE :cari3
                OR mtbk_id_user        LIKE :cari4
             ORDER BY mtbk_createDate DESC"
        );

        $stmt->bindValue(':cari',  $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari2', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari3', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari4', $cari, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'total'   => count($data),
            'data'    => $data,
        ];
    }

    // Ambil satu data pemesanan by id
    public function ambilById($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM mst_transaksi_booking WHERE mtbk_id_booking = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return ['success' => true, 'data' => $data];
        }
        return ['success' => false, 'message' => 'Data pemesanan tidak ditemukan!'];
    }

    // Verifikasi pembayaran: ubah status_bayar dari Pending menjadi Lunas
    public function verifikasi($id) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Data pemesanan tidak ditemukan!'];
        }

        if ($dataLama['data']['mtbk_status_bayar'] !== 'Pending') {
            return ['success' => false, 'message' => 'Pemesanan ini tidak berstatus Pending!'];
        }

        $stmt = $this->db->prepare(
            "UPDATE mst_transaksi_booking
                SET mtbk_status_bayar = 'Lunas'
              WHERE mtbk_id_booking = :id"
        );
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount()) {
            return ['success' => true, 'message' => 'Pembayaran berhasil diverifikasi, status menjadi Lunas!'];
        }
        return ['success' => false, 'message' => 'Gagal memverifikasi pembayaran!'];
    }

    // Hapus data pemesanan
    public function hapusPemesanan($id) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Data pemesanan tidak ditemukan!'];
        }

        $stmt = $this->db->prepare("DELETE FROM mst_transaksi_booking WHERE mtbk_id_booking = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount()) {
            return ['success' => true, 'message' => 'Pemesanan berhasil dihapus!'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus pemesanan!'];
    }

    // Statistik ringkas untuk kartu di atas tabel
    public function ambilStatistik() {
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as total FROM mst_transaksi_booking");
        $stmtTotal->execute();
        $totalPemesanan = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtTiket = $this->db->prepare("SELECT COALESCE(SUM(mtbk_jumlah_tiket), 0) as total FROM mst_transaksi_booking");
        $stmtTiket->execute();
        $totalTiket = $stmtTiket->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtPending = $this->db->prepare("SELECT COUNT(*) as total FROM mst_transaksi_booking WHERE mtbk_status_bayar = 'Pending'");
        $stmtPending->execute();
        $totalPending = $stmtPending->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtPendapatan = $this->db->prepare("SELECT COALESCE(SUM(mtbk_total_harga), 0) as total FROM mst_transaksi_booking WHERE mtbk_status_bayar = 'Lunas'");
        $stmtPendapatan->execute();
        $totalPendapatan = $stmtPendapatan->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'success' => true,
            'data'    => [
                'total_pemesanan'     => (int) $totalPemesanan,
                'total_tiket_dipesan' => (int) $totalTiket,
                'total_pending'       => (int) $totalPending,
                'total_pendapatan'    => (float) $totalPendapatan,
            ]
        ];
    }
}

session_start();

$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Action tidak boleh kosong!']);
    exit;
}

$pemesanan = new DaftarPemesanan($pdo);
$result    = [];

// DEV_MODE: tampilkan pesan error PHP/PDO asli ke response JSON saat development.
// Ubah ke false sebelum dipakai/deploy production, agar detail database tidak terekspos.
define('DEV_MODE', true);

try {

    switch ($action) {

        case 'baca':
            $id     = $_GET['id'] ?? null;
            $result = $id ? $pemesanan->ambilById($id) : $pemesanan->ambilSemua();
            break;

        case 'cari':
            $keyword = trim($_GET['q'] ?? '');
            $result  = $pemesanan->cariPemesanan($keyword);
            break;

        case 'verifikasi':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
                exit;
            }
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID booking tidak ditemukan!']);
                exit;
            }
            $result = $pemesanan->verifikasi($id);
            break;

        case 'hapus':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
                exit;
            }
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID booking tidak ditemukan!']);
                exit;
            }
            $result = $pemesanan->hapusPemesanan($id);
            break;

        case 'statistik':
            $result = $pemesanan->ambilStatistik();
            break;

        default:
            $result = ['success' => false, 'message' => 'Action tidak dikenal!'];
            break;
    }

} catch (PDOException $e) {
    $result = [
        'success' => false,
        'message' => DEV_MODE
            ? 'Database error: ' . $e->getMessage()
            : 'Terjadi kesalahan pada database. Silakan coba lagi nanti.'
    ];
} catch (Throwable $e) {
    $result = [
        'success' => false,
        'message' => DEV_MODE
            ? 'Server error: ' . $e->getMessage()
            : 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'
    ];
}

echo json_encode($result);
?>