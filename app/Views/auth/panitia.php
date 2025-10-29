<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Login Panitia</title>
  <!--begin::Primary Meta Tags-->
  <?= $this->include('assets/css'); ?>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="login-page bg-body-secondary">
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header"> <a href="#"
          class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
          <h1 class="mb-0"> <b>Login Panitia</b>
          </h1>
        </a> </div>
      <div class="card-body login-card-body">
        <form action="/Auth/Panitia" method="post">
          <div class="input-group mb-1">
            <div class="form-floating"> <input id="username" type="text" class="form-control" value="" placeholder=""
                name="username">
              <label for="username">Username</label>
            </div>
            <div class="input-group-text"> <span class="fa-solid fa-envelope"></span> </div>
          </div>
          <div class="input-group mb-1">
            <div class="form-floating"> <input id="password" type="password" class="form-control" placeholder=""
                name="password">
              <label for="password">Password</label>
            </div>
            <div class="input-group-text"> <span class="fa-solid fa-key"></span> </div>
          </div>
          <!--begin::Row-->
          <div class="row">
            <div class="col-4">
              <div class="d-grid gap-2"> <button type="submit" class="btn btn-primary">Masuk</button> </div>
            </div> <!-- /.col -->
          </div>
          <!--end::Row-->
        </form>
        <p class="mb-0"> <a href="/" class="text-center">
            kembali ke Halaman Depan
          </a> </p>
      </div> <!-- /.login-card-body -->
    </div>
  </div> <!-- /.login-box -->
  <?= $this->include('assets/js'); ?>
  <!--end::OverlayScrollbars Configure-->
  <!--end::Script-->

  <script>
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