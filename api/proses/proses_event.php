<?php

header('Content-Type: application/json');

require_once '../koneksi.php';

class Event {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Tambah event
    public function tambahEvent($data, $foto) {
        $namaFoto = $this->uploadFoto($foto);
        if (!$namaFoto['success']) {
            return $namaFoto;
        }

        $id_Baru = $this->generateId();

        $statement = $this->db->prepare(
            "INSERT INTO mst_event
                (mev_id_event, mev_nama_event, mev_tanggal_event, mev_waktu_mulai, mew_waktu_selesai, mev_lokasi, mev_kategori, mev_deskripsi, mev_foto, mev_status, mev_createBy)
            VALUES
                (:id, :nama, :tanggal, :waktuMulai, :waktuSelesai, :lokasi, :kategori, :deskripsi, :foto, :status, :createBy)"
        );

        $statement->execute([
            ':id'           => $id_Baru,
            ':nama'         => $data['namaEvent'],
            ':tanggal'      => $data['tanggalEvent'],
            ':waktuMulai'   => $data['waktuMulai'],
            ':waktuSelesai' => $data['waktuSelesai'],
            ':lokasi'       => $data['lokasi'],
            ':kategori'     => $data['kategori'],
            ':deskripsi'    => $data['deskripsi'] ?? null,
            ':foto'         => $namaFoto['nama_file'],
            ':status'       => $data['status'],
            ':createBy'     => $_SESSION['nama'] ?? 'System'
        ]);

        if ($statement->rowCount()) {
            return ['success' => true, 'message' => 'Event wisata berhasil ditambahkan!', 'id' => $id_Baru];
        }
        return ['success' => false, 'message' => 'Gagal menambahkan event wisata!'];
    }

    // Ambil semua event
    public function ambilSemua() {
        $stmt = $this->db->prepare("SELECT * FROM mst_event ORDER BY mev_createDate ASC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['success' => true, 'data' => $data];
    }

    public function cariEvent($keyword) {
        $keyword = trim($keyword);

        // Jika keyword kosong, kembalikan semua data
        if ($keyword === '') {
            return $this->ambilSemua();
        }

        $cari = '%' . $keyword . '%';

        $stmt = $this->db->prepare(
            "SELECT * FROM mst_event
             WHERE mev_nama_event LIKE :cari
                OR mev_lokasi     LIKE :cari2
                OR mev_kategori   LIKE :cari3
                OR mev_status     LIKE :cari4
             ORDER BY mev_nama_event ASC"
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

    public function ambilStatistik() {
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as total FROM mst_event");
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtAktif = $this->db->prepare("SELECT COUNT(*) as total FROM mst_event WHERE mev_status = 'Aktif'");
        $stmtAktif->execute();
        $aktif = $stmtAktif->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtSelesai = $this->db->prepare("SELECT COUNT(*) as total FROM mst_event WHERE mev_status = 'Selesai'");
        $stmtSelesai->execute();
        $selesai = $stmtSelesai->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtBatal = $this->db->prepare("SELECT COUNT(*) as total FROM mst_event WHERE mev_status = 'Batal'");
        $stmtBatal->execute();
        $batal = $stmtBatal->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'success' => true,
            'data'    => [
                'total'   => $total,
                'aktif'   => $aktif,
                'selesai' => $selesai,
                'batal'   => $batal
            ]
        ];
    }

    // Ambil event by ID
    public function ambilById($id) {
        $stmt = $this->db->prepare("SELECT * FROM mst_event WHERE mev_id_event = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return ['success' => true, 'data' => $data];
        }
        return ['success' => false, 'message' => 'Event tidak ditemukan!'];
    }

    // Edit event
    public function editEvent($id, $data, $foto = null) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Event tidak ditemukan!'];
        }

        $fotoFinal = $dataLama['data']['mev_foto'];

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $uploadBaru = $this->uploadFoto($foto);
            if (!$uploadBaru['success']) {
                return $uploadBaru;
            }
            $this->hapusFoto($fotoFinal);
            $fotoFinal = $uploadBaru['nama_file'];
        }

        $stmt = $this->db->prepare(
            "UPDATE mst_event SET
                mev_nama_event     = :nama,
                mev_tanggal_event  = :tanggal,
                mev_waktu_mulai    = :waktuMulai,
                mew_waktu_selesai  = :waktuSelesai,
                mev_lokasi         = :lokasi,
                mev_kategori       = :kategori,
                mev_deskripsi      = :deskripsi,
                mev_foto           = :foto,
                mev_status         = :status
            WHERE mev_id_event = :id"
        );

