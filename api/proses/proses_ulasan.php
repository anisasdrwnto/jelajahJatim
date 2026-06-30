<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

header('Content-Type: application/json');

require_once '../koneksi.php';
require_once '../config_cloudinary.php';

class Ulasan {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function tambahUlasan($data, $foto) {
        $userId      = $data['user_id'] ?? null;
        $destinasiId = trim($data['destinasi_id'] ?? '');
        $kategori    = trim($data['kategori_ulasan'] ?? '');
        $rating      = (int) ($data['rating'] ?? 0);
        $komentar    = trim($data['isi_ulasan'] ?? '');

        if (!$userId) {
            return ['success' => false, 'message' => 'Anda harus login untuk menulis ulasan.'];
        }
        if (!$destinasiId || !$kategori || $rating < 1 || $rating > 5 || $komentar === '') {
            return ['success' => false, 'message' => 'Lengkapi semua field ulasan (kategori, rating, dan isi ulasan).'];
        }

        $namaFoto = null;
        if ($foto && isset($foto['tmp_name']) && $foto['error'] === UPLOAD_ERR_OK) {
            $uploadHasil = $this->uploadFoto($foto);
            if (!$uploadHasil['success']) {
                return $uploadHasil;
            }
            $namaFoto = $uploadHasil['nama_file'];
        }

        $idBaru = $this->generateId();

        $stmt = $this->db->prepare(
            "INSERT INTO `mst_ulasan` 
                (mul_id_ulasan, mul_id_destinasi, mul_id_user, mul_kategori, mul_rating, mul_komentar, mul_foto, mul_status) 
            VALUES 
                (:id, :idDestinasi, :idUser, :kategori, :rating, :komentar, :foto, 'Tampil')"
        );

        $stmt->bindValue(':id', $idBaru, PDO::PARAM_STR);
        $stmt->bindValue(':idDestinasi', $destinasiId, PDO::PARAM_STR);
        $stmt->bindValue(':idUser', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':kategori', $kategori, PDO::PARAM_STR);
        $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindValue(':komentar', $komentar, PDO::PARAM_STR);
        $stmt->bindValue(':foto', $namaFoto, $namaFoto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        try {
            $stmt->execute();
        } catch (Throwable $e) {
            if ($namaFoto) { $this->hapusFoto($namaFoto); }
            throw $e;
        }

        return ['success' => true, 'message' => 'Ulasan berhasil dikirim, terima kasih!', 'id' => $idBaru];
    }

