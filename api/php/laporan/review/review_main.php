<?php
session_start();
// Cek session login di sini (sesuaikan path-nya)
// if (!isset($_SESSION['user'])) { header("Location: ../../../login.php"); exit; }
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Review Wisatawan – Jelajah Wisata Jawa Timur</title>

  <link rel="stylesheet" href="../../../../assets/tabler/dist/css/tabler.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css"/>
</head>

<body class="antialiased">
  <div class="page">

    <?php include('../../template/header.php'); ?>

    <div class="page-wrapper">

      <div class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <div class="page-pretitle">Laporan Wisata</div>
              <h2 class="page-title">Review Wisatawan</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="page-body">
        <div class="container-xl">

          <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="avatar bg-primary-lt rounded text-primary"><i class="ti ti-message-2"></i></span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Total Ulasan</div>
                      <div class="h3 mb-0" id="cardTotalUlasan">0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="avatar bg-success-lt rounded text-success"><i class="ti ti-eye"></i></span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Ditampilkan</div>
                      <div class="h3 mb-0" id="cardTampil">0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="avatar bg-secondary-lt rounded text-secondary"><i class="ti ti-eye-off"></i></span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Disembunyikan</div>
                      <div class="h3 mb-0" id="cardSembunyi">0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="avatar bg-warning-lt rounded text-warning"><i class="ti ti-star"></i></span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Rata-rata Rating</div>
                      <div class="h3 mb-0" id="cardRataRating">0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="row w-100 g-2 align-items-center">
                <div class="col-12 col-md-auto">
                  <h3 class="card-title mb-0">Daftar Ulasan</h3>
                </div>
                <div class="col-12 col-md ms-md-auto d-flex justify-content-md-end">
                  <div class="input-group input-group-sm w-auto">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" id="search-ulasan" placeholder="Cari destinasi / user / komentar...">
                  </div>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-vcenter table-hover card-table">
                <thead>
                  <tr>
                    <th class="w-1">Foto</th>
                    <th>Destinasi</th>
                    <th>User</th>
                    <th class="d-none d-md-table-cell">Kategori</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th class="d-none d-sm-table-cell">Tanggal</th>
                    <th>Status</th>
                    <th class="text-end text-nowrap">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbodyUlasan"></tbody>
              </table>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
              <p class="m-0 text-muted small" id="labelHasilSearch" style="display:none;"></p>
            </div>
          </div>

        </div>
      </div>

      <?php include('../../template/footer.php'); ?>
    </div>
  </div>

  <script src="../../../../assets/tabler/dist/js/tabler.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script>
    const BASE_URL = "/api/";
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../../../js/review.js"></script>
</body>
</html>