<?php
$base_url = '/api/php';
// Mendapatkan nama file dan nama folder yang sedang diakses saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));

// Data user dari session
$user_name     = $_SESSION['nama']     ?? 'Admin';
$user_email    = $_SESSION['email']    ?? 'admin@jelajahjatim.id';
$user_role     = $_SESSION['role']     ?? 'Administrator';
$user_username = $_SESSION['username'] ?? 'admin';
$user_telp     = $_SESSION['no_telp'] ?? '';

// Inisial avatar dari nama depan
$avatar_initial = strtoupper(substr($user_name, 0, 1));

// Pecah nama depan & belakang
$name_parts  = explode(' ', $user_name, 2);
$nama_depan  = $name_parts[0] ?? '';
$nama_belakang = $name_parts[1] ?? '';
?>

<header class="navbar navbar-expand-md navbar-light d-print-none">
  <div class="container-xl">

    <div class="navbar-nav flex-row order-md-last ms-auto align-items-center gap-2">

      <div class="nav-item d-none d-md-flex">
        <a href="#" class="nav-link px-0" title="Notifikasi">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
          </svg>
        </a>
      </div>

      <div class="d-none d-md-flex nav-item">
        <div class="vr my-2 mx-1 opacity-25"></div>
      </div>

      <div class="nav-item dropdown">
        <a href="#"
           class="nav-link d-flex lh-1 text-reset p-0 align-items-center gap-2"
           data-bs-toggle="dropdown"
           aria-label="Buka menu profil"
           aria-expanded="false">
          <span class="avatar avatar-sm rounded-circle bg-blue-lt fw-bold text-blue">
            <?= htmlspecialchars($avatar_initial) ?>
          </span>
          <div class="d-none d-xl-flex flex-column">
            <span class="fw-semibold lh-1" style="font-size: 0.85rem;">
              <?= htmlspecialchars($user_name) ?>
            </span>
            <small class="text-secondary mt-1" style="font-size: 0.72rem; line-height: 1;">
              <?= htmlspecialchars($user_role) ?>
            </small>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-secondary d-none d-xl-block"
            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M6 9l6 6l6 -6" />
          </svg>
        </a>

        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 220px;">

          <div class="dropdown-header py-2">
            <div class="d-flex align-items-center gap-2">
              <span class="avatar avatar-sm rounded-circle bg-blue-lt fw-bold text-blue">
                <?= htmlspecialchars($avatar_initial) ?>
              </span>
              <div>
                <div class="fw-semibold" style="font-size: 0.85rem; line-height: 1.3;">
                  <?= htmlspecialchars($user_name) ?>
                </div>
                <small class="text-secondary" style="font-size: 0.72rem;">
                  <?= htmlspecialchars($user_email) ?>
                </small>
              </div>
            </div>
          </div>

          <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahPassword">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
              <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
              <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
            </svg>
            Ubah Password
          </a>

          <div class="dropdown-divider"></div>

            <a href="<?= $base_url ?>/logout.php" class="dropdown-item text-danger">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon text-danger" width="24" height="24"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
              <path d="M9 12h12l-3 -3" />
              <path d="M18 15l3 -3" />
            </svg>
            Logout
          </a>
        </div>
      </div>
      </div>
    </div>
