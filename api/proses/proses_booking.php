<?php

header('Content-Type: application/json');

require_once '../koneksi.php';

class BookingTiket {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Tambah jenis tiket
    public function tambahTiket($data) {
        $cekKuota = $this->validasiKuota($data['kuota'], $data['kuotaTerjual'] ?? 0);
        if (!$cekKuota['success']) {
            return $cekKuota;
        }

        $idBaru = $this->generateId();

        $statement = $this->db->prepare(
            "INSERT INTO mst_booking_tiket
                (mbt_id_tiket, mbt_id_event, mbt_nama_tiket, mbt_harga, mbt_kuota, mbt_kuota_terjual)
            VALUES
                (:id, :idEvent, :namaTiket, :harga, :kuota, :kuotaTerjual)"
        );

        $statement->execute([
            ':id'           => $idBaru,
            ':idEvent'      => $data['idEvent'],
            ':namaTiket'    => $data['namaTiket'] ?? null,
            ':harga'        => $data['harga'],
            ':kuota'        => $data['kuota'],
            ':kuotaTerjual' => $data['kuotaTerjual'] ?? 0,
        ]);

        if ($statement->rowCount()) {
            return ['success' => true, 'message' => 'Tiket berhasil ditambahkan!', 'id' => $idBaru];
        }
        return ['success' => false, 'message' => 'Gagal menambahkan tiket!'];
    }

    // Ambil semua tiket (join ke mst_event supaya nama event ikut tampil)
    public function ambilSemua() {
        $stmt = $this->db->prepare(
            "SELECT bt.*, e.mev_nama_event
             FROM mst_booking_tiket bt
             LEFT JOIN mst_event e ON bt.mbt_id_event = e.mev_id_event
             ORDER BY bt.mbt_id_tiket ASC"
        );
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['success' => true, 'data' => $data];
    }

    public function cariTiket($keyword) {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $this->ambilSemua();
        }

        $cari = '%' . $keyword . '%';

        $stmt = $this->db->prepare(
            "SELECT bt.*, e.mev_nama_event
             FROM mst_booking_tiket bt
             LEFT JOIN mst_event e ON bt.mbt_id_event = e.mev_id_event
             WHERE bt.mbt_nama_tiket   LIKE :cari
                OR bt.mbt_id_tiket     LIKE :cari2
                OR e.mev_nama_event    LIKE :cari3
             ORDER BY bt.mbt_id_tiket ASC"
        );

