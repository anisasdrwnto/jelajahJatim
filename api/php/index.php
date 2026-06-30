<?php
session_start();

// 1. Ambil data sesi
$isLoggedIn = isset($_SESSION['mus_id_users']);
$userRole   = $_SESSION['role'] ?? 'Guest';
$userName   = htmlspecialchars($_SESSION['mus_name'] ?? 'User');

// 2. Proteksi halaman admin
$halaman_publik = ['index.php', 'login.php', 'register.php'];
$current_file = basename($_SERVER['PHP_SELF']);

if (!in_array($current_file, $halaman_publik)) {
    if (!$isLoggedIn || !in_array($userRole, ['ADMIN', 'ADMIN_MASTER'])) {
        header("Location: index.php");
        exit;
    }
}

// 3. Redirect khusus Admin
if ($isLoggedIn) {
    if ($userRole === 'ADMIN_MASTER') {
        header("Location: /api/php/dashboard_master.php");
        exit;
    } elseif ($userRole === 'ADMIN') {
        header("Location: /api/php/dashboard_admin.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajah Jawa Timur</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inria+Serif:ital,wght@0,700;1,400&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/css/index.css">
</head>
<body>


<!-- ===================== HERO ===================== -->
<section class="hero-section" id="hero">
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="/assets/logo.png" alt="Logo" height="100">
            </a>
            <button class="navbar-toggler border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto gap-1">
                    <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#destinasi">Destinasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kegiatan">Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ulasan">Ulasan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>

                <div class="d-flex gap-2 mt-2 mt-lg-0 align-items-center">
                    <?php if ($isLoggedIn): ?>
                        <div class="dropdown">
                            <button class="btn btn-link p-0 border-0" data-bs-toggle="dropdown">
                                <div class="profile-avatar">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text fw-semibold"><?= $userName ?> (<?= $userRole ?>)</span></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <?php if ($userRole === 'ADMIN' || $userRole === 'ADMIN_MASTER'): ?>
                                    <li><a class="dropdown-item" href="/api/php/dashboard_admin.php">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard Admin
                                    </a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="/api/php/profile.php">
                                        <i class="bi bi-person me-2"></i> Edit Profil
                                    </a></li>
                                <?php endif; ?>
                                
                                <li><a class="dropdown-item text-danger" href="/api/php/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-light rounded-pill px-4" id="btnLogin">Login</button>
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium" id="btnRegister">Daftar Akun</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="hero-overlay"></div>

    <div class="container hero-content">
        <div class="row">
            <div class="col-12 col-lg-8">
                <p class="hero-subtitle">Selamat Datang di</p>
                <h1 class="hero-title"><span id="typed-text"></span></h1>
                <p class="hero-desc">
                    Dari puncak Bromo yang menawarkan ketenangan, hingga Kawah Ijen
                    yang menyimpan misteri. Mari jelajahi surga tersembunyi di Jawa Timur!
                </p>
                <button type="button" class="btn btn-hero rounded-pill px-4 py-2 mt-2" id="btnMulai">
                    Mulai sekarang
                </button>
            </div>
        </div>
    </div>

    <div class="cloud-parallax" id="cloudParallax">
        <img src="/assets/footer-clouds 1.png" alt="">
    </div>
</section>

<!-- ===================== DESTINASI ===================== -->
<section id="destinasi">
    <div class="container">
        <div class="dest-header">
            <h2>Destinasi Wisata</h2>
            <p>Temukan keindahan alam dan budaya Jawa Timur yang memukau</p>
        </div>

        <div class="dest-search-wrap">
            <div class="dest-search-box">
                <svg class="dest-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="destSearchInput" class="dest-search-input" placeholder="Cari destinasi wisata...">
                <button type="button" class="dest-search-clear" id="destSearchClear" style="display:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="dest-filter-wrap">
            <button type="button" class="dest-filter-btn active" data-kategori="semua"><i class="bi bi-grid-fill"></i> Semua</button>
            <button type="button" class="dest-filter-btn" data-kategori="Alam"><i class="bi bi-tree-fill"></i> Alam</button>
            <button type="button" class="dest-filter-btn" data-kategori="Budaya"><i class="bi bi-building-fill"></i> Budaya</button>
            <button type="button" class="dest-filter-btn" data-kategori="Buatan"><i class="bi bi-stars"></i> Buatan</button>
            <button type="button" class="dest-filter-btn" data-kategori="Kuliner"><i class="bi bi-egg-fried"></i> Kuliner</button>
        </div>

        <div id="destHasilLabel" class="dest-hasil-label" style="display:none;"></div>

        <div class="dest-grid" id="destGrid">
            <div class="dest-loading text-center w-100 py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted mt-2">Memuat destinasi...</div>
            </div>
        </div>

        <div class="destination-button">
            <button type="button" class="btn rounded-pill fw-bold position-relative d-inline-flex align-items-center justify-content-center btn-explore" style="background:#E0BB44;color:white;font-size:16px;padding:10px 50px;box-shadow:0 10px 25px rgba(224,187,68,0.25);transition:0.35s ease;"
                    onclick="window.location.href='destinasi.php'">
                Telusuri Lebih Lanjut
                <span class="position-absolute end-0 me-2 d-inline-flex align-items-center justify-content-center rounded-circle btn-explore-arrow" style="width:28px;height:28px;background:rgba(0,0,0,0.15);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</section>

<!-- ===================== KEGIATAN / EVENT ===================== -->
<section id="kegiatan">
    <div class="container">
        <div class="event-header">
            <h2>Kegiatan Wisata</h2>
            <p>Ikuti kegiatan wisata yang ada di Jawa Timur</p>
        </div>

        <div class="pinterest-grid" id="eventGrid"></div>

        <div class="event-button">
            <button type="button" class="btn rounded-pill fw-bold position-relative d-inline-flex align-items-center justify-content-center btn-explore" style="background:#E0BB44;color:white;font-size:16px;padding:10px 50px;box-shadow:0 10px 25px rgba(224,187,68,0.25);transition:0.35s ease;"
                    onclick="window.location.href='event.php'">
                Telusuri Lebih Lanjut
                <span class="position-absolute end-0 me-2 d-inline-flex align-items-center justify-content-center rounded-circle btn-explore-arrow" style="width:28px;height:28px;background:rgba(0,0,0,0.15);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</section>

<!-- ===================== KONTAK / FOOTER ===================== -->
<section id="kontak">
    <footer class="custom-footer py-4">
        <div class="container">
            <hr class="footer-divider">

            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 text-center text-lg-start">
                    <img src="/assets/logo.png" alt="Jelajah Jawa Timur Logo" class="footer-logo">
                </div>
                <div class="col-lg-7 col-md-12 contact-wrapper">
                    <div class="contact-info mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-map-marker-alt mt-1 me-3"></i>
                            <span>Jalan Imam Bonjol, Sumbersoko, Pandean, Kec. Mejayan, Madiun, Indonesia.</span>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 d-flex align-items-center mb-2 mb-sm-0">
                                <i class="fas fa-phone-alt me-3"></i><span>(123) 456-7890</span>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <i class="fas fa-print me-3"></i><span>(123) 456-7890</span>
                            </div>
                        </div>
                    </div>
                    <div class="social-container d-flex align-items-center">
                        <span class="social-media-label me-4">Social Media</span>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-google-plus-g"></i></a>
                            <a href="#"><i class="fab fa-pinterest-p"></i></a>
                            <a href="#"><i class="fas fa-rss"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="row align-items-center">
                <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                    <ul class="list-inline footer-nav mb-0">
                        <li class="list-inline-item me-4"><a href="#">ABOUT US</a></li>
                        <li class="list-inline-item me-4"><a href="#">REVIEW</a></li>
                        <li class="list-inline-item me-4"><a href="#">HELP</a></li>
                        <li class="list-inline-item"><a href="#">PRIVACY POLICY</a></li>
                    </ul>
                </div>
                <div class="col-md-5 text-center text-md-end copyright-text">
                    Copyright &copy; 2026 &bull; Jelajah Jawa Timur
                </div>
            </div>
        </div>
    </footer>
</section>

<button class="btn scroll-btn" id="scrollBtn">
    <i class="bi bi-chevron-down" id="scrollIcon"></i>
</button>

<!-- ===================== MODAL: DETAIL DESTINASI ===================== -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius:16px;background-color:#f8f9fa;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h4 class="modal-title fw-bold" id="detailNamaDestinasi" style="color:#1a4a35;">Nama Destinasi</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 rounded-4 overflow-hidden shadow-sm">
                    <img id="detailFotoDestinasi" src="" alt="Foto Destinasi" class="w-100" style="height:400px;object-fit:cover;">
                </div>
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm border-0 h-100">
                            <h5 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-info-square-fill text-success me-2"></i> Deskripsi Wisata
                            </h5>
                            <p id="detailDeskripsiDestinasi" class="text-secondary" style="line-height:1.8;text-align:justify;font-size:15px;"></p>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="bg-white p-4 rounded-4 shadow-sm border-0 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div class="d-flex flex-column">
                                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                                        <i class="bi bi-star-fill text-warning me-2"></i> Ulasan
                                    </h5>
                                    <span class="text-muted" style="font-size:13px;">Berdasarkan pengunjung</span>
                                </div>
                                <button type="button" class="btn text-white fw-semibold rounded-pill px-3 py-2 btn-write-review"
                                        style="background-color:#0f6e56;font-size:14px;box-shadow:0 4px 10px rgba(15,110,86,0.2);"
                                        data-bs-toggle="modal" data-bs-target="#modalUlasan">
                                    <i class="bi bi-pencil-square me-1"></i> Tulis Ulasan
                                </button>
                            </div>
                            <div class="ulasan-list overflow-auto pe-2" style="max-height:350px;" id="listUlasanModal">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-chat-square-text" style="font-size:2.5rem;color:#ccc;"></i>
                                    <p class="mt-3 mb-0 fw-medium">Belum ada ulasan.</p>
                                    <small>Jadilah yang pertama memberikan ulasan untuk tempat ini!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL: TULIS ULASAN ===================== -->
<div class="modal fade" id="modalUlasan" tabindex="-1" aria-labelledby="modalUlasanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-soft" style="border-radius:15px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalUlasanLabel" style="color:#2d6a4f;">Tulis Ulasan Wisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted mb-4" style="font-size:14px;">
                    Bagikan pengalamanmu saat mengunjungi
                    <strong id="namaDestinasiModal" style="color:#E85D04;">Destinasi</strong>!
                </p>

                <form id="formPopUpUlasan">
                    <input type="hidden" id="idDestinasiModal" name="destinasi_id">

                    <div class="mb-3">
                        <label class="form-label fw-medium" style="font-size:14px;">Kategori Ulasan</label>
                        <select class="form-select rounded-3" name="kategori_ulasan" required>
                            <option value="" disabled selected>Pilih kategori...</option>
                            <option value="Fasilitas">Fasilitas</option>
                            <option value="Pelayanan">Pelayanan</option>
                            <option value="Internet">Internet</option>
                            <option value="Akses Lokasi">Akses Lokasi</option>
                            <option value="Toilet">Toilet</option>
                            <option value="Harga">Harga</option>
                        </select>
                    </div>

                    <div class="mb-3 text-center">
                        <label class="form-label fw-medium d-block" style="font-size:14px;">Rating</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" id="ulasanStar5" value="5"><label for="ulasanStar5">★</label>
                            <input type="radio" name="rating" id="ulasanStar4" value="4"><label for="ulasanStar4">★</label>
                            <input type="radio" name="rating" id="ulasanStar3" value="3"><label for="ulasanStar3">★</label>
                            <input type="radio" name="rating" id="ulasanStar2" value="2"><label for="ulasanStar2">★</label>
                            <input type="radio" name="rating" id="ulasanStar1" value="1"><label for="ulasanStar1">★</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium" style="font-size:14px;">Isi Ulasan</label>
                        <textarea class="form-control rounded-3" name="isi_ulasan" rows="3" placeholder="Ceritakan pengalaman Anda..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium" style="font-size:14px;">Foto (opsional)</label>
                        <input type="file" class="form-control" name="foto" id="ulasanFotoInput" accept="image/jpeg,image/png">
                        <small class="text-muted">Format JPG/PNG, maks. 2MB</small>
                    </div>

                    <button type="button" class="btn w-100 rounded-pill fw-bold btn-submit-review" style="background:#00296b;color:white;">Kirim Ulasan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL: PESAN TIKET ===================== -->
<div class="modal fade" id="modalPesanTiket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pesan Tiket: <span id="namaEventModal" style="color:#2d6a4f;"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formPesanTiket">
                    <input type="hidden" id="idEventModal" name="event_id">
                    <input type="hidden" name="action" value="pesan">

                    <label class="form-label fw-bold mb-2">Pilih Jenis Tiket</label>
                    <div id="daftarTiketContainer" class="mb-4"></div>

                    <label class="form-label fw-bold mb-2">Jumlah Tiket</label>
                    <input type="number" class="form-control mb-4" name="jumlah_tiket" id="jumlahTiketInput"
                           value="1" min="1" max="20" required>

                    <label class="form-label fw-bold mb-3">Metode Pembayaran</label>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="radio" name="metode_bayar" id="bayarCash" value="Cash" class="d-none" required>
                            <label for="bayarCash" class="btn btn-outline-secondary w-100 py-2 metode-bayar-label">
                                <i class="bi bi-cash-stack me-1"></i> Cash/Tunai
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" name="metode_bayar" id="bayarQRIS" value="QRIS" class="d-none">
                            <label for="bayarQRIS" class="btn btn-outline-secondary w-100 py-2 metode-bayar-label">
                                <i class="bi bi-qr-code-scan me-1"></i> QRIS
                            </label>
                        </div>
                    </div>
                    <style>
                        input[name="metode_bayar"]:checked + .metode-bayar-label {
                            background-color: #0f6e56 !important;
                            border-color: #0f6e56 !important;
                            color: #fff !important;
                        }
                    </style>

                    <div id="ringkasanTotal" class="mb-4 p-3 rounded-3 d-none" style="background:#f0f7ff;border:1px solid #cfe2ff;">
                        <div class="d-flex justify-content-between" style="font-size:14px;color:#555;">
                            <span id="ringkasanLabel">Tiket × Jumlah</span>
                            <span id="ringkasanRincian">-</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold mt-1" style="font-size:18px;color:#0d6efd;">
                            <span>Total Bayar</span>
                            <span id="ringkasanTotalHarga">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 py-2 fw-bold text-white btn-book-now" style="background-color:#007bff;border-radius:8px;">Pesan Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.1.0/dist/typed.umd.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
(function () {
    'use strict';

    /* ============================================================
       CONFIG & GLOBAL STATE
    ============================================================ */
    const BASE_URL    = '/api/';
    const isLoggedIn  = <?= $isLoggedIn ? 'true' : 'false' ?>;
    const userRole    = '<?= $userRole ?>';

    const BADGE_COLOR = {
        Alam:    '#2d6a4f',
        Budaya:  '#D62828',
        Buatan:  '#0077B6',
        Kuliner: '#E85D04'
    };

    let allDestinasi   = [];   // cache data destinasi dari server
    let activeKategori  = 'semua';
    let activeKeyword   = '';

    /* ============================================================
       HELPERS
    ============================================================ */
    function escHtml(str) {
        return $('<span>').text(String(str || '')).html();
    }

    function getFotoUrlDestinasi(foto) {
        if (!foto) return '/assets/placeholder.jpg';
        if (/^https?:\/\//.test(foto)) return foto;
        return BASE_URL + '../uploads/destinasi/' + foto;
    }

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
    }

    /* Hitung & tampilkan ringkasan total pembayaran di modal Pesan Tiket */
    function updateRingkasanTotal() {
        const tiketTerpilih  = $('input[name="tiket_id"]:checked');
        const metodeTerpilih = $('input[name="metode_bayar"]:checked');
        const jumlah = parseInt($('#jumlahTiketInput').val(), 10) || 0;

        if (tiketTerpilih.length === 0 || metodeTerpilih.length === 0 || jumlah <= 0) {
            $('#ringkasanTotal').addClass('d-none');
            return;
        }

        const harga = parseFloat(tiketTerpilih.data('harga')) || 0;
        const nama  = tiketTerpilih.data('nama') || 'Tiket';
        const total = harga * jumlah;

        $('#ringkasanLabel').text(`${nama} × ${jumlah}`);
        $('#ringkasanRincian').text(formatRupiah(harga) + ' / tiket');
        $('#ringkasanTotalHarga').text(formatRupiah(total));
        $('#ringkasanTotal').removeClass('d-none');
    }

    /* ============================================================
       AUTH GUARD
    ============================================================ */
    function cekLogin() {
        if (isLoggedIn) return true;

        Swal.fire({
            title: 'Belum Login!',
            text: 'Anda harus login terlebih dahulu untuk mengakses fitur ini.',
            icon: 'warning',
            confirmButtonText: 'Login Sekarang',
            confirmButtonColor: '#0f6e56'
        }).then(result => {
            if (result.isConfirmed) window.location.href = BASE_URL + 'php/login.php';
        });
        return false;
    }

    /* ============================================================
       DESTINASI WISATA — render, filter, detail
    ============================================================ */
    function renderDestinasi(data) {
        const grid  = $('#destGrid');
        const items = data.filter(d => d.mdw_status === 'Aktif').slice(0, 6);
        grid.empty();

        if (items.length === 0) {
            grid.html(`
                <div class="dest-empty w-100 text-center py-5">
                    <i class="bi bi-map" style="font-size:48px;color:#ccc;display:block;margin-bottom:12px;"></i>
                    <p class="text-muted mb-0">Tidak ada destinasi yang ditemukan.</p>
                </div>`);
            return;
        }

        items.forEach(item => {
            const fotoUrl = getFotoUrlDestinasi(item.mdw_foto);
            const warna   = BADGE_COLOR[item.mdw_kategori] || '#0f6e56';
            const nama    = escHtml(item.mdw_nama_destinasi_wisata);
            const kabkota = escHtml(item.mdw_kabupaten_kota || '');
            const desk    = escHtml(item.mdw_deskripsi || 'Destinasi wisata menarik di Jawa Timur.');
            const kat     = escHtml(item.mdw_kategori || '');
            const idDest  = item.mdw_id_destinasi_wisata;

            grid.append(`
                <div class="dest-card">
                    <div class="dest-img">
                        <img src="${fotoUrl}" alt="${nama}" loading="lazy"
                             onerror="this.onerror=null;this.src='/assets/placeholder.jpg'">
                        <span class="dest-badge" style="background:${warna};">${kat}</span>
                    </div>
                    <div class="dest-body">
                        <h3>${nama}</h3>
                        <div class="dest-loc">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            ${kabkota}
                        </div>
                        <p>${desk}</p>
                        <div class="dest-footer">
                            <div class="dest-rating">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#E0BB44" stroke="#E0BB44" stroke-width="1">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span class="ms-1" style="font-size:13px;color:#888;font-weight:400;">Jawa Timur</span>
                            </div>
                            <button type="button" class="btn dest-btn rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#modalDetail"
                                    onclick="bukaDetailModal('${idDest}')">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>`);
        });
    }

    function applyFilter() {
        const keyword = activeKeyword.toLowerCase().trim();
        const kat     = activeKategori;

        const filtered = allDestinasi.filter(d => {
            const matchKat = kat === 'semua' || d.mdw_kategori === kat;
            const matchKey = !keyword || [
                d.mdw_nama_destinasi_wisata,
                d.mdw_kabupaten_kota,
                d.mdw_deskripsi,
                d.mdw_wilayah
            ].some(field => (field || '').toLowerCase().includes(keyword));
            return matchKat && matchKey;
        });

        const label = $('#destHasilLabel');
        if (keyword || kat !== 'semua') {
            const total = filtered.filter(d => d.mdw_status === 'Aktif').length;
            let txt;
            if (keyword && kat !== 'semua') {
                txt = `Menampilkan <strong>${total}</strong> hasil untuk "<em>${escHtml(activeKeyword)}</em>" di kategori <strong>${escHtml(kat)}</strong>`;
            } else if (keyword) {
                txt = `Menampilkan <strong>${total}</strong> hasil untuk "<em>${escHtml(activeKeyword)}</em>"`;
            } else {
                txt = `Menampilkan kategori <strong>${escHtml(kat)}</strong> &mdash; <strong>${total}</strong> destinasi`;
            }
            label.html(txt).show();
        } else {
            label.hide();
        }

        renderDestinasi(filtered);
    }

    function renderUlasanList(data) {
        const container = $('#listUlasanModal');
        container.empty();

        if (!data || data.length === 0) {
            container.html(`
                <div class="text-center text-muted py-5">
                    <i class="bi bi-chat-square-text" style="font-size:2.5rem;color:#ccc;"></i>
                    <p class="mt-3 mb-0 fw-medium">Belum ada ulasan.</p>
                    <small>Jadilah yang pertama memberikan ulasan untuk tempat ini!</small>
                </div>`);
            return;
        }

        data.forEach(item => {
            let bintang = '';
            for (let i = 1; i <= 5; i++) {
                bintang += i <= item.mul_rating
                    ? '<i class="bi bi-star-fill text-warning"></i> '
                    : '<i class="bi bi-star text-muted"></i> ';
            }
            const fotoHtml = item.mul_foto
                ? `<img src="${item.mul_foto}" class="rounded mt-2" style="width:100%;max-width:160px;height:100px;object-fit:cover;">`
                : '';

            container.append(`
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="fw-bold">${escHtml(item.nama_user || 'Pengunjung')}</div>
                        <small class="text-muted">${escHtml(item.mul_kategori || '')}</small>
                    </div>
                    <div style="font-size:13px;">${bintang}</div>
                    <p class="mb-0 mt-1 text-secondary" style="font-size:14px;">${escHtml(item.mul_komentar || '')}</p>
                    ${fotoHtml}
                </div>`);
        });
    }

    function loadUlasanDestinasi(destinasiId) {
        $('#listUlasanModal').html('<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm"></div></div>');
        $.ajax({
            url: BASE_URL + 'proses/proses_ulasan.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'baca_publik', destinasi_id: destinasiId },
            success: function (res) {
                renderUlasanList(res.success ? res.data : []);
            },
            error: function () {
                renderUlasanList([]);
            }
        });
    }

    window.bukaDetailModal = function (id) {
        const dest = allDestinasi.find(d => d.mdw_id_destinasi_wisata === id);
        if (!dest) {
            console.error('Data dengan ID ' + id + ' tidak ditemukan.');
            return;
        }
        $('#detailNamaDestinasi').text(dest.mdw_nama_destinasi_wisata);
        $('#detailFotoDestinasi').attr('src', getFotoUrlDestinasi(dest.mdw_foto));
        $('#detailDeskripsiDestinasi').text(dest.mdw_deskripsi || 'Deskripsi untuk destinasi ini belum tersedia.');

        $('#namaDestinasiModal').text(dest.mdw_nama_destinasi_wisata);
        $('#idDestinasiModal').val(id);
        loadUlasanDestinasi(id);
    };

    function loadDestinasi() {
        $.ajax({
            url: BASE_URL + 'proses/proses_destinasi.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'baca' },
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    allDestinasi = res.data;
                    applyFilter();
                } else {
                    $('#destGrid').html('<p class="text-center text-muted w-100 py-5">Belum ada destinasi tersedia.</p>');
                }
            },
            error: function (xhr) {
                $('#destGrid').html('<p class="text-center text-danger w-100 py-5">Gagal memuat destinasi. Silakan refresh halaman.</p>');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    /* ============================================================
       KEGIATAN / EVENT — render & tiket
    ============================================================ */
    function renderEvent(data) {
        const grid = $('#eventGrid');
        grid.empty();

        data.slice(0, 3).forEach(item => {
            grid.append(`
                <div class="pinterest-card">
                    <div class="card-image">
                        <img src="${item.mev_foto}" alt="${item.mev_nama_event}" onerror="this.src='/assets/placeholder.jpg'">
                        <div class="card-overlay"></div>
                    </div>
                    <div class="card-content">
                        <div class="card-meta">
                            <span class="event-tag1">${item.mev_kategori}</span>
                            <div class="card-date"><i class="bi bi-calendar-event me-1"></i> ${item.mev_tanggal_event}</div>
                        </div>
                        <h3>${item.mev_nama_event}</h3>
                        <p>${item.mev_deskripsi}</p>
                        <button type="button" class="btn card-btn w-100 mt-3 rounded-3 py-2 fw-semibold"
                                data-bs-toggle="modal" data-bs-target="#modalPesanTiket"
                                onclick="bukaModalTiket('${item.mev_id_event}', '${item.mev_nama_event}')">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>`);
        });
    }

    window.bukaModalTiket = function (id, nama) {
        $('#namaEventModal').text(nama);
        $('#idEventModal').val(id);
        $('#jumlahTiketInput').val(1);
        $('input[name="metode_bayar"]').prop('checked', false);
        $('#ringkasanTotal').addClass('d-none');

        $.ajax({
            url: BASE_URL + 'proses/proses_pemesanan.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'ambil_tiket', event_id: id },
            success: function (res) {
                const container = $('#daftarTiketContainer');
                container.empty();

                if (res.success && res.data.length > 0) {
                    res.data.forEach(ticket => {
                        container.append(`
                            <div class="mb-2 position-relative">
                                <input type="radio" name="tiket_id" id="tiket_${ticket.mbt_id_tiket}" value="${ticket.mbt_id_tiket}" data-harga="${ticket.mbt_harga}" data-nama="${ticket.mbt_nama_tiket}" required>
                                <label for="tiket_${ticket.mbt_id_tiket}" class="ticket-list-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">${ticket.mbt_nama_tiket}</div>
                                        <div class="text-muted" style="font-size:0.85rem;">Rp ${parseFloat(ticket.mbt_harga).toLocaleString()}</div>
                                    </div>
                                    <i class="bi bi-check-circle-fill check-icon"></i>
                                </label>
                            </div>`);
                    });
                } else {
                    container.append('<p class="text-muted">Tiket tidak tersedia.</p>');
                }
            }
        });
    };

    function loadEvent() {
        $.ajax({
            url: BASE_URL + 'proses/proses_event.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'baca' },
            success: function (res) {
                if (res.success) {
                    renderEvent(res.data);
                } else {
                    console.warn('Gagal memuat event: ' + res.message);
                }
            }
        });
    }

    /* ============================================================
       UI WIDGETS — typed text, scroll button, star rating toggle
    ============================================================ */
    function initTypedText() {
        new Typed('#typed-text', {
            strings: ['Jelajah Jawa Timur'],
            typeSpeed: 80,
            backSpeed: 40,
            startDelay: 300,
            backDelay: 200,
            showCursor: true,
            cursorChar: '|',
            loop: true
        });
    }

    function initScrollButton() {
        const scrollBtn  = document.getElementById('scrollBtn');
        const scrollIcon = document.getElementById('scrollIcon');
        const kegiatan   = document.getElementById('kegiatan');

        window.addEventListener('scroll', () => {
            scrollBtn.classList.add('visible');
            scrollIcon.className = window.scrollY > 200 ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
        });

        scrollBtn.addEventListener('click', () => {
            window.scrollY > 200
                ? window.scrollTo({ top: 0, behavior: 'smooth' })
                : kegiatan.scrollIntoView({ behavior: 'smooth' });
        });

        window.dispatchEvent(new Event('scroll'));
    }

    /* Toggle behaviour for any "star-rating" or "tiket_id" radio group:
       klik ulang pada pilihan yang sama akan membatalkannya. */
    function initToggleableRadios(selector) {
        $(document).on('click', selector, function () {
            if ($(this).hasClass('is-checked')) {
                $(this).prop('checked', false).removeClass('is-checked');
            } else {
                $(selector).removeClass('is-checked');
                $(this).addClass('is-checked');
            }
            updateRingkasanTotal();
        });
    }

    /* ============================================================
       EVENT BINDINGS
    ============================================================ */
    function bindNavbar() {
        $('.navbar-nav .nav-link').on('click', function () {
            $('.navbar-nav .nav-link').removeClass('active');
            $(this).addClass('active');
        });
    }

    function bindHeroCta() {
        $('#btnMulai').on('click', function () {
            $('#cloudParallax').addClass('active');
            setTimeout(() => {
                document.getElementById('destinasi').scrollIntoView({ behavior: 'smooth' });
            }, 800);
        });
    }

    function bindAuthButtons() {
        $('#btnLogin').on('click', () => window.location.href = BASE_URL + 'php/login.php');
        $('#btnRegister').on('click', () => window.location.href = BASE_URL + 'php/register.php');
    }

    function bindAuthGuards() {
        // Form ulasan
        $('#formPopUpUlasan').on('submit', function (e) {
            if (!cekLogin()) e.preventDefault();
        });

        // Tombol "Tulis Ulasan" di modal detail destinasi
        $('[data-bs-target="#modalUlasan"]').on('click', function (e) {
            if (!isLoggedIn) {
                e.preventDefault();
                e.stopPropagation();
                cekLogin();
            }
        });

        // Tombol "Daftar Sekarang" pada kartu event (render dinamis -> event delegation)
        $(document).on('click', '.card-btn', function (e) {
            if (!isLoggedIn) {
                e.preventDefault();
                e.stopPropagation();
                cekLogin();
            }
        });
    }

    function bindDestinasiFilters() {
        $(document).on('click', '.dest-filter-btn', function () {
            $('.dest-filter-btn').removeClass('active');
            $(this).addClass('active');
            activeKategori = $(this).data('kategori');
            applyFilter();
        });

        let searchTimer = null;
        $('#destSearchInput').on('input', function () {
            const val = $(this).val();
            activeKeyword = val;
            $('#destSearchClear').toggle(val.length > 0);

            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilter, 300);
        });

        $('#destSearchClear').on('click', function () {
            $('#destSearchInput').val('').trigger('input');
        });
    }

    function bindTicketForm() {
        $(document).on('input', '#jumlahTiketInput', updateRingkasanTotal);
        $(document).on('change', 'input[name="metode_bayar"]', updateRingkasanTotal);

        $('#formPesanTiket').on('submit', function (e) {
            e.preventDefault();

            if (!isLoggedIn) {
                Swal.fire({
                    title: 'Belum Login!',
                    text: 'Anda harus login untuk melakukan pemesanan tiket.',
                    icon: 'warning',
                    confirmButtonText: 'Login Sekarang',
                    confirmButtonColor: '#0f6e56'
                }).then(result => {
                    if (result.isConfirmed) {
                        localStorage.setItem('redirect_event', $('#idEventModal').val());
                        window.location.href = BASE_URL + 'php/login.php';
                    }
                });
                return;
            }

            const formData = $(this).serialize();
            $.post(BASE_URL + 'proses/proses_pemesanan.php', formData, function (res) {
                if (!res.success) {
                    Swal.fire({
                        title: 'Gagal Memesan',
                        text: res.message || 'Terjadi kesalahan saat memproses pemesanan.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                $('#modalPesanTiket').modal('hide');

                const pesanStatus = res.status === 'Lunas'
                    ? `Pesanan Anda sudah lunas (QRIS). Kode booking: <b>${res.booking_code}</b>`
                    : `Pesanan tercatat dengan status "Pending". Tunjukkan kode booking <b>${res.booking_code}</b> ke panitia saat menyerahkan pembayaran tunai di lokasi.`;

                Swal.fire({
                    title: 'Pemesanan Berhasil!',
                    html: pesanStatus,
                    icon: 'success',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#0f6e56'
                });

                // reset form & ringkasan setelah modal ditutup
                $('#formPesanTiket')[0].reset();
                $('input[name="tiket_id"]').removeClass('is-checked');
                $('#ringkasanTotal').addClass('d-none');
            }, 'json').fail(function () {
                Swal.fire({
                    title: 'Gagal Memesan',
                    text: 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            });
        });
    }

    function bindReviewForm() {
        $('.btn-submit-review').on('click', function () {
            const form = document.getElementById('formPopUpUlasan');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            formData.append('action', 'tambah');

            $.ajax({
                url: BASE_URL + 'proses/proses_ulasan.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
                        return;
                    }
                    $('#modalUlasan').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Terima kasih!',
                        text: res.message,
                        confirmButtonColor: '#0f6e56'
                    });
                    form.reset();
                    $('.star-rating input').prop('checked', false).removeClass('is-checked');

                    // refresh daftar ulasan di modal detail
                    const destinasiId = $('#idDestinasiModal').val();
                    if (destinasiId) loadUlasanDestinasi(destinasiId);
                },
                error: function () {
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengirim ulasan.', 'error');
                }
            });
        });
    }

    /* ============================================================
       INIT
    ============================================================ */
    $(document).ready(function () {
        initTypedText();
        initScrollButton();
        initToggleableRadios('.star-rating input');
        initToggleableRadios('input[name="tiket_id"]');

        bindNavbar();
        bindHeroCta();
        bindAuthButtons();
        bindAuthGuards();
        bindDestinasiFilters();
        bindTicketForm();
        bindReviewForm();

        loadDestinasi();
        loadEvent();
    });
})();
</script>

</body>
</html>