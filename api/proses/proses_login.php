<?php
session_start();
header('Content-Type: application/json');

// Pastikan path koneksi.php benar (naik 1 folder ke folder 'api/')
require_once '../koneksi.php'; 

class User {
    private $db;
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function cekLogin($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM mst_users WHERE mus_email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password
        if ($user && password_verify($password, $user['mus_password'])) {
            return [
                'success' => true,
                'data'    => $user // Mengembalikan seluruh data user
            ];
        }
        return ['success' => false, 'message' => 'Email atau password salah!'];
    }
}

// Validasi Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi!']);
    exit;
}

$user_obj = new User($pdo); // Pastikan $pdo sudah terdefinisi di koneksi.php
$result   = $user_obj->cekLogin($email, $password);

// Jika sukses, isi data ke session
if ($result['success']) {
    $data = $result['data'];
    
    $_SESSION['mus_id_users'] = $data['mus_id_users'];
    $_SESSION['mus_name']     = $data['mus_name'];
    $_SESSION['mus_email']    = $data['mus_email'];
    $_SESSION['role']         = $data['mus_role'];
    
    echo json_encode(['success' => true, 'message' => 'Login berhasil']);
} else {
    echo json_encode($result);
}
?>