</header>
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <h1 class="navbar-brand">
      <a href="<?= $base_url ?>/dashboard_admin.php">
        <img src="<?= $base_url ?>/../../../assets/logo.png" height="32" alt="Jelajah Jawa Timur">
      </a>
    </h1>

    <div class="collapse navbar-collapse" id="sidebar-menu">
      <ul class="navbar-nav pt-lg-3">

        <li class="nav-item <?= ($current_page == 'dashboard_admin.php') ? 'active' : '' ?>">
          <a class="nav-link" href="<?= $base_url ?>/dashboard_admin.php">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
              </svg>
            </span>
            <span class="nav-link-title">Dashboard</span>
          </a>
        </li>

        <li class="nav-item dropdown <?= ($current_dir == 'kelola') ? 'active' : '' ?>">
          <a class="nav-link dropdown-toggle <?= ($current_dir == 'kelola') ? 'show' : '' ?>"
             href="#navbar-wisata"
             data-bs-toggle="dropdown"
             data-bs-auto-close="false"
             role="button"
             aria-expanded="<?= ($current_dir == 'kelola') ? 'true' : 'false' ?>">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
              </svg>
            </span>
            <span class="nav-link-title">Kelola Wisata</span>
          </a>
          <div class="dropdown-menu <?= ($current_dir == 'kelola') ? 'show' : '' ?>">
            <a class="dropdown-item <?= ($current_page == 'destinasi_wisata.php') ? 'active' : '' ?>"
               href="<?= $base_url ?>/kelola/destinasi_wisata.php">
              Destinasi Wisata
            </a>
            <a class="dropdown-item <?= ($current_page == 'event_main.php') ? 'active' : '' ?>"
               href="<?= $base_url ?>/event/event_main.php">
              Event Wisata
            </a>
          </div>
        </li>

        <li class="nav-item dropdown <?= ($current_dir == 'laporan' || $current_dir == 'review') ? 'active' : '' ?>">
          <a class="nav-link dropdown-toggle <?= ($current_dir == 'laporan' || $current_dir == 'review') ? 'show' : '' ?>"
             href="#navbar-laporan"
             data-bs-toggle="dropdown"
             data-bs-auto-close="false"
             role="button"
             aria-expanded="<?= ($current_dir == 'laporan' || $current_dir == 'review') ? 'true' : 'false' ?>">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                <path d="M9 14h.01" />
                <path d="M9 17h.01" />
                <path d="M12 16l1 1l3 -3" />
              </svg>
            </span>
            <span class="nav-link-title">Laporan Wisata</span>
          </a>
          <div class="dropdown-menu <?= ($current_dir == 'laporan' || $current_dir == 'review') ? 'show' : '' ?>">
            <a class="dropdown-item <?= ($current_page == 'review_main.php') ? 'active' : '' ?>" href="<?= $base_url ?>/laporan/review/review_main.php">Review Wisatawan</a>
          </div>
        </li>

        <li class="nav-item dropdown <?= ($current_dir == 'booking') ? 'active' : '' ?>">
          <a class="nav-link dropdown-toggle <?= ($current_dir == 'booking') ? 'show' : '' ?>"
             href="#navbar-booking"
             data-bs-toggle="dropdown"
             data-bs-auto-close="false"
             role="button"
             aria-expanded="<?= ($current_dir == 'booking') ? 'true' : 'false' ?>">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M15 5l0 2" /><path d="M15 11l0 2" /><path d="M15 17l0 2" />
                <path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2" />
              </svg>
            </span>
            <span class="nav-link-title">Booking Tiket (Event)</span>
          </a>
          <div class="dropdown-menu <?= ($current_dir == 'booking') ? 'show' : '' ?>">
            <a class="dropdown-item <?= ($current_page == 'booking_main.php') ? 'active' : '' ?>"
               href="<?= $base_url ?>/booking/booking_main.php">
              Booking Tiket Event
            </a>
            <a class="dropdown-item <?= ($current_page == 'daftar_pemesanan.php') ? 'active' : '' ?>"
               href="<?= $base_url ?>/daftar_tiket/daftar_tiket.php">
              Daftar Pemesanan Tiket
            </a>
          </div>
        </li>

        <li class="nav-item mt-auto"></li>

        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <i class="ti ti-palette"></i>
            </span>
            <span class="nav-link-title">Pilih Mode</span>
          </a>
          <div class="dropdown-menu">
            <a href="javascript:void(0)" class="dropdown-item theme-btn" data-theme-value="light" onclick="setTheme('light')">
              <i class="ti ti-sun me-2"></i> Terang
            </a>
            <a href="javascript:void(0)" class="dropdown-item theme-btn" data-theme-value="dark" onclick="setTheme('dark')">
              <i class="ti ti-moon me-2"></i> Gelap
            </a>
            <a href="javascript:void(0)" class="dropdown-item theme-btn" data-theme-value="auto" onclick="setTheme('auto')">
              <i class="ti ti-device-desktop me-2"></i> Sistem
            </a>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?= $base_url ?>/logout.php">
            <span class="nav-link-icon d-md-none d-lg-inline-block text-danger">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                <path d="M9 12h12l-3 -3" />
                <path d="M18 15l3 -3" />
              </svg>
            </span>
            <span class="nav-link-title text-danger">Logout</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</aside>
