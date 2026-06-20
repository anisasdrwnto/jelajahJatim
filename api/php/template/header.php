<?php
$base_url = '/api/php';
// Mendapatkan nama file dan nama folder yang sedang diakses saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
?>

<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <h1 class="navbar-brand">
      <a href="<?= $base_url ?>/dashboard_admin.php">Jelajah Wisata</a>
    </h1>
    
    <div class="collapse navbar-collapse" id="sidebar-menu">
      <ul class="navbar-nav pt-lg-3">
        
        <li class="nav-item <?= ($current_page == 'dashboard_admin.php') ? 'active' : '' ?>">
          <a class="nav-link" href="<?= $base_url ?>/dashboard_admin.php">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
            </span>
            <span class="nav-link-title">Dashboard</span>
          </a>
        </li>

        <li class="nav-item dropdown <?= ($current_dir == 'kelola') ? 'active' : '' ?>">
          <a class="nav-link dropdown-toggle <?= ($current_dir == 'kelola') ? 'show' : '' ?>" href="#navbar-wisata" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= ($current_dir == 'kelola') ? 'true' : 'false' ?>">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
            </span>
            <span class="nav-link-title">Kelola Wisata</span>
          </a>
          <div class="dropdown-menu <?= ($current_dir == 'kelola') ? 'show' : '' ?>">
            <a class="dropdown-item <?= ($current_page == 'destinasi_wisata.php') ? 'active' : '' ?>" href="<?= $base_url ?>/kelola/destinasi_wisata.php">Destinasi Wisata</a>
            <a class="dropdown-item <?= ($current_page == 'event_wisata.php') ? 'active' : '' ?>" href="#">Event Wisata</a>
          </div>
        </li>

        <li class="nav-item dropdown <?= ($current_dir == 'laporan') ? 'active' : '' ?>">
          <a class="nav-link dropdown-toggle <?= ($current_dir == 'laporan') ? 'show' : '' ?>" href="#navbar-laporan" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= ($current_dir == 'laporan') ? 'true' : 'false' ?>">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 14h.01" /><path d="M9 17h.01" /><path d="M12 16l1 1l3 -3" /></svg>
            </span>
            <span class="nav-link-title">Laporan Wisata</span>
          </a>
          <div class="dropdown-menu <?= ($current_dir == 'laporan') ? 'show' : '' ?>">
            <a class="dropdown-item" href="#">Review Wisatawan</a>
            <a class="dropdown-item" href="#">Statistik Laporan</a>
          </div>
        </li>

        <li class="nav-item <?= ($current_page == 'booking.php') ? 'active' : '' ?>">
          <a class="nav-link" href="#">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 5l0 2" /><path d="M15 11l0 2" /><path d="M15 17l0 2" /><path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2" /></svg>
            </span>
            <span class="nav-link-title">Booking Tiket (Event)</span>
          </a>
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
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
            </span>
            <span class="nav-link-title text-danger">Logout</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</aside>

<script>
  const themeStorageKey = "tablerTheme";
  const defaultTheme = "auto";
  
  function setTheme(theme) {
    // 1. Ubah warna sistem
    if (theme === 'auto') {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.body.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
    } else {
      document.body.setAttribute('data-bs-theme', theme);
    }
    localStorage.setItem(themeStorageKey, theme);

    // 2. Tandai pilihan menu dropdown yang aktif
    const themeBtns = document.querySelectorAll('.theme-btn');
    themeBtns.forEach(btn => {
      // Hapus class active dari semua tombol
      btn.classList.remove('active');
      // Tambahkan class active hanya pada tombol yang dipilih
      if (btn.getAttribute('data-theme-value') === theme) {
        btn.classList.add('active');
      }
    });
  }
  
  // Set tema dan tandai menu saat halaman pertama kali diload
  const savedTheme = localStorage.getItem(themeStorageKey) || defaultTheme;
  setTheme(savedTheme);
</script>