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
  <title>Destinasi Wisata – Jelajah Wisata Jawa Timur</title>
  
  <link rel="stylesheet" href="../../../assets/tabler/dist/css/tabler.min.css">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css"/>
</head>

<body class="antialiased">
  <div class="page">
    
    <?php include('../template/header.php'); ?>

    <div class="page-wrapper">
      
      <div class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <div class="page-pretitle">Kelola Wisata</div>
              <h2 class="page-title">
                Destinasi Wisata
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
                        <i class="ti ti-map-pin"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Total Destinasi</div>
                      <div class="h3 mb-0">24</div>
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
                        <i class="ti ti-eye"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Aktif</div>
                      <div class="h3 mb-0">20</div>
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
                      <span class="avatar bg-warning-lt rounded text-warning">
                        <i class="ti ti-eye-off"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Nonaktif</div>
                      <div class="h3 mb-0">4</div>
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
                        <i class="ti ti-category"></i>
                      </span>
                    </div>
                    <div class="col">
                      <div class="text-muted small">Kategori</div>
                      <div class="h3 mb-0">5</div>
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
                  <h3 class="card-title mb-0">Daftar Destinasi</h3>
                  <select class="form-select form-select-sm w-auto" id="filter-kategori">
                    <option value="">Semua Kategori</option>
                    <option value="alam">Alam</option>
                    <option value="budaya">Budaya</option>
                    <option value="kuliner">Kuliner</option>
                    <option value="sejarah">Sejarah</option>
                    <option value="religi">Religi</option>
                  </select>
                  <select class="form-select form-select-sm w-auto" id="filter-status">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                  </select>
                </div>

                <div class="col-12 col-md ms-md-auto d-flex align-items-center gap-2 justify-content-md-end flex-wrap">
                  <div class="input-group input-group-sm w-auto">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" id="search-destinasi" placeholder="Cari destinasi..."/>
                  </div>
                  <button type="button" class="btn btn-primary btn-sm" id="btn-tambah-destinasi" data-bs-toggle="modal" data-bs-target="#modalDestinasi">
                    <i class="ti ti-plus me-1"></i>Tambah Destinasi
                  </button>
                </div>

              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-vcenter table-hover card-table">
                <thead>
                  <tr>
                    <th class="w-1">#</th>
                    <th class="w-1">Foto</th>
                    <th>Nama Destinasi</th>
                    <th class="d-none d-lg-table-cell">Lokasi</th>
                    <th class="d-none d-md-table-cell">Kategori</th>
                    <th class="d-none d-sm-table-cell">Harga Tiket</th>
                    <th>Status</th>
                    <th class="text-end text-nowrap">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tbody-destinasi">
                  <tr data-id="1">
                    <td class="text-muted">1</td>
                    <td><span class="avatar avatar-sm bg-blue-lt text-blue rounded"><i class="ti ti-photo"></i></span></td>
                    <td><div class="fw-medium">Kawah Ijen</div><div class="text-muted small">Blue fire yang memukau</div></td>
                    <td class="text-muted d-none d-lg-table-cell">Banyuwangi</td>
                    <td class="d-none d-md-table-cell"><span class="badge bg-blue-lt text-blue">Alam</span></td>
                    <td class="text-muted d-none d-sm-table-cell">Rp 5.000</td>
                    <td><span class="badge bg-success-lt text-success">Aktif</span></td>
                    <td class="text-end text-nowrap">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info btn-detail" data-id="1" title="Detail"><i class="ti ti-eye"></i></button>
                        <button class="btn btn-outline-warning btn-edit" data-id="1" title="Edit" data-bs-toggle="modal" data-bs-target="#modalDestinasi"><i class="ti ti-edit"></i></button>
                        <button class="btn btn-outline-danger btn-hapus" data-id="1" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalHapus"><i class="ti ti-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr data-id="2">
                    <td class="text-muted">2</td>
                    <td><span class="avatar avatar-sm bg-blue-lt text-blue rounded"><i class="ti ti-photo"></i></span></td>
                    <td><div class="fw-medium">Gunung Bromo</div><div class="text-muted small">Sunrise terbaik Jawa Timur</div></td>
                    <td class="text-muted d-none d-lg-table-cell">Probolinggo</td>
                    <td class="d-none d-md-table-cell"><span class="badge bg-blue-lt text-blue">Alam</span></td>
                    <td class="text-muted d-none d-sm-table-cell">Rp 29.000</td>
                    <td><span class="badge bg-success-lt text-success">Aktif</span></td>
                    <td class="text-end text-nowrap">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info btn-detail" data-id="2" title="Detail"><i class="ti ti-eye"></i></button>
                        <button class="btn btn-outline-warning btn-edit" data-id="2" title="Edit" data-bs-toggle="modal" data-bs-target="#modalDestinasi"><i class="ti ti-edit"></i></button>
                        <button class="btn btn-outline-danger btn-hapus" data-id="2" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalHapus"><i class="ti ti-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
              <p class="m-0 text-muted small">
                Menampilkan <strong>1–5</strong> dari <strong>24</strong> destinasi
              </p>
              <ul class="pagination m-0">
                <li class="page-item disabled"><a class="page-link" href="#"><i class="ti ti-chevron-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">4</a></li>
                <li class="page-item"><a class="page-link" href="#">5</a></li>
                <li class="page-item"><a class="page-link" href="#"><i class="ti ti-chevron-right"></i></a></li>
              </ul>
            </div>

          </div>
          </div>
      </div>
      <footer class="footer footer-transparent d-print-none">
        <div class="container-xl">
          <div class="row text-center">
            <div class="col-12">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item">© 2025 Jelajah Wisata Jawa Timur</li>
                <li class="list-inline-item"><a href="#" class="link-secondary">Bantuan</a></li>
              </ul>
            </div>
          </div>
        </div>
      </footer>

    </div>
  </div>

  <script src="../../../assets/tabler/dist/js/tabler.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

</body>
</html>