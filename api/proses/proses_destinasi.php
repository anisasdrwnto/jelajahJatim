<?php

header('Content-Type: application/json');

require_once '../koneksi.php';
require_once '../config_cloudinary.php';

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

        try {
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
        } catch (Throwable $e) {
            // INSERT gagal -> hapus foto yang sudah keburu diupload ke Cloudinary
            $this->hapusFoto($namaFoto['nama_file']);
            throw $e;
        }

        return ['success' => true, 'message' => 'Destinasi wisata berhasil ditambahkan!', 'id' => $id_Baru];
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

        $fotoLama         = $dataLama['data']['mdw_foto'];
        $fotoFinal        = $fotoLama;
        $fotoBaruDiupload = false;

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $uploadBaru = $this->uploadFoto($foto);
            if (!$uploadBaru['success']) {
                return $uploadBaru;
            }
            $fotoFinal        = $uploadBaru['nama_file'];
            $fotoBaruDiupload = true;
            // Foto lama BELUM dihapus -> baru dihapus setelah UPDATE berhasil
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

        try {
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
        } catch (Throwable $e) {
            if ($fotoBaruDiupload) {
                $this->hapusFoto($fotoFinal);
            }
            throw $e;
        }

        if ($fotoBaruDiupload) {
            $this->hapusFoto($fotoLama);
        }

        return ['success' => true, 'message' => 'Destinasi wisata berhasil diperbarui!'];
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

    // Helper - upload foto ke Cloudinary
    private function uploadFoto($foto) {
        if (!$foto || !isset($foto['tmp_name']) || $foto['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Foto destinasi harus diupload!'];
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize      = 2 * 1024 * 1024;

        if (!in_array($foto['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format foto tidak valid! Gunakan JPG atau PNG.'];
        }
        if ($foto['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran foto maksimal 2MB!'];
        }

        $folder    = 'jelajah_wisata/destinasi'; 
        $publicId  = preg_replace('/[^a-zA-Z0-9_]/', '', uniqid('des_', true));
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
        
        // MENGABAIKAN VERIFIKASI SSL AGAR LANCAR DI LOCALHOST MANAPUN
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ditambahkan juga di proses hapus foto
        curl_exec($ch);
        curl_close($ch);
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

// MODUS DEVELOPMENT AKTIF UNTUK MELIHAT ERROR JIKA ADA FITUR PHP LAPTOP YANG MATI
define('DEV_MODE', true);

try {

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