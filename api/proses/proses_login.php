<?php

header('Content-Type: application/json');

//proses login

require_once '../koneksi.php';

//Bikin class User
class User{
    private $db; //properti untuk menyimpan koneksi PDO

    public function __construct($pdo){
        $this->db = $pdo; //simpan koneksi PDO ke properti DB
    }

    //Buat fungsi untuk cek login
    public function cekLogin($email, $password){
        //Deklarasi variabel statement
        $stmt = $this->db->prepare("SELECT * FROM mst_users WHERE mus_email = :email LIMIT 1"); //ambil email dari tabel mst_users yang maksimal 1 baris
        $stmt->execute([':email' => $email]); //eksekusi statement dengan parameter email
        $user = $stmt->fetch(PDO::FETCH_ASSOC); //ambil hasil eksekusi dalam bentuk array asosiatif(key-value)

        if($user && password_verify($password, $user['mus_password'])){ //jika user ditemukan dengan password yang sesuai
            return [                             //kembalikan hasil sukses berdasarkan nama dan role user
                'success' => true, 
                'nama'    => $user['mus_name'],  //ambil nama user dari tabel mst_users
                'role'    => $user['mus_role'] 
            ];
        }
        return ['success' => false, 'message' => 'Email atau password salah!']; //kembalikan hasil gagal dengan pesan error
    }
}

//handling AJAX request

//Cek apakah method yang digunakan POST atau bukan
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

//Ambil data dari request POST di login.js
$email    = trim($_POST['email'] ?? ''); //trim untuk menghapus spasi di awal dan akhir
$password = trim($_POST['password'] ?? '');


//jika email atau password kosong, kembalikan pesan error
if(empty($email) || empty($password)){
    echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi!']);
    exit;
}

//Buat objek User dan panggil fungsi cekLogin untuk memeriksa email dan password
$user   = new User($pdo);
$result = $user->cekLogin($email, $password); //hasil cekLogin akan mengembalikan array dengan key success login berhasil atau gagal

//Jika hasilnya success,buat session untuk menyimpan sesi login
if($result['success']){
    session_start();
    $_SESSION['user'] = $result; 
}
//Lempar hasil cekLogin dalam bentuk JSON ke login.js
echo json_encode($result);
?>