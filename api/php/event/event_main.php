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
  <script src="../../../js/destinasi.js"></script>
</body>
</html>
