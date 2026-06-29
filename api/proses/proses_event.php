<?php
// 1. Session start harus di paling atas agar $_SESSION bisa diakses di dalam class
session_start();

header('Content-Type: application/json');

require_once '../koneksi.php';
require_once '../config_cloudinary.php';

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

        try {
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
                ':status'       => $data['status'] ?? 'Aktif',
                ':createBy'     => $_SESSION['nama'] ?? 'System'
            ]);
        } catch (Throwable $e) {
            $this->hapusFoto($namaFoto['nama_file']);
            throw $e;
        }

        return ['success' => true, 'message' => 'Event wisata berhasil ditambahkan!', 'id' => $id_Baru];
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

        $fotoLama         = $dataLama['data']['mev_foto'];
        $fotoFinal        = $fotoLama;
        $fotoBaruDiupload = false;

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $uploadBaru = $this->uploadFoto($foto);
            if (!$uploadBaru['success']) {
                return $uploadBaru;
            }
            $fotoFinal        = $uploadBaru['nama_file'];
            $fotoBaruDiupload = true;
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

        try {
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
        } catch (Throwable $e) {
            if ($fotoBaruDiupload) {
                $this->hapusFoto($fotoFinal);
            }
            throw $e;
        }

        if ($fotoBaruDiupload) {
            $this->hapusFoto($fotoLama);
        }

        return ['success' => true, 'message' => 'Event wisata berhasil diperbarui!'];
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

    // Helper - upload foto ke Cloudinary
    private function uploadFoto($foto) {
        if (!$foto || !isset($foto['tmp_name']) || $foto['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Foto event harus diupload!'];
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize      = 2 * 1024 * 1024;

        if (!in_array($foto['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format foto tidak valid! Gunakan JPG atau PNG.'];
        }
        if ($foto['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran foto maksimal 2MB!'];
        }

        $folder    = 'jelajah_wisata/event';
        $publicId  = preg_replace('/[^a-zA-Z0-9_]/', '', uniqid('event_', true));
        $timestamp = time();

        $paramsToSign = ['folder' => $folder, 'public_id' => $publicId, 'timestamp' => $timestamp];
        ksort($paramsToSign);
        $signatureString = '';
        foreach ($paramsToSign as $key => $value) {
            $signatureString .= $key . '=' . $value . '&';
        }
        $signatureString = rtrim($signatureString, '&') . CLOUDINARY_API_SECRET;
        $signature = sha1($signatureString);

        $url = 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/upload';

        $postFields = [
            'file'      => new CURLFile($foto['tmp_name'], $foto['type'], $foto['name']),
            'api_key'   => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'folder'    => $folder,
            'public_id' => $publicId,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // PERBAIKAN: Diubah ke false agar tidak error SSL saat testing di komputer lokal (XAMPP)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        
        // PERBAIKAN PHP 8.5: Menghapus curl_close($ch) karena sudah otomatis/deprecated

        if ($curlErr) {
            return ['success' => false, 'message' => 'Gagal koneksi ke Cloudinary: ' . $curlErr];
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200 || empty($result['secure_url'])) {
            $msg = $result['error']['message'] ?? 'Upload ke Cloudinary gagal!';
            return ['success' => false, 'message' => $msg];
        }

        return [
            'success'   => true,
            'nama_file' => $result['secure_url'],
            'public_id' => $result['public_id']
        ];
    }

    // Helper - hapus foto dari Cloudinary
    private function hapusFoto($urlFoto) {
        if (!$urlFoto) return;

        $pattern = '#/upload/(?:v\d+/)?(.+)\.[a-zA-Z0-9]+$#';
        if (!preg_match($pattern, $urlFoto, $matches)) {
            return;
        }
        $publicId = $matches[1];

        $timestamp    = time();
        $paramsToSign = ['public_id' => $publicId, 'timestamp' => $timestamp];
        ksort($paramsToSign);
        $signatureString = '';
        foreach ($paramsToSign as $key => $value) {
            $signatureString .= $key . '=' . $value . '&';
        }
        $signatureString = rtrim($signatureString, '&') . CLOUDINARY_API_SECRET;
        $signature = sha1($signatureString);

        $url = 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/destroy';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'public_id' => $publicId,
            'api_key'   => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // PERBAIKAN: Diubah ke false juga di fungsi hapus agar sinkron di lokal
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
        curl_exec($ch);
        // PERBAIKAN PHP 8.5: Menghapus curl_close($ch)
    }
}

$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Action tidak boleh kosong!']);
    exit;
}

$event  = new Event($pdo);
$result = [];

define('DEV_MODE', true); 

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