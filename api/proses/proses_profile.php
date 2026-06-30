<?php
session_start();
// Pastikan path ke koneksi.php benar. 
// Jika file ini di api/proses/ maka naik 1 level ke api/
require_once '../koneksi.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['mus_id_users'];
    $nama = $_POST['mus_name'];
    $email = $_POST['mus_email'];
    $pass = $_POST['mus_password'];

    // --- Ganti $koneksi menjadi $pdo di sini ---
    $sql = "UPDATE mst_users SET mus_name = ?, mus_email = ? WHERE mus_id_users = ?";
    $stmt = $pdo->prepare($sql); // <--- UBAH DI SINI
    $stmt->execute([$nama, $email, $id]);

    if (!empty($pass)) {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $sql_pass = "UPDATE mst_users SET mus_password = ? WHERE mus_id_users = ?";
        $stmt_pass = $pdo->prepare($sql_pass); // <--- UBAH DI SINI
        $stmt_pass->execute([$hashed, $id]);
    }

    $_SESSION['mus_name'] = $nama;
    $_SESSION['mus_email'] = $email;

    echo "<script>alert('Profil berhasil diupdate!'); window.location.href='../php/profile.php';</script>";
}
?>