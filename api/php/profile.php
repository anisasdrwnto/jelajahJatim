<?php
session_start();
// Pastikan koneksi.php mendefinisikan variabel $pdo
require_once '../koneksi.php'; 

// Jika session belum ada, paksa user login dulu
if (!isset($_SESSION['mus_id_users'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['mus_id_users'];
// Pastikan $pdo sudah terdefinisi di koneksi.php
$stmt = $pdo->prepare("SELECT * FROM mst_users WHERE mus_id_users = ?");
$stmt->execute([$id]);
$data_terbaru = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data_terbaru) {
    $_SESSION['mus_name']  = $data_terbaru['mus_name'];
    $_SESSION['mus_email'] = $data_terbaru['mus_email'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: url('../../assets/background-login.jpg') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    .card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
    }
    /* Warna untuk judul Edit Profil */
    .text-custom-green {
        color: #355848 !important;
    }
    /* Warna dan style untuk tombol */
    .btn-primary {
        background-color: #355848 !important;
        border-color: #355848 !important;
        border-radius: 10px;
        padding: 10px;
    }
    .btn-primary:hover {
        background-color: #2a4639 !important; /* Warna sedikit lebih gelap saat di-hover */
        border-color: #2a4639 !important;
    }
</style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
               <div class="text-center mb-4">
                    <h3 class="fw-bold text-custom-green">Edit Profil</h3>
                    <p class="text-muted">Perbarui informasi akun Anda</p>
                </div>
                <form action="../proses/proses_profile.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="mus_name" class="form-control shadow-sm" 
                               value="<?= htmlspecialchars($_SESSION['mus_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="mus_email" class="form-control shadow-sm" 
                               value="<?= htmlspecialchars($_SESSION['mus_email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="mus_password" class="form-control shadow-sm" 
                               placeholder="********">
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>