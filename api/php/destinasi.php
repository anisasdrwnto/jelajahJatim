<?php
session_start();

$isLoggedIn = isset($_SESSION['mus_id_users']);
$userRole   = $_SESSION['role'] ?? 'Guest';
$userName   = htmlspecialchars($_SESSION['mus_name'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinasi Wisata - Jelajah Jawa Timur</title>

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
    <style>
        body { background:#f5f5f3; }

        .dw-page-hero {
            background:#0f6e56;
            padding:130px 0 60px;
            position:relative;
        }
        .dw-page-hero h1 {
            font-family:'Inria Serif', serif;
            color:white;
            font-size:clamp(36px, 5vw, 56px);
            margin-bottom:10px;
        }
        .dw-page-hero p {
            color:rgba(255,255,255,0.85);
            font-size:16px;
            margin-bottom:0;
        }
        .dw-breadcrumb a {
            color:rgba(255,255,255,0.75);
            text-decoration:none;
            font-size:14px;
        }
        .dw-breadcrumb a:hover { color:#fff; }

        #dw-list { padding:60px 0 100px; }
    </style>
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#0f6e56;position:fixed;top:0;width:100%;z-index:20;">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="/assets/logo.png" alt="Logo" height="60">
        </a>
        <button class="navbar-toggler border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" href="destinasi.php">Destinasi</a></li>
                <li class="nav-item"><a class="nav-link" href="event.php">Kegiatan</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#kontak">Kontak</a></li>
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

<!-- ===================== HEADER HALAMAN ===================== -->
<section class="dw-page-hero">
    <div class="container">
        <div class="dw-breadcrumb mb-2">
            <a href="index.php">Beranda</a> <span class="text-white-50">/</span> <span class="text-white">Destinasi Wisata</span>
        </div>
        <h1>Destinasi Wisata</h1>
        <p>Temukan keindahan alam dan budaya Jawa Timur yang memukau</p>
    </div>
</section>

<!-- ===================== LIST DESTINASI ===================== -->
<section id="dw-list">
    <div class="container">

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

    </div>
</section>

<!-- ===================== FOOTER ===================== -->
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
                                <button type="button" id="btnTulisUlasan"
                                        class="btn text-white fw-semibold rounded-pill px-3 py-2 btn-write-review"
                                        style="background-color:#0f6e56;font-size:14px;box-shadow:0 4px 10px rgba(15,110,86,0.2);">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
(function () {
    'use strict';

    const BASE_URL   = '/api/';
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

    const BADGE_COLOR = {
        Alam:    '#2d6a4f',
        Budaya:  '#D62828',
        Buatan:  '#0077B6',
        Kuliner: '#E85D04'
    };

    let allDestinasi   = [];
    let activeKategori  = 'semua';
    let activeKeyword   = '';

    function escHtml(str) {
        return $('<span>').text(String(str || '')).html();
    }

    function getFotoUrlDestinasi(foto) {
        if (!foto) return '/assets/placeholder.jpg';
        if (/^https?:\/\//.test(foto)) return foto;
        return BASE_URL + '../uploads/destinasi/' + foto;
    }

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
       RENDER DESTINASI (seluruh data, tanpa slice)
    ============================================================ */
    function renderDestinasi(data) {
        const grid  = $('#destGrid');
        const items = data.filter(d => d.mdw_status === 'Aktif');
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

    function initToggleableRadios(selector) {
        $(document).on('click', selector, function () {
            if ($(this).hasClass('is-checked')) {
                $(this).prop('checked', false).removeClass('is-checked');
            } else {
                $(selector).removeClass('is-checked');
                $(this).addClass('is-checked');
            }
        });
    }

    function bindAuthButtons() {
        $('#btnLogin').on('click', () => window.location.href = BASE_URL + 'php/login.php');
        $('#btnRegister').on('click', () => window.location.href = BASE_URL + 'php/register.php');
    }

    function bindAuthGuards() {
        $('#formPopUpUlasan').on('submit', function (e) {
            if (!cekLogin()) e.preventDefault();
        });

        $(document).on('click', '#btnTulisUlasan', function (e) {
            e.preventDefault();
            if (!cekLogin()) return;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUlasan')).show();
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

                    const destinasiId = $('#idDestinasiModal').val();
                    if (destinasiId) loadUlasanDestinasi(destinasiId);
                },
                error: function () {
                    Swal.fire('Error!', 'Terjadi kesalahan saat mengirim ulasan.', 'error');
                }
            });
        });
    }

    $(document).ready(function () {
        initToggleableRadios('.star-rating input');
        bindAuthButtons();
        bindAuthGuards();
        bindDestinasiFilters();
        bindReviewForm();

        loadDestinasi();
    });
})();
</script>

</body>
</html>