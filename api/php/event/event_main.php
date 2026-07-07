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
  <title>Event Wisata – Jelajah Wisata Jawa Timur</title>

  <link rel="stylesheet" href="../../../assets/tabler/dist/css/tabler.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css"/>
</head>

<body class="antialiased">
  <div class="page">

  <!---- Header ---->
    <?php include('../template/header.php'); ?>

    <div class="page-wrapper">

      <div class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <div class="page-pretitle">Kelola Wisata</div>
              <h2 class="page-title">
                Event Wisata
              </h2>
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
                      <span class="avatar bg-primary-lt rounded text-primary">
                        <i class="ti ti-calendar-event"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Total Event</div>
                      <div class="h3 mb-0" id="cardTotalEvent">0</div>
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
                      <span class="avatar bg-success-lt rounded text-success">
                        <i class="ti ti-circle-check"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Aktif</div>
                      <div class="h3 mb-0" id="cardAktif">0</div>
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
                      <span class="avatar bg-info-lt rounded text-info">
                        <i class="ti ti-flag-check"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Selesai</div>
                      <div class="h3 mb-0" id="cardSelesai">0</div>
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
                      <span class="avatar bg-danger-lt rounded text-danger">
                        <i class="ti ti-circle-x"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Batal</div>
                      <div class="h3 mb-0" id="cardBatal">0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="card">
            <div class="card-header">
              <div class="row w-100 g-2 align-items-center">

                <div class="col-12 col-md-auto d-flex align-items-center gap-2 flex-wrap">
                  <h3 class="card-title mb-0">Daftar Event</h3>
                  <select class="form-select form-select-sm w-auto" id="filter-status">
                    <option value="">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Batal">Batal</option>
                  </select>
                </div>

                <div class="col-12 col-md ms-md-auto d-flex align-items-center gap-2 justify-content-md-end flex-wrap">
                  <div class="input-group input-group-sm w-auto">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" id="search-event" placeholder="Cari event..."/>
                  </div>
                  <button type="button" class="btn btn-primary btn-sm" id="btn-AddEvent" data-bs-toggle="modal" data-bs-target="#modalAddEvent">
                    <i class="ti ti-plus me-1"></i>Tambah Event
                  </button>
                </div>

              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-vcenter table-hover card-table">
                <thead>
                  <tr>
                    <th class="w-1">ID Event</th>
                    <th>Nama Event</th>
                    <th class="d-none d-lg-table-cell">Tanggal</th>
                    <th class="d-none d-lg-table-cell">Waktu</th>
                    <th class="d-none d-lg-table-cell">Lokasi</th>
                    <th class="w-1">Foto</th>
                    <th class="d-none d-md-table-cell">Kategori</th>
                    <th class="d-none d-sm-table-cell">Deskripsi</th>
                    <th>Status</th>
                    <th class="text-end text-nowrap">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbodyEvent"></tbody>
              </table>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
              <p class="m-0 text-muted small" id="labelHasilSearch" style="display:none;"></p>
            </div>

          </div>
        </div>
      </div>

    <?php include 'create_event.php'; ?>
    <?php include 'edit_event.php'; ?>

    <?php include('../template/footer.php'); ?>
</div>
 <script src="../../../assets/tabler/dist/js/tabler.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script>
        const BASE_URL = "/api/";
    </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../../js/event.js"></script>
</body>
</html>
