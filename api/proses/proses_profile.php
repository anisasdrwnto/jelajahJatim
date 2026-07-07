<?php
session_start();

// Pastikan path ke koneksi.php benar.
// Jika file ini di api/proses/ maka naik 1 level ke api/
require_once '../koneksi.php';

class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Update nama & email
    public function updateProfil($id, $nama, $email) {
        $stmt = $this->db->prepare(
            "UPDATE mst_users SET mus_name = :nama, mus_email = :email WHERE mus_id_users = :id"
        );
        return $stmt->execute([
            ':nama'  => $nama,
            ':email' => $email,
            ':id'    => $id,
        ]);
    }

    // Update password (terpisah, hanya jalan kalau password diisi)
    public function updatePassword($id, $passwordBaru) {
        $hashed = password_hash($passwordBaru, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "UPDATE mst_users SET mus_password = :password WHERE mus_id_users = :id"
        );
        return $stmt->execute([
            ':password' => $hashed,
            ':id'       => $id,
        ]);
    }

    // Cek apakah email sudah dipakai user lain (selain dirinya sendiri)
    public function cekEmailDipakai($email, $idSekarang) {
        $stmt = $this->db->prepare(
            "SELECT mus_id_users FROM mst_users WHERE mus_email = :email AND mus_id_users != :id LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':id' => $idSekarang]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}

// ================= PROSES REQUEST =================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit; // atau redirect balik ke profile.php kalau mau
}

if (!isset($_SESSION['mus_id_users'])) {
    echo "<script>alert('Sesi login tidak ditemukan, silakan login ulang.'); window.location.href='../php/login.php';</script>";
    exit;
}

$id    = $_SESSION['mus_id_users'];
$nama  = trim($_POST['mus_name']  ?? '');
$email = trim($_POST['mus_email'] ?? '');
$pass  = trim($_POST['mus_password'] ?? '');

// Validasi dasar
if (empty($nama) || empty($email)) {
    echo "<script>alert('Nama dan email tidak boleh kosong!'); window.location.href='../php/profile.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Format email tidak valid!'); window.location.href='../php/profile.php';</script>";
    exit;
}

$user = new User($pdo);

// Cek email tidak bentrok dengan user lain
if ($user->cekEmailDipakai($email, $id)) {
    echo "<script>alert('Email sudah dipakai akun lain!'); window.location.href='../php/profile.php';</script>";
    exit;
}

// Update nama & email
$user->updateProfil($id, $nama, $email);

// Update password hanya jika diisi (dan validasi panjang minimal)
if (!empty($pass)) {
    if (strlen($pass) < 8) {
        echo "<script>alert('Password minimal 8 karakter! Nama & email tersimpan, password tidak diubah.'); window.location.href='../php/profile.php';</script>";
        exit;
    }
    $user->updatePassword($id, $pass);
}

// Update session supaya tampilan langsung sinkron tanpa perlu login ulang
$_SESSION['mus_name']  = $nama;
$_SESSION['mus_email'] = $email;

echo "<script>alert('Profil berhasil diupdate!'); window.location.href='../php/profile.php';</script>";
?>