<?php 
// Buat koneksi ke database TIDB
$host     = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$username = "3ZRN5c6PUXtTqvf.root";
$password = "mWYBDrZ8ogUT5beM";
$database = "jelajahjatim";
$port     = 4000;

// Pastikan file isrgrootx1.pem berada di posisi yang benar
$cert_path = dirname(__DIR__) . '/isrgrootx1.pem';

/**
 * PENDETEKSI OTOMATIS VERSI PHP (Fitur Lintas Laptop)
 * Jika PHP 8.5+ (Laptop Kamu), gunakan Pdo\Mysql::ATTR_SSL_CA secara dinamis.
 * Jika PHP 8.3 ke bawah (Laptop Temen), gunakan angka 1012 (Nomor identitas asli dari PDO::MYSQL_ATTR_SSL_CA).
 */
if (class_exists('Pdo\Mysql') && defined('Pdo\Mysql::ATTR_SSL_CA')) {
    $ssl_attribute = constant('Pdo\Mysql::ATTR_SSL_CA'); // Laptop Kamu
} else {
    $ssl_attribute = 1012; // Laptop Temen (Aman dari Fatal Error)
}

// Deteksi koneksi
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;ssl-mode=VERIFY_IDENTITY";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        $ssl_attribute => $cert_path, // Menggunakan attribute yang sudah disesuaikan otomatis
    ]);
    
    // echo "Koneksi berhasil!"; 
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
} 
?>