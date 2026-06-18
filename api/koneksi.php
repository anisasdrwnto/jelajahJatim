<?php 

// Buat koneksi ke database TIDB
$host     = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$username = "3ZRN5c6PUXtTqvf.root";
$password = "mWYBDrZ8ogUT5beM";
$database = "jelajahjatim";
$port     = 4000;

// Deteksi koneksi
try {
    // Spasi di setelah titik koma dihapus agar parser PHP membaca DSN dengan sempurna
    $dsn = "mysql:host=$host;port=$port;dbname=$database;ssl-mode=VERIFY_IDENTITY";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lempar pesan jika terjadi error 
        Pdo\Mysql::ATTR_SSL_CA => dirname(__DIR__) . '/isrgrootx1.pem',
    ]);
    
    // echo "Koneksi berhasil!"; 
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
} 
?>