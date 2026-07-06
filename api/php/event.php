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
    <title>Event Wisata - Jelajah Jawa Timur</title>

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
    <link rel="stylesheet" href="/css/event.css">
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
                <li class="nav-item"><a class="nav-link" href="index.php#destinasi">Destinasi</a></li>
                <li class="nav-item"><a class="nav-link active" href="event.php">Kegiatan</a></li>
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
<section class="evt-page-hero">
    <div class="container">
        <div class="evt-breadcrumb mb-2">
            <a href="index.php">Beranda</a> <span class="text-white-50">/</span> <span class="text-white">Kegiatan Wisata</span>
        </div>
        <h1>Kegiatan Wisata</h1>
        <p>Temukan dan ikuti berbagai festival, konser, dan acara budaya di Jawa Timur</p>
    </div>
</section>

<!-- ===================== LIST EVENT ===================== -->
<section id="evt-list">
    <div class="container">

        <div class="evt-search-wrap">
            <div class="evt-search-box">
                <svg class="evt-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="evtSearchInput" class="evt-search-input" placeholder="Cari event wisata...">
                <button type="button" class="evt-search-clear" id="evtSearchClear" style="display:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="evt-filter-wrap">
            <button type="button" class="evt-filter-btn active" data-kategori="semua"><i class="bi bi-grid-fill"></i> Semua</button>
            <button type="button" class="evt-filter-btn" data-kategori="Festival"><i class="bi bi-stars"></i> Festival</button>
            <button type="button" class="evt-filter-btn" data-kategori="Konser"><i class="bi bi-music-note-beamed"></i> Konser</button>
            <button type="button" class="evt-filter-btn" data-kategori="Budaya"><i class="bi bi-building-fill"></i> Budaya</button>
            <button type="button" class="evt-filter-btn" data-kategori="Olahraga"><i class="bi bi-trophy-fill"></i> Olahraga</button>
            <button type="button" class="evt-filter-btn" data-kategori="Pameran"><i class="bi bi-easel-fill"></i> Pameran</button>
        </div>

        <div id="evtHasilLabel" class="evt-hasil-label" style="display:none;"></div>

        <div class="evt-grid" id="evtGrid">
            <div class="text-center w-100 py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted mt-2">Memuat event...</div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