<div class="modal modal-blur fade" id="modalEditAkun" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">


      <form action="<?= $base_url ?>/akun/update_akun.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <span class="avatar avatar-xl rounded-circle bg-blue-lt fw-bold text-blue fs-2">
              <?= htmlspecialchars($avatar_initial) ?>
            </span>
            <div>
              <div class="fw-semibold mb-1">Foto Profil</div>
              <div class="text-secondary small mb-2">Format JPG atau PNG, maks. 2MB</div>
              <label class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                  <path d="M7 9l5 -5l5 5" />
                  <path d="M12 4l0 12" />
                </svg>
                Upload Foto
                <input type="file" name="foto_profil" class="d-none" accept="image/jpeg,image/png">
              </label>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label required">Nama Depan</label>
              <input type="text"
                     name="nama_depan"
                     class="form-control"
                     value="<?= htmlspecialchars($nama_depan) ?>"
                     placeholder="Nama depan"
                     required>
            </div>
            <div class="col-6">
              <label class="form-label">Nama Belakang</label>
              <input type="text"
                     name="nama_belakang"
                     class="form-control"
                     value="<?= htmlspecialchars($nama_belakang) ?>"
                     placeholder="Nama belakang">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Email</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                  <path d="M3 7l9 6l9 -6" />
                </svg>
              </span>
              <input type="email"
                     name="email"
                     class="form-control"
                     value="<?= htmlspecialchars($user_email) ?>"
                     placeholder="email@example.com"
                     required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Username</label>
            <div class="input-group">
              <span class="input-group-text">@</span>
              <input type="text"
                     name="username"
                     class="form-control"
                     value="<?= htmlspecialchars($user_username) ?>"
                     placeholder="username"
                     required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                </svg>
              </span>
              <input type="text"
                     name="no_telp"
                     class="form-control"
                     value="<?= htmlspecialchars($user_telp) ?>"
                     placeholder="+62 8xx-xxxx-xxxx">
            </div>
          </div>

          <div class="alert alert-info alert-dismissible mb-0" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
              <path d="M12 9h.01" /><path d="M11 12h1v4h1" />
            </svg>
            <div>Untuk mengubah password, gunakan menu <strong>Ubah Password</strong>.</div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">
            Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
              <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
              <path d="M14 4l0 4l-6 0l0 -4" />
            </svg>
            Simpan Perubahan
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modalUbahPassword" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="24" height="24"
            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
            <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
            <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
          </svg>
          Ubah Password
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <form action="<?= $base_url ?>/akun/update_password.php" method="POST">
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label required">Password Saat Ini</label>
            <input type="password"
                   name="password_lama"
                   class="form-control"
                   placeholder="Masukkan password lama"
                   required>
          </div>

          <div class="mb-3">
            <label class="form-label required">Password Baru</label>
            <input type="password"
                   name="password_baru"
                   class="form-control"
                   placeholder="Min. 8 karakter"
                   minlength="8"
                   required>
          </div>

          <div class="mb-0">
            <label class="form-label required">Konfirmasi Password Baru</label>
            <input type="password"
                   name="konfirmasi_password"
                   class="form-control"
                   placeholder="Ulangi password baru"
                   minlength="8"
                   required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">
            Batal
          </button>
          <button type="submit" class="btn btn-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
              <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
              <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
            </svg>
            Ubah Password
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<script>
  const themeStorageKey = "tablerTheme";
  const defaultTheme    = "auto";

  function setTheme(theme) {
    if (theme === 'auto') {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.body.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
    } else {
      document.body.setAttribute('data-bs-theme', theme);
    }
    localStorage.setItem(themeStorageKey, theme);

    document.querySelectorAll('.theme-btn').forEach(btn => {
      btn.classList.remove('active');
      if (btn.getAttribute('data-theme-value') === theme) {
        btn.classList.add('active');
      }
    });
  }

  // Terapkan tema tersimpan saat halaman load
  const savedTheme = localStorage.getItem(themeStorageKey) || defaultTheme;
  setTheme(savedTheme);
</script>