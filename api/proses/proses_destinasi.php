<?php

header('Content-Type: application/json');

require_once '../koneksi.php';

class Destinasi {
    private $db;

    public function __construct($pdo) { 
        $this->db = $pdo;
    }

    // Tambah destinasi
    public function tambahDestinasi($data, $foto) {
        $namaFoto = $this->uploadFoto($foto); 
        if (!$namaFoto['success']) {
            return $namaFoto;
        }

        $id_Baru = $this->generateId();

        $statement = $this->db->prepare(
            "INSERT INTO mst_destinasi_wisata 
                (mdw_id_destinasi_wisata, mdw_nama_destinasi_wisata, mdw_kabupaten_kota, mdw_wilayah, mdw_kategori, mdw_alamat_lengkap, mdw_deskripsi, mdw_status, mdw_foto, mdw_createBy)
            VALUES 
                (:id, :nama, :kabkota, :wilayah, :kategori, :alamat, :deskripsi, :status, :foto, :createBy)"
        );

        $statement->execute([
            ':id'        => $id_Baru,
            ':nama'      => $data['namaDestinasi'],
            ':kabkota'   => $data['kabupatenKota'],
            ':wilayah'   => $data['wilayah']   ?? null,
            ':kategori'  => $data['kategori'],
            ':alamat'    => $data['alamatLengkap'],
            ':deskripsi' => $data['deskripsi'] ?? null,
            ':status'    => $data['status'],
            ':foto'      => $namaFoto['nama_file'],
            ':createBy'  => $_SESSION['nama']  ?? 'System'
        ]);

        if ($statement->rowCount()) {
            return ['success' => true, 'message' => 'Destinasi wisata berhasil ditambahkan!', 'id' => $id_Baru];
        }
        return ['success' => false, 'message' => 'Gagal menambahkan destinasi wisata!'];
    }

    // Ambil semua destinasi
    public function ambilSemua() {
        $stmt = $this->db->prepare("SELECT * FROM mst_destinasi_wisata ORDER BY mdw_createDate ASC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['success' => true, 'data' => $data];
    }

    public function cariDestinasi($keyword) {
        $keyword = trim($keyword);

        // Jika keyword kosong, kembalikan semua data
        if ($keyword === '') {
            return $this->ambilSemua();
        }

        $cari = '%' . $keyword . '%';

        $stmt = $this->db->prepare(
            "SELECT * FROM mst_destinasi_wisata
             WHERE mdw_nama_destinasi_wisata LIKE :cari
                OR mdw_kabupaten_kota        LIKE :cari2
                OR mdw_kategori              LIKE :cari3
                OR mdw_wilayah               LIKE :cari4
                OR mdw_alamat_lengkap        LIKE :cari5
             ORDER BY mdw_nama_destinasi_wisata ASC"
        );

        $stmt->bindValue(':cari',  $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari2', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari3', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari4', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari5', $cari, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'total'   => count($data),
            'data'    => $data,
        ];
    }

    public function ambilStatistik() {
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as total FROM mst_destinasi_wisata");
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtAktif = $this->db->prepare("SELECT COUNT(*) as total FROM mst_destinasi_wisata WHERE mdw_status = 'Aktif'");
        $stmtAktif->execute();
        $aktif = $stmtAktif->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtNonaktif = $this->db->prepare("SELECT COUNT(*) as total FROM mst_destinasi_wisata WHERE mdw_status = 'Nonaktif'");
        $stmtNonaktif->execute();
        $nonaktif = $stmtNonaktif->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtKategori = $this->db->prepare("SELECT COUNT(DISTINCT mdw_kategori) as total FROM mst_destinasi_wisata");
        $stmtKategori->execute();
        $totalKategori = $stmtKategori->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'success' => true,
            'data'    => [
                'total'          => $total,
                'aktif'          => $aktif,
                'nonaktif'       => $nonaktif,
                'total_kategori' => $totalKategori
            ]
        ];
    }

    // Ambil destinasi by ID
    public function ambilById($id) {
        $stmt = $this->db->prepare("SELECT * FROM mst_destinasi_wisata WHERE mdw_id_destinasi_wisata = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return ['success' => true, 'data' => $data];
        }
        return ['success' => false, 'message' => 'Destinasi tidak ditemukan!'];
    }