        $stmt->bindValue(':cari',  $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari2', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari3', $cari, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'total'   => count($data),
            'data'    => $data,
        ];
    }

    public function ambilStatistik() {
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as total FROM mst_booking_tiket");
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtKuota = $this->db->prepare("SELECT COALESCE(SUM(mbt_kuota), 0) as total FROM mst_booking_tiket");
        $stmtKuota->execute();
        $totalKuota = $stmtKuota->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtTerjual = $this->db->prepare("SELECT COALESCE(SUM(mbt_kuota_terjual), 0) as total FROM mst_booking_tiket");
        $stmtTerjual->execute();
        $totalTerjual = $stmtTerjual->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtPendapatan = $this->db->prepare("SELECT COALESCE(SUM(mbt_harga * mbt_kuota_terjual), 0) as total FROM mst_booking_tiket");
        $stmtPendapatan->execute();
        $totalPendapatan = $stmtPendapatan->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'success' => true,
            'data'    => [
                'total_jenis_tiket' => $total,
                'total_kuota'       => (int) $totalKuota,
                'total_terjual'     => (int) $totalTerjual,
                'total_pendapatan'  => (float) $totalPendapatan,
            ]
        ];
    }

    // Ambil tiket by ID
    public function ambilById($id) {
        $stmt = $this->db->prepare(
            "SELECT bt.*, e.mev_nama_event
             FROM mst_booking_tiket bt
             LEFT JOIN mst_event e ON bt.mbt_id_event = e.mev_id_event
             WHERE bt.mbt_id_tiket = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return ['success' => true, 'data' => $data];
        }
        return ['success' => false, 'message' => 'Tiket tidak ditemukan!'];
    }

    // Ambil daftar event untuk dropdown pilihan di form
    public function ambilDaftarEvent() {
        $stmt = $this->db->prepare("SELECT mev_id_event, mev_nama_event FROM mst_event ORDER BY mev_nama_event ASC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['success' => true, 'data' => $data];
    }

    // Edit tiket
    public function editTiket($id, $data) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Tiket tidak ditemukan!'];
        }

        $cekKuota = $this->validasiKuota($data['kuota'], $data['kuotaTerjual'] ?? 0);
        if (!$cekKuota['success']) {
            return $cekKuota;
        }

        $stmt = $this->db->prepare(
            "UPDATE mst_booking_tiket SET
                mbt_id_event      = :idEvent,
                mbt_nama_tiket    = :namaTiket,
                mbt_harga         = :harga,
                mbt_kuota         = :kuota,
                mbt_kuota_terjual = :kuotaTerjual
            WHERE mbt_id_tiket = :id"
        );

        $stmt->execute([
            ':idEvent'      => $data['idEvent'],
            ':namaTiket'    => $data['namaTiket'] ?? null,
            ':harga'        => $data['harga'],
            ':kuota'        => $data['kuota'],
            ':kuotaTerjual' => $data['kuotaTerjual'] ?? 0,
            ':id'           => $id,
        ]);

        if ($stmt->rowCount()) {
            return ['success' => true, 'message' => 'Tiket berhasil diperbarui!'];
        }
        return ['success' => false, 'message' => 'Tidak ada perubahan data!'];
    }

    // Hapus tiket
    public function hapusTiket($id) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Tiket tidak ditemukan!'];
        }

        $stmt = $this->db->prepare("DELETE FROM mst_booking_tiket WHERE mbt_id_tiket = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount()) {
            return ['success' => true, 'message' => 'Tiket berhasil dihapus!'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus tiket!'];
    }

    // Helper - validasi kuota_terjual tidak boleh lebih besar dari kuota total
    private function validasiKuota($kuota, $kuotaTerjual) {
        if ((int) $kuotaTerjual > (int) $kuota) {
            return ['success' => false, 'message' => 'Kuota terjual tidak boleh lebih besar dari kuota total!'];
        }
        return ['success' => true];
    }

    // Helper - generate ID format BT001, BT002, dst
    private function generateId() {
        $stmt = $this->db->prepare("SELECT mbt_id_tiket FROM mst_booking_tiket ORDER BY mbt_id_tiket DESC LIMIT 1");
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $angka     = (int) substr($last['mbt_id_tiket'], 2);
            $angkaBaru = $angka + 1;
        } else {
            $angkaBaru = 1;
        }

        return 'BT' . str_pad($angkaBaru, 3, '0', STR_PAD_LEFT);
    }
}

session_start();

$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Action tidak boleh kosong!']);
    exit;
}

$booking = new BookingTiket($pdo);
$result  = [];

// DEV_MODE: tampilkan pesan error PHP/PDO asli ke response JSON saat development.
// Ubah ke false sebelum dipakai/deploy production, agar detail database tidak terekspos.
define('DEV_MODE', true);

try {

    switch ($action) {

        case 'tambah':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
                exit;
            }
            if (empty($_POST['idEvent']) || $_POST['harga'] === '' || $_POST['kuota'] === '') {
                echo json_encode(['success' => false, 'message' => 'Field wajib tidak boleh kosong!']);
                exit;
            }
            $result = $booking->tambahTiket($_POST);
            break;

        case 'baca':
            $id     = $_GET['id'] ?? null;
            $result = $id ? $booking->ambilById($id) : $booking->ambilSemua();
            break;

        case 'cari':
            $keyword = trim($_GET['q'] ?? '');
            $result  = $booking->cariTiket($keyword);
            break;

        case 'daftarEvent':
            $result = $booking->ambilDaftarEvent();
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
                exit;
            }
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID tiket tidak ditemukan!']);
                exit;
            }
            $result = $booking->editTiket($id, $_POST);
            break;

        case 'hapus':
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID tiket tidak ditemukan!']);
                exit;
            }
            $result = $booking->hapusTiket($id);
            break;

        case 'statistik':
            $result = $booking->ambilStatistik();
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
