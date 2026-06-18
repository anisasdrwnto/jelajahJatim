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
 * Menangani perbedaan konstanta antara PHP 8.5 (Laptop Temen) dan PHP 8.3 (Laptop Lu)
 */
if (class_exists('Pdo\Mysql') && defined('Pdo\Mysql::ATTR_SSL_CA')) {
    // Laptop Temen (PHP 8.4 / 8.5)
    $ssl_ca_attr     = constant('Pdo\Mysql::ATTR_SSL_CA');
    $ssl_verify_attr = constant('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT');
} else {
    // Laptop Lu (PHP 8.3 ke bawah)
    // Tidak perlu pakai angka 1012, pakai konstanta bawaan resminya saja karena PHP 8.3 sudah kenal
    $ssl_ca_attr     = PDO::MYSQL_ATTR_SSL_CA;
    $ssl_verify_attr = PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT;
}

// Deteksi koneksi
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;ssl-mode=VERIFY_IDENTITY";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        
        // 1. Memasukkan sertifikat SSL
        $ssl_ca_attr => $cert_path, 
        
        // 2. OBAT ERROR 1105: Matikan verifikasi ketat server cert agar SSL tidak diam-diam mati
        $ssl_verify_attr => false 
    ]);
    
    // echo "Koneksi berhasil!"; 
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
} 
?>