(function () {
    'use strict';

    const BASE_URL   = '/api/';
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

    const BADGE_COLOR = {
        Festival: '#E85D04',
        Konser:   '#7B2CBF',
        Budaya:   '#D62828',
        Olahraga: '#0077B6',
        Pameran:  '#2d6a4f'
    };

    let allEvent      = [];
    let activeKategori = 'semua';
    let activeKeyword  = '';

    function escHtml(str) {
        return $('<span>').text(String(str || '')).html();
    }

    function getFotoUrlEvent(foto) {
        if (!foto) return '/assets/placeholder.jpg';
        if (/^https?:\/\//.test(foto)) return foto;
        return BASE_URL + '../uploads/event/' + foto;
    }

    function formatTanggal(tgl) {
        if (!tgl) return '-';
        const d = new Date(tgl);
        if (isNaN(d.getTime())) return tgl;
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
    }

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
       RENDER EVENT GRID
    ============================================================ */
    function renderEvent(data) {
        const grid = $('#evtGrid');
        grid.empty();

        if (data.length === 0) {
            grid.html(`
                <div class="evt-empty text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size:48px;color:#ccc;display:block;margin-bottom:12px;"></i>
                    <p class="text-muted mb-0">Tidak ada event yang ditemukan.</p>
                </div>`);
            return;
        }

        data.forEach(item => {
            const fotoUrl = getFotoUrlEvent(item.mev_foto);
            const warna   = BADGE_COLOR[item.mev_kategori] || '#0f6e56';
            const nama    = escHtml(item.mev_nama_event);
            const lokasi  = escHtml(item.mev_lokasi || '');
            const desk    = escHtml(item.mev_deskripsi || 'Event wisata menarik di Jawa Timur.');
            const kat     = escHtml(item.mev_kategori || '');
            const idEvt   = item.mev_id_event;
            const status  = item.mev_status || 'Aktif';

            let statusBadge = '';
            let btnDisabled = '';
            if (status === 'Selesai') {
                statusBadge = '<span class="evt-badge-selesai">Selesai</span>';
                btnDisabled = 'disabled';
            } else if (status === 'Batal') {
                statusBadge = '<span class="evt-badge-batal">Dibatalkan</span>';
                btnDisabled = 'disabled';
            }

            grid.append(`
                <div class="evt-card">
                    <div class="evt-img">
                        <img src="${fotoUrl}" alt="${nama}" loading="lazy"
                             onerror="this.onerror=null;this.src='/assets/placeholder.jpg'">
                        <span class="evt-badge" style="background:${warna};">${kat}</span>
                        ${statusBadge}
                    </div>
                    <div class="evt-body">
                        <div class="evt-date">
                            <i class="bi bi-calendar-event"></i> ${formatTanggal(item.mev_tanggal_event)}
                        </div>
                        <h3>${nama}</h3>
                        ${lokasi ? `
                        <div class="evt-loc">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            ${lokasi}
                        </div>` : ''}
                        <p>${desk}</p>
                        <button type="button" class="btn evt-btn" ${btnDisabled}
                                data-event-id="${idEvt}"
                                data-event-nama="${nama}">
                            ${status === 'Selesai' ? 'Event Selesai' : (status === 'Batal' ? 'Event Dibatalkan' : 'Daftar Sekarang')}
                        </button>
                    </div>
                </div>`);
        });
    }

    function applyFilter() {
        const keyword = activeKeyword.toLowerCase().trim();
        const kat     = activeKategori;

        const filtered = allEvent.filter(e => {
            const matchKat = kat === 'semua' || e.mev_kategori === kat;
            const matchKey = !keyword || [
                e.mev_nama_event,
                e.mev_lokasi,
                e.mev_deskripsi
            ].some(field => (field || '').toLowerCase().includes(keyword));
            return matchKat && matchKey;
        });

        const label = $('#evtHasilLabel');
        if (keyword || kat !== 'semua') {
            const total = filtered.length;
            let txt;
            if (keyword && kat !== 'semua') {
                txt = `Menampilkan <strong>${total}</strong> hasil untuk "<em>${escHtml(activeKeyword)}</em>" di kategori <strong>${escHtml(kat)}</strong>`;
            } else if (keyword) {
                txt = `Menampilkan <strong>${total}</strong> hasil untuk "<em>${escHtml(activeKeyword)}</em>"`;
            } else {
                txt = `Menampilkan kategori <strong>${escHtml(kat)}</strong> &mdash; <strong>${total}</strong> event`;
            }
            label.html(txt).show();
        } else {
            label.hide();
        }

        renderEvent(filtered);
    }

    function loadEvent() {
        $.ajax({
            url: BASE_URL + 'proses/proses_event.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'baca' },
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    allEvent = res.data;
                    applyFilter();
                } else {
                    $('#evtGrid').html('<p class="text-center text-muted w-100 py-5">Belum ada event tersedia.</p>');
                }
            },
            error: function (xhr) {
                $('#evtGrid').html('<p class="text-center text-danger w-100 py-5">Gagal memuat event. Silakan refresh halaman.</p>');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    /* ============================================================
       MODAL PESAN TIKET
    ============================================================ */
    window.bukaModalTiket = function (id, nama) {
        $('#namaEventModal').text(nama);
        $('#idEventModal').val(id);
        $('#jumlahTiketInput').val(1);
        $('input[name="metode_bayar"]').prop('checked', false);
        $('#ringkasanTotal').addClass('d-none');

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPesanTiket')).show();
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

    function bindEventGuards() {
        $(document).on('click', '.evt-btn', function (e) {
            if ($(this).is(':disabled')) return;

            if (!isLoggedIn) {
                e.preventDefault();
                e.stopPropagation();
                cekLogin();
            } else {
                const eventId = $(this).data('event-id');
                const eventName = $(this).data('event-nama');
                bukaModalTiket(eventId, eventName);
            }
        });
    }

    function bindFilters() {
        $(document).on('click', '.evt-filter-btn', function () {
            $('.evt-filter-btn').removeClass('active');
            $(this).addClass('active');
            activeKategori = $(this).data('kategori');
            applyFilter();
        });

        let searchTimer = null;
        $('#evtSearchInput').on('input', function () {
            const val = $(this).val();
            activeKeyword = val;
            $('#evtSearchClear').toggle(val.length > 0);

            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilter, 300);
        });

        $('#evtSearchClear').on('click', function () {
            $('#evtSearchInput').val('').trigger('input');
        });
    }

    function bindAuthButtons() {
        $('#btnLogin').on('click', () => window.location.href = BASE_URL + 'php/login.php');
        $('#btnRegister').on('click', () => window.location.href = BASE_URL + 'php/register.php');
    }

    $(document).ready(function () {
        initToggleableRadios('input[name="tiket_id"]');
        bindFilters();
        bindEventGuards();
        bindTicketForm();
        bindAuthButtons();
        loadEvent();
    });
})();
</script>

</body>
</html>