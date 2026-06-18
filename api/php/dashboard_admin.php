<?php
session_start();
// Cek session login di sini
// if (!isset($_SESSION['user'])) { header("Location: ../../login.php"); exit; }
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
          
          <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Total Wisata</div>
                  <div class="h1 mb-0">128</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Total User</div>
                  <div class="h1 mb-0">540</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Booking Sukses</div>
                  <div class="h1 mb-0">1,240</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="subheader">Ulasan Baru</div>
                  <div class="h1 mb-0">86</div>
                </div>
              </div>
            </div>
          </div>

          <div class="row row-cards">
            
            <div class="col-lg-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Tren Kunjungan Wisatawan Mancanegara (Jawa Timur)</h3>
                </div>
                <div class="card-body">
                  <div id="chart-trend" style="min-height: 300px;"></div>
                </div>
              </div>
            </div>

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

    </div>
  </div>

  <script src="../../assets/tabler/dist/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../../assets/tabler/dist/js/tabler.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      
      const initialTheme = document.body.getAttribute("data-bs-theme") === "dark" ? "dark" : "light";

      const datasetTrenJatim = {
        tahun: ['2018', '2019', '2020', '2021', '2022', '2023'],
        kunjungan: [320.5, 243.8, 35.1, 0.7, 56.4, 178.2] 
      };

      let chartTrend, chartProporsi;

      const chartTrendElement = document.getElementById('chart-trend');
      if (chartTrendElement) {
          chartTrend = new ApexCharts(chartTrendElement, {
            theme: { mode: initialTheme },
            chart: {
              type: 'area',
              height: 300,
              fontFamily: 'inherit',
              toolbar: { show: false },
              background: 'transparent'
            },
            series: [{
              name: 'Wisatawan Asing (Ribu Orang)',
              data: datasetTrenJatim.kunjungan
            }],
            xaxis: {
              categories: datasetTrenJatim.tahun,
              tooltip: { enabled: false }
            },
            colors: ['#206bc4'],
            dataLabels: { enabled: false },
            stroke: { width: 2, curve: 'smooth' },
            fill: {
              type: 'gradient',
              gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
              }
            }
          });
          chartTrend.render();
      }

      const datasetKategoriJatim = {
        labels: ['Wisata Alam (Bromo, Ijen, dll)', 'Taman Hiburan (Jatim Park, dll)', 'Wisata Religi (Wali Songo)', 'Budaya & Sejarah (Trowulan, dll)'],
        series: [40, 30, 18, 12] 
      };

      const chartProporsiElement = document.getElementById('chart-proporsi');
      if (chartProporsiElement) {
          chartProporsi = new ApexCharts(chartProporsiElement, {
            theme: { mode: initialTheme }, 
            chart: {
              type: 'donut',
              height: 300,
              fontFamily: 'inherit',
              background: 'transparent'
            },
            series: datasetKategoriJatim.series,
            labels: datasetKategoriJatim.labels,
            colors: ['#206bc4', '#4299e1', '#48b0f7', '#a8d2fa'],
            stroke: {
              colors: ['transparent'] 
            },
            tooltip: {
              y: {
                formatter: function (val) {
                  return val + "%"
                }
              }
            },
            legend: {
              position: 'bottom'
            }
          });
          chartProporsi.render();
      }

      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.attributeName === "data-bs-theme") {
            const currentTheme = document.body.getAttribute("data-bs-theme") === "dark" ? "dark" : "light";
            if (chartTrend) chartTrend.updateOptions({ theme: { mode: currentTheme } });
            if (chartProporsi) chartProporsi.updateOptions({ theme: { mode: currentTheme } });
          }
        });
      });
      observer.observe(document.body, { attributes: true });

    });
  </script>
</body>
</html>