<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Dashboard Admin - Jelajah Wisata</title>
  <link rel="stylesheet" href="../../assets/tabler/dist/css/tabler.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css"/>
</head>

<body class="antialiased">
  <div class="page">

    <?php include('template/header.php'); ?>

    <div class="page-wrapper">
      <div class="page-header d-print-none">
        <div class="container-xl">
          <div class="row g-2 align-items-center">
            <div class="col">
              <div class="page-pretitle">Ringkasan Sistem</div>
              <h2 class="page-title">Dashboard Admin Jawa Timur</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="page-body">
        <div class="container-xl">

          <!-- Kartu statistik -->
          <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Total Wisata</div>
                  <div class="h1 mb-0" id="cardTotalDestinasi">—</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Total User</div>
                  <div class="h1 mb-0" id="cardTotalUser">—</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Booking Sukses</div>
                  <div class="h1 mb-0" id="cardBookingSukses">—</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Ulasan Baru</div>
                  <div class="h1 mb-0" id="cardUlasan">—</div>
                </div>
              </div>
            </div>
          </div>

          <div class="row row-cards">

            <!-- Chart tren -->
            <div class="col-lg-8">
              <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <div>
                    <h3 class="card-title mb-0" id="judulChart">
                      Tren Kunjungan Wisatawan Mancanegara (Jawa Timur)
                    </h3>
                    <!-- Badge sumber data BPS -->
                    <small class="text-muted" id="sumberData">
                      <i class="ti ti-database me-1"></i>Memuat data...
                    </small>
                  </div>
                  <!-- Indikator loading -->
                  <div id="chartLoading" class="spinner-border spinner-border-sm text-primary" role="status" style="display:none">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
                <div class="card-body">
                  <div id="chart-trend" style="min-height: 300px;"></div>
                </div>
              </div>
            </div>

            <!-- Chart proporsi -->
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Kategori Wisata Populer</h3>
                </div>
                <div class="card-body">
                  <div id="chart-proporsi" style="min-height: 300px;"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <?php include('template/footer.php'); ?>
    </div>
  </div>

  <script src="../../assets/tabler/dist/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../../assets/tabler/dist/js/tabler.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const BASE_URL    = "/api/";
    const initialTheme = document.body.getAttribute("data-bs-theme") === "dark" ? "dark" : "light";

    let chartTrend    = null;
    let chartProporsi = null;
    const mode = 'bulanan';

    function buatChartTrend(labels, values, satuan) {
      const el = document.getElementById('chart-trend');
      if (!el) return;

      const options = {
        theme : { mode: initialTheme },
        chart : {
          type      : 'area',
          height    : 300,
          fontFamily: 'inherit',
          toolbar   : { show: false },
          background: 'transparent',
          animations: { enabled: true, easing: 'easeinout', speed: 500 },
        },
        series: [{
          name: 'Wisatawan Mancanegara',
          data: values,
        }],
        xaxis: {
          categories: labels,
          tooltip   : { enabled: false },
          labels    : {
            rotate: -45,
            style : { fontSize: '11px' },
          },
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              if (val >= 1000) return (val / 1000).toFixed(0) + ' Rb';
              return val.toLocaleString('id-ID');
            }
          }
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val.toLocaleString('id-ID') + ' ' + satuan;
            }
          }
        },
        colors     : ['#206bc4'],
        dataLabels : { enabled: false },
        stroke     : { width: 2, curve: 'smooth' },
        fill: {
          type    : 'gradient',
          gradient: {
            shadeIntensity: 1,
            opacityFrom   : 0.4,
            opacityTo     : 0.05,
            stops         : [0, 90, 100],
          },
        },
      };

      if (chartTrend) {
        chartTrend.updateOptions({
          series: [{ name: 'Wisatawan Mancanegara', data: values }],
          xaxis : { categories: labels },
        });
      } else {
        chartTrend = new ApexCharts(el, options);
        chartTrend.render();
      }
    }


    function loadTrenWisman() {
      $('#chartLoading').show();
      $('#sumberData').text('Memuat data dari BPS...');

      $.ajax({
        url     : BASE_URL + 'proses/proses_dashboard.php',
        type    : 'GET',
        dataType: 'json',
        data    : { action: 'tren_bulanan' },
        success : function(response) {
          $('#chartLoading').hide();

          if (response.success) {
            const d = response.data;

            const fallbackNote = response.is_fallback
              ? ' <span class="badge bg-warning text-dark ms-1">Cache lokal</span>'
              : '';
            $('#sumberData').html(
              `<i class="ti ti-database me-1"></i>` +
              `Sumber: <strong>${d.sumber}</strong>` +
              ` &bull; Diperbarui: ${d.diperbarui}` +
              fallbackNote
            );

            $('#judulChart').text('Kunjungan Wisatawan Mancanegara per Bulan (2024-2026)');

            buatChartTrend(d.label, d.nilai, d.satuan);
          } else {
            $('#sumberData').html('<span class="text-danger">Gagal memuat data</span>');
          }
        },
        error: function() {
          $('#chartLoading').hide();
          $('#sumberData').html('<span class="text-danger">Gagal menghubungi server</span>');
        }
      });
    }

    const datasetKategoriJatim = {
      labels: [
        'Wisata Alam (Bromo, Ijen, dll)',
        'Taman Hiburan (Jatim Park, dll)',
        'Wisata Religi (Wali Songo)',
        'Budaya & Sejarah (Trowulan, dll)'
      ],
      series: [40, 30, 18, 12],
    };

    const elProporsi = document.getElementById('chart-proporsi');
    if (elProporsi) {
      chartProporsi = new ApexCharts(elProporsi, {
        theme : { mode: initialTheme },
        chart : {
          type      : 'donut',
          height    : 300,
          fontFamily: 'inherit',
          background: 'transparent',
        },
        series : datasetKategoriJatim.series,
        labels : datasetKategoriJatim.labels,
        colors : ['#206bc4', '#4299e1', '#48b0f7', '#a8d2fa'],
        stroke : { colors: ['transparent'] },
        tooltip: { y: { formatter: val => val + '%' } },
        legend : { position: 'bottom' },
      });
      chartProporsi.render();
    }


    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.attributeName === "data-bs-theme") {
          const theme = document.body.getAttribute("data-bs-theme") === "dark" ? "dark" : "light";
          if (chartTrend)    chartTrend.updateOptions({ theme: { mode: theme } });
          if (chartProporsi) chartProporsi.updateOptions({ theme: { mode: theme } });
        }
      });
    });
    observer.observe(document.body, { attributes: true });


    function loadCardDestinasi() {
      $.ajax({
        url     : BASE_URL + 'proses/proses_dashboard.php',
        type    : 'GET',
        dataType: 'json',
        data    : { action: 'statistik' },
        success : function(response) {
          if (response.success) {
            const d = response.data;
            $('#cardTotalDestinasi').text(d.total);
            $('#cardTotalUser').text(d.user);
            $('#cardBookingSukses').text(d.booking);
            $('#cardUlasan').text(d.ulasan);
          }
        },
        error: function(xhr) {
          console.error('Gagal load statistik:', xhr.responseText);
        }
      });
    }

    loadCardDestinasi();
    loadTrenWisman();
  });
  </script>
</body>
</html>