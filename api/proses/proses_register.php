<?php 

header('Content-Type: application/json');

require_once '../koneksi.php';

class User{
    private $db;

    public function __construct($pdo){
        $this->db = $pdo;
    }

    public function cekEmail($email){
        $stmt = $this->db->prepare("SELECT mus_email FROM mst_users WHERE mus_email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generateId() {
        $stmt = $this->db->query("SELECT mus_id_users FROM mst_users ORDER BY mus_id_users DESC LIMIT 1");
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $angka = (int) substr($last['mus_id_users'], 3); // ambil angka setelah "USR"
            $angka++;
        } else {
            $angka = 1; // user pertama
        }

        return 'USR' . str_pad($angka, 3, '0', STR_PAD_LEFT); // USR001, USR002, dst
    }

    public function register($nama, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $newId          = $this->generateId();

        $stmt = $this->db->prepare("INSERT INTO mst_users (mus_id_users, mus_name, mus_email, mus_password, mus_role) 
                                    VALUES (:id, :nama, :email, :password, 'USR')");
        $stmt->execute([
            ':id'       => $newId,
            ':nama'     => $nama,
            ':email'    => $email,
            ':password' => $hashedPassword
        ]);

        return $newId;
    }
}


//jika method bukan POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$nama            = trim($_POST['nama'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['confirm_password']?? '');

if(empty($nama) || empty($email) || empty($password) || empty($confirmPassword)){
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi!']);
    exit;   
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid!']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password minimal 8 karakter!']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok!']);
    exit;
}

$user = new User($pdo);

if($user->cekEmail($email)){
    echo json_encode(['success' => false, 'message' => 'Email sudah terpakai']);
    exit;
}

$userId = $user->register($nama, $email, $password);

if($userId){
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['nama']    = $nama;
    $_SESSION['role']    = 'USR';
    echo json_encode(true);
}else{
    echo json_encode(['success' => false, 'message' => 'Gagal membuat akun!']);
}

?>