    public function ambilUlasanPublik($destinasiId) {
        $stmt = $this->db->prepare(
            "SELECT u.*, us.mus_name AS nama_user 
             FROM `mst_ulasan` u 
             LEFT JOIN `mst_users` us ON u.mul_id_user = us.mus_id_users 
             WHERE u.mul_id_destinasi = :id AND LOWER(u.mul_status) = 'tampil' 
             ORDER BY u.mul_createDate DESC"
        );
        $stmt->execute([':id' => $destinasiId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalUlasan = count($data);
        $rataRating  = $totalUlasan > 0 ? round(array_sum(array_column($data, 'mul_rating')) / $totalUlasan, 1) : 0;

        return [
            'success' => true,
            'data'    => $data,
            'total'   => $totalUlasan,
            'rata_rating' => $rataRating
        ];
    }

    public function ambilSemua() {
        $stmt = $this->db->prepare(
            "SELECT u.*, d.mdw_nama_destinasi_wisata, us.mus_name AS nama_user 
             FROM `mst_ulasan` u 
             LEFT JOIN `mst_destinasi_wisata` d ON u.mul_id_destinasi = d.mdw_id_destinasi_wisata 
             LEFT JOIN `mst_users` us ON u.mul_id_user = us.mus_id_users 
             ORDER BY u.mul_id_ulasan DESC"
        );
        $stmt->execute();
        return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function cariUlasan($keyword) {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->ambilSemua();
        }

        $cari = '%' . $keyword . '%';
        $stmt = $this->db->prepare(
            "SELECT u.*, d.mdw_nama_destinasi_wisata, us.mus_name AS nama_user 
             FROM `mst_ulasan` u 
             LEFT JOIN `mst_destinasi_wisata` d ON u.mul_id_destinasi = d.mdw_id_destinasi_wisata 
             LEFT JOIN `mst_users` us ON u.mul_id_user = us.mus_id_users 
             WHERE d.mdw_nama_destinasi_wisata LIKE :cari 
                OR us.mus_name                 LIKE :cari2 
                OR u.mul_kategori              LIKE :cari3 
                OR u.mul_komentar              LIKE :cari4 
             ORDER BY u.mul_id_ulasan DESC"
        );
        $stmt->bindValue(':cari',  $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari2', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari3', $cari, PDO::PARAM_STR);
        $stmt->bindValue(':cari4', $cari, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['success' => true, 'total' => count($data), 'data' => $data];
    }

    public function ambilStatistik() {
        // Total Ulasan
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as total FROM `mst_ulasan`");
        $stmtTotal->execute();
        $total = (int) $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // Tampil (Gunakan LOWER untuk TiDB)
        $stmtTampil = $this->db->prepare("SELECT COUNT(*) as total FROM `mst_ulasan` WHERE LOWER(mul_status) = 'tampil'");
        $stmtTampil->execute();
        $tampil = (int) $stmtTampil->fetch(PDO::FETCH_ASSOC)['total'];

        // Sembunyi (Apapun yang tidak berstatus tampil)
        $stmtSembunyi = $this->db->prepare("SELECT COUNT(*) as total FROM `mst_ulasan` WHERE LOWER(mul_status) != 'tampil'");
        $stmtSembunyi->execute();
        $sembunyi = (int) $stmtSembunyi->fetch(PDO::FETCH_ASSOC)['total'];

        // Rata-rata Rating
        $stmtRating = $this->db->prepare("SELECT COALESCE(AVG(mul_rating), 0) as rata FROM `mst_ulasan`");
        $stmtRating->execute();
        $rataRating = round((float) $stmtRating->fetch(PDO::FETCH_ASSOC)['rata'], 1);

        return [
            'success' => true,
            'data' => [
                'total_ulasan'        => $total,
                'total_tampil'        => $tampil,
                'total_disembunyikan' => $sembunyi,
                'rata_rating'         => $rataRating
            ]
        ];
    }

    public function ubahStatus($id, $statusBaru) {
        if (!in_array($statusBaru, ['Tampil', 'Sembunyi'], true)) {
            return ['success' => false, 'message' => 'Status tidak valid!'];
        }

        $stmt = $this->db->prepare("UPDATE `mst_ulasan` SET mul_status = :status WHERE mul_id_ulasan = :id");
        $stmt->execute([':status' => $statusBaru, ':id' => $id]);

        if ($stmt->rowCount() > 0) {
            $pesan = $statusBaru === 'Tampil' ? 'Ulasan berhasil ditampilkan!' : 'Ulasan berhasil disembunyikan!';
            return ['success' => true, 'message' => $pesan];
        }
        return ['success' => false, 'message' => 'Gagal memperbarui status atau status sama!'];
    }

    public function hapusUlasan($id) {
        $stmt = $this->db->prepare("SELECT mul_foto FROM `mst_ulasan` WHERE mul_id_ulasan = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['success' => false, 'message' => 'Ulasan tidak ditemukan!'];
        }

        $stmt = $this->db->prepare("DELETE FROM `mst_ulasan` WHERE mul_id_ulasan = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            if (!empty($row['mul_foto'])) { $this->hapusFoto($row['mul_foto']); }
            return ['success' => true, 'message' => 'Ulasan berhasil dihapus!'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus ulasan!'];
    }

    private function generateId() {
        $stmt = $this->db->prepare("SELECT mul_id_ulasan FROM `mst_ulasan` ORDER BY mul_id_ulasan DESC LIMIT 1");
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        $angkaBaru = $last ? ((int) substr($last['mul_id_ulasan'], 2)) + 1 : 1;
        return 'UL' . str_pad($angkaBaru, 3, '0', STR_PAD_LEFT);
    }

    private function uploadFoto($foto) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize      = 2 * 1024 * 1024;

        if (!in_array($foto['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format foto tidak valid! Gunakan JPG atau PNG.'];
        }
        if ($foto['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran foto maksimal 2MB!'];
        }

        $folder    = 'jelajah_wisata/ulasan';
        $publicId  = preg_replace('/[^a-zA-Z0-9_]/', '', uniqid('ul_', true));
        $timestamp = time();

        $paramsToSign = ['folder' => $folder, 'public_id' => $publicId, 'timestamp' => $timestamp];
        ksort($paramsToSign);
        $signatureString = '';
        foreach ($paramsToSign as $key => $value) { $signatureString .= $key . '=' . $value . '&'; }
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);

        if ($curlErr) { return ['success' => false, 'message' => 'Gagal koneksi ke Cloudinary: ' . $curlErr]; }

        $result = json_decode($response, true);
        if ($httpCode !== 200 || empty($result['secure_url'])) {
            $msg = $result['error']['message'] ?? 'Upload foto ulasan gagal!';
            return ['success' => false, 'message' => $msg];
        }

        return ['success' => true, 'nama_file' => $result['secure_url'], 'public_id' => $result['public_id']];
    }

    private function hapusFoto($urlFoto) {
        if (!$urlFoto) return;
        $pattern = '#/upload/(?:v\d+/)?(.+)\.[a-zA-Z0-9]+$#';
        if (!preg_match($pattern, $urlFoto, $matches)) { return; }
        $publicId = $matches[1];

        $timestamp    = time();
        $paramsToSign = ['public_id' => $publicId, 'timestamp' => $timestamp];
        ksort($paramsToSign);
        $signatureString = '';
        foreach ($paramsToSign as $key => $value) { $signatureString .= $key . '=' . $value . '&'; }
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
    }
}

session_start();
$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Action tidak boleh kosong!']);
    exit;
}

$ulasan = new Ulasan($pdo);
$result = [];
define('DEV_MODE', true);

try {
    switch ($action) {
        case 'tambah':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success' => false, 'message' => 'Method tidak valid!']); exit; }
            if (!isset($_SESSION['user_id'])) { echo json_encode(['success' => false, 'message' => 'Anda harus login untuk menulis ulasan.']); exit; }
            $payload = $_POST;
            $payload['user_id'] = $_SESSION['user_id'];
            $result = $ulasan->tambahUlasan($payload, $_FILES['foto'] ?? null);
            break;
        case 'baca_publik':
            $destinasiId = $_GET['destinasi_id'] ?? '';
            if (empty($destinasiId)) { echo json_encode(['success' => false, 'message' => 'ID destinasi tidak ditemukan!']); exit; }
            $result = $ulasan->ambilUlasanPublik($destinasiId);
            break;
        case 'baca':
            $result = $ulasan->ambilSemua();
            break;
        case 'cari':
            $keyword = trim($_GET['q'] ?? '');
            $result  = $ulasan->cariUlasan($keyword);
            break;
        case 'statistik':
            $result = $ulasan->ambilStatistik();
            break;
        case 'ubahStatus':
            $id     = trim($_POST['id'] ?? '');
            $status = trim($_POST['status'] ?? '');
            if (empty($id)) { echo json_encode(['success' => false, 'message' => 'ID ulasan tidak ditemukan!']); exit; }
            $result = $ulasan->ubahStatus($id, $status);
            break;
        case 'hapus':
            $id = trim($_POST['id'] ?? '');
            if (empty($id)) { echo json_encode(['success' => false, 'message' => 'ID ulasan tidak ditemukan!']); exit; }
            $result = $ulasan->hapusUlasan($id);
            break;
        default:
            $result = ['success' => false, 'message' => 'Action tidak dikenal!'];
            break;
    }
} catch (PDOException $e) {
    $result = ['success' => false, 'message' => DEV_MODE ? 'Database error: ' . $e->getMessage() : 'Terjadi kesalahan pada database.'];
} catch (Throwable $e) {
    $result = ['success' => false, 'message' => DEV_MODE ? 'Server error: ' . $e->getMessage() : 'Terjadi kesalahan pada server.'];
}

echo json_encode($result);
?>