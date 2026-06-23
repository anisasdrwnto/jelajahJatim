<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../koneksi.php';
require_once 'auth.php';

authenticate($pdo);

$stmt = $pdo->query("SELECT 
    mdw_id_destinasi_wisata AS id,
    mdw_nama_destinasi_wisata AS nama,
    mdw_kabupaten_kota AS kabupaten_kota,
    mdw_wilayah AS wilayah,
    mdw_alamat_lengkap AS alamat,
    mdw_kategori AS kategori,
    mdw_deskripsi AS deskripsi
FROM mst_destinasi_wisata");

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data' => $data]);