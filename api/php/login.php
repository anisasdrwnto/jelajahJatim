<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jelajah Jawa Timur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/login.css">
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center p-3 p-md-4">

    <div class="card login-card shadow-lg border-0 w-100">
        <div class="row g-0 h-100">
            <div class="col-lg-6 position-relative p-0 d-none d-lg-block">
                <div class="position-absolute top-0 start-0 p-4 z-3 text-white fw-bold fs-4">
                    <img src="/assets/logo.png" alt="Logo" class="logo-icon-img">
                </div>

                <div id="wisataCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        
                        <div class="carousel-item active" data-bs-interval="4000">
                            <img src="/assets/image1.jpg" class="d-block w-100" alt="Bromo">
                        </div>
                        <div class="carousel-item" data-bs-interval="4000">
                            <img src="/assets/image2.jpg" class="d-block w-100" alt="Alam">
                        </div>
                        <div class="carousel-item" data-bs-interval="4000">
                            <img src="/assets/image3.jpg" class="d-block w-100" alt="Ijen">
                        </div>

                    </div>
                    <div class="carousel-overlay"></div>
                    
                    <div class="carousel-content">
                       <h2 class="fw-bold mb-3">Halo, Selamat Datang!👋</h2>
                       <p class="fw-light mb-0">Event seru udah nunggu kamu. Masuk sekarang, cek jadwal terbaru, dan share pengalamanmu di Jelajah Jawa Timur.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="w-100" style="max-width: 400px;">
                    
                    <div class="d-lg-none mb-4 fw-bold fs-4 text-dark text-center">
                         <img src="/assets/logo2.png" alt="Logo" class="logo-icon-img-2">
                    </div>

                    <h2 class="fw-bold text-dark mb-2">Masuk ke Akun</h2>
                    <p class="text-muted mb-4 fs-6">Telusuri semua event yang sedang berlangsung di Jawa Timur.Akses mudah dan cepat.</p>

                    <form>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-secondary">Email Kamu</label>
                            <input type="email" class="form-control py-2" id="email" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium text-secondary">Password</label>
                            <input type="password" class="form-control py-2" id="password" placeholder="••••••••" required>
                        </div>
                        <button type="button" class="btn btn-primary-custom w-100 rounded-3" id="btnLogin">Masuk Sekarang</button>
                    </form>

                    <div class="divider my-4 small">atau lanjutkan dengan</div>

                    <div class="d-flex gap-3 mb-4">
                        <button class="btn btn-light border flex-grow-1 py-2 text-secondary fw-medium d-flex align-items-center justify-content-center gap-2" id="btnGoogle">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo" style="width: 20px; height: 20px;">
                            Google
                        </button>
                        <button type="button" class="btn btn-light border flex-grow-1 py-2 text-secondary fw-medium d-flex align-items-center justify-content-center gap-2" id="btnApple">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple Logo" style="width: 20px; height: 20px;">
                            Apple
                        </button>
                    </div>

                    <p class="text-center text-muted small mb-0">
                        Belum punya akun? <a href="register.php" class="text-green fw-bold text-decoration-none">Daftar Akun</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery --->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const BASE_URL = "/api/";  
    </script>
    <script src="/js/login.js"></script>
</body>
</html>