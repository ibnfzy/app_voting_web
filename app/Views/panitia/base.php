<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>E-Voting Panel Panitia</title>
  <!--begin::Primary Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="title" content="Website Pemilihan Kepala Desa">
  <meta name="author" content="JULTDEV">
  <meta name="description" content="Website pemilihan kepala desa">
  <?= $this->include('assets/css'); ?>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <?= $this->include('panitia/layouts/navbar'); ?>
    <!--end::Header-->
    <!--begin::Sidebar-->
    <?= $this->include('panitia/layouts/sidebar'); ?>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <main class="app-main">

      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid mt-3">
          <!--begin::Row-->
          <?= $this->renderSection('content'); ?>
          <!--end::Row-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <footer class="app-footer">
      <!--begin::To the end-->
      <div class="float-end d-none d-sm-inline">E - Voting</div>
      <!--end::To the end-->
      <!--begin::Copyright--> <strong>
        JULTDEV
      </strong>
      All rights reserved.
      <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <?= $this->include('assets/js'); ?>

  <?= $this->renderSection('script'); ?>

  <script>
    new DataTable('.datatables');
    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": true,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": true,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
  </script>

  <?php
  if (session()->getFlashdata('dataMessage')) {
    foreach (session()->getFlashdata('dataMessage') as $item) {
      echo '<script>toastr["' .
        session()->getFlashdata('type-status') . '"]("' . $item . '")</script>';
    }
  }
  if (session()->getFlashdata('message')) {
    echo '<script>toastr["' .
      session()->getFlashdata('type-status') . '"]("' . session()->getFlashdata('message') . '")</script>';
  }
  ?>
</body>
<!--end::Body-->

</html>