    // Edit destinasi
    public function editDestinasi($id, $data, $foto = null) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Destinasi tidak ditemukan!'];
        }

        $fotoFinal = $dataLama['data']['mdw_foto'];

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $uploadBaru = $this->uploadFoto($foto);
            if (!$uploadBaru['success']) {
                return $uploadBaru;
            }
            $this->hapusFoto($fotoFinal);
            $fotoFinal = $uploadBaru['nama_file'];
        }

        $stmt = $this->db->prepare(
            "UPDATE mst_destinasi_wisata SET
                mdw_nama_destinasi_wisata = :nama,
                mdw_kabupaten_kota        = :kabkota,
                mdw_wilayah               = :wilayah,
                mdw_kategori              = :kategori,
                mdw_alamat_lengkap        = :alamat,
                mdw_deskripsi             = :deskripsi,
                mdw_status                = :status,
                mdw_foto                  = :foto
            WHERE mdw_id_destinasi_wisata = :id"
        );

        $stmt->execute([
            ':nama'      => $data['namaDestinasi'],
            ':kabkota'   => $data['kabupatenKota'],
            ':wilayah'   => $data['wilayah']   ?? null,
            ':kategori'  => $data['kategori'],
            ':alamat'    => $data['alamatLengkap'],
            ':deskripsi' => $data['deskripsi'] ?? null,
            ':status'    => $data['status'],
            ':foto'      => $fotoFinal,
            ':id'        => $id
        ]);

        if ($stmt->rowCount()) {
            return ['success' => true, 'message' => 'Destinasi wisata berhasil diperbarui!'];
        }
        return ['success' => false, 'message' => 'Tidak ada perubahan data!'];
    }

    // Hapus destinasi
    public function hapusDestinasi($id) {
        $dataLama = $this->ambilById($id);
        if (!$dataLama['success']) {
            return ['success' => false, 'message' => 'Destinasi tidak ditemukan!'];
        }

        $stmt = $this->db->prepare("DELETE FROM mst_destinasi_wisata WHERE mdw_id_destinasi_wisata = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount()) {
            $this->hapusFoto($dataLama['data']['mdw_foto']);
            return ['success' => true, 'message' => 'Destinasi wisata berhasil dihapus!'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus destinasi wisata!'];
    }

    // Helper - generate ID format DW001, DW002, dst
    private function generateId() {
        $stmt = $this->db->prepare("SELECT mdw_id_destinasi_wisata FROM mst_destinasi_wisata ORDER BY mdw_id_destinasi_wisata DESC LIMIT 1");
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $angka     = (int) substr($last['mdw_id_destinasi_wisata'], 2);
            $angkaBaru = $angka + 1;
        } else {
            $angkaBaru = 1;
        }

        return 'DW' . str_pad($angkaBaru, 3, '0', STR_PAD_LEFT);
    }

    // Helper - upload foto
    private function uploadFoto($foto) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize      = 2 * 1024 * 1024;
        $uploadDir    = '../../uploads/destinasi/';

        if (!in_array($foto['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format foto tidak valid! Gunakan JPG atau PNG.'];
        }
        if ($foto['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran foto maksimal 2MB!'];
        }

        $ekstensi = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $namaFile = uniqid('des_') . '.' . $ekstensi;

        if (move_uploaded_file($foto['tmp_name'], $uploadDir . $namaFile)) {
            return ['success' => true, 'nama_file' => $namaFile];
        }
        return ['success' => false, 'message' => 'Gagal menyimpan foto ke server!'];
    }

    // Helper - hapus foto lama dari server
    private function hapusFoto($namaFile) {
        $path = '../../uploads/destinasi/' . $namaFile;
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

$destinasi = new Destinasi($pdo);
$result    = [];

switch ($action) {

    case 'tambah':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
            exit;
        }
        if (empty($_POST['namaDestinasi']) || empty($_POST['kabupatenKota']) ||
            empty($_POST['kategori'])      || empty($_POST['alamatLengkap'])) {
            echo json_encode(['success' => false, 'message' => 'Field wajib tidak boleh kosong!']);
            exit;
        }
        $result = $destinasi->tambahDestinasi($_POST, $_FILES['foto'] ?? null);
        break;

    case 'baca':
        $id     = $_GET['id'] ?? null;
        $result = $id ? $destinasi->ambilById($id) : $destinasi->ambilSemua();
        break;

    case 'cari':
        $keyword = trim($_GET['q'] ?? '');
        $result  = $destinasi->cariDestinasi($keyword);
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak valid!']);
            exit;
        }
        $id = trim($_POST['id'] ?? '');
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID destinasi tidak ditemukan!']);
            exit;
        }
        $result = $destinasi->editDestinasi($id, $_POST, $_FILES['foto'] ?? null);
        break;

    case 'hapus':
        $id = trim($_POST['id'] ?? '');
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID destinasi tidak ditemukan!']);
            exit;
        }
        $result = $destinasi->hapusDestinasi($id);
        break;

    case 'statistik':
        $result = $destinasi->ambilStatistik();
        break;

    default:
        $result = ['success' => false, 'message' => 'Action tidak dikenal!'];
        break;
}

echo json_encode($result);
?>