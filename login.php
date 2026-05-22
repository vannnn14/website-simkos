<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login SIMKOS</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #F5F7F6;
    }

    .login-card {
      border-radius: 20px;
      padding: 30px;
    }

    .hero {
      background-color: #0F6E56;
      border-radius: 20px;
      padding: 30px;
      color: white;
      text-align: center;
    }
  </style>
</head>

<body>

<div class="container d-flex align-items-center justify-content-center vh-100">

  <div class="row w-100" style="max-width: 900px;">

    <!-- kiri -->
    <div class="col-md-6 mb-3">
      <div class="hero">
        <h3 class="fw-bold">SIMKOS</h3>
        <p>Wisma Al Rasyid</p>
      </div>
    </div>

    <!-- kanan -->
    <div class="col-md-6">
      <div class="card login-card shadow-sm">

        <h5 class="fw-bold mb-3">Login</h5>

        <form action="index.php" method="post">

          <div class="mb-3">
            <label class="form-label">Nomor WhatsApp</label>
            <input type="text" class="form-control" placeholder="08xxxxxxxxxx">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" placeholder="••••••">
          </div>

          <button class="btn btn-success w-100">
            Masuk
          </button>

        </form>

      </div>
    </div>

  </div>

</div>

</body>
</html>