        $stmt->execute([
            ':nama'         => $data['namaEvent'],
            ':tanggal'      => $data['tanggalEvent'],
            ':waktuMulai'   => $data['waktuMulai'],
            ':waktuSelesai' => $data['waktuSelesai'],
            ':lokasi'       => $data['lokasi'],
            ':kategori'     => $data['kategori'],
            ':deskripsi'    => $data['deskripsi'] ?? null,
            ':foto'         => $fotoFinal,
            ':status'       => $data['status'],
            ':id'           => $id
        ]);

        if ($stmt->rowCount()) {
            return ['success' => true, 'message' => 'Event wisata berhasil diperbarui!'];
        }
        return ['success' => false, 'message' => 'Tidak ada perubahan data!'];
    }

    // Hapus event
    public function hapusEvent($id) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Event tidak ditemukan!'];
        }

        $stmt = $this->db->prepare("DELETE FROM mst_event WHERE mev_id_event = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount()) {
            $this->hapusFoto($dataLama['data']['mev_foto']);
            return ['success' => true, 'message' => 'Event wisata berhasil dihapus!'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus event wisata!'];
    }

    // Helper - generate ID format EW001, EW002, dst
    private function generateId() {
        $stmt = $this->db->prepare("SELECT mev_id_event FROM mst_event ORDER BY mev_id_event DESC LIMIT 1");
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $angka     = (int) substr($last['mev_id_event'], 2);
            $angkaBaru = $angka + 1;
        } else {
            $angkaBaru = 1;
        }

        return 'EW' . str_pad($angkaBaru, 3, '0', STR_PAD_LEFT);
    }

    // Helper - upload foto
    private function uploadFoto($foto) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize      = 2 * 1024 * 1024;
        $uploadDir    = '../../uploads/event/';

        if (!in_array($foto['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format foto tidak valid! Gunakan JPG atau PNG.'];
        }
        if ($foto['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran foto maksimal 2MB!'];
        }

        $ekstensi = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $namaFile = uniqid('event_') . '.' . $ekstensi;

        if (move_uploaded_file($foto['tmp_name'], $uploadDir . $namaFile)) {
            return ['success' => true, 'nama_file' => $namaFile];
        }
        return ['success' => false, 'message' => 'Gagal menyimpan foto ke server!'];
    }

    // Helper - hapus foto lama dari server
    private function hapusFoto($namaFile) {
        $path = '../../uploads/event/' . $namaFile;
        if ($namaFile && file_exists($path)) {
            unlink($path);
        }
    }
}

session_start();

$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Action tidak boleh kosong!']);
    exit;
}

$event  = new Event($pdo);
$result = [];

// DEV_MODE: tampilkan pesan error PHP/PDO asli ke response JSON saat development.
// Ubah ke false sebelum dipakai/deploy production, agar detail database tidak terekspos.
define('DEV_MODE', false);

try {

    switch ($action) {

        case 'tambah':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
                exit;
            }
            if (empty($_POST['namaEvent'])    || empty($_POST['tanggalEvent']) ||
                empty($_POST['waktuMulai'])   || empty($_POST['waktuSelesai']) ||
                empty($_POST['lokasi'])       || empty($_POST['kategori'])) {
                echo json_encode(['success' => false, 'message' => 'Field wajib tidak boleh kosong!']);
                exit;
            }
            $result = $event->tambahEvent($_POST, $_FILES['foto'] ?? null);
            break;

        case 'baca':
            $id     = $_GET['id'] ?? null;
            $result = $id ? $event->ambilById($id) : $event->ambilSemua();
            break;

        case 'cari':
            $keyword = trim($_GET['q'] ?? '');
            $result  = $event->cariEvent($keyword);
            break;

        case 'edit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
                exit;
            }
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID event tidak ditemukan!']);
                exit;
            }
            $result = $event->editEvent($id, $_POST, $_FILES['foto'] ?? null);
            break;

        case 'hapus':
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID event tidak ditemukan!']);
                exit;
            }
            $result = $event->hapusEvent($id);
            break;

        case 'statistik':
            $result = $event->ambilStatistik();
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
