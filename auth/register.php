<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: /simkos-web/dashboard/index.php');
    exit;
}

include '../config/koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap    = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username        = mysqli_real_escape_string($conn, $_POST['username']);
    $password        = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($nama_lengkap) || empty($username) || empty($password)) {
        $error = 'Semua field harus diisi';
    } elseif ($password !== $password_confirm) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 4) {
        $error = 'Password minimal 4 karakter';
    } else {
        $q = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_fetch_assoc($q)) {
            $error = 'Username sudah digunakan';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap) VALUES ('$username', '$hash', '$nama_lengkap')");
            $success = 'Akun berhasil dibuat. Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIMKOS Register</title>

  <?php include '../components/theme.php'; ?>
</head>

<body class="bg-[#a1a197] dark:bg-[#8f9187] transition duration-500">

  <!-- THEME BUTTON -->
  <button onclick="toggleTheme()"
    class="fixed top-5 right-5 z-50 w-12 h-12 rounded-full 
    bg-white/90 dark:bg-black/80 
    backdrop-blur-md border border-gray-200 dark:border-[#252525]
    shadow-lg flex items-center justify-center
    hover:scale-105 transition duration-300">

    <svg class="w-5 h-5 text-black dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/>
    </svg>

    <svg class="w-5 h-5 text-white hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9 9 0 1020.354 15.354z"/>
    </svg>

  </button>

  <!-- MAIN -->
  <div class="w-full h-screen flex items-center justify-center p-6">

    <!-- CARD -->
    <div class="w-full max-w-7xl h-[90vh] rounded-[32px] overflow-hidden shadow-2xl flex">

      <!-- LEFT -->
      <div class="hidden lg:block w-1/2 relative">

        <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?q=80&w=1400&auto=format&fit=crop"
          class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/45"></div>

        <div class="absolute inset-0 z-10 p-14 flex flex-col justify-between">

          <div>
            <h1 class="text-white text-6xl font-bold">SIMKOS</h1>
            <p class="text-gray-300 mt-3 text-lg">Sistem Informasi Manajemen Kos</p>
          </div>

          <div class="max-w-xl">
            <h2 class="text-white text-5xl leading-tight font-bold mb-5">
              Start your journey<br>with SIMKOS.
            </h2>
            <p class="text-gray-200 text-xl mb-2">
              Create account & manage your kos easily.
            </p>
            <span class="text-gray-300 text-base">
              Smart • Elegant • Efficient
            </span>
          </div>

        </div>

      </div>

      <!-- RIGHT -->
      <div class="w-full lg:w-1/2 bg-white dark:bg-black flex items-center justify-center px-10 lg:px-24">

        <div class="w-full max-w-md">

          <!-- MOBILE -->
          <div class="lg:hidden text-center mb-8">
            <h1 class="text-4xl font-bold text-black dark:text-white">SIMKOS</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">
              Sistem Informasi Manajemen Kos
            </p>
          </div>

          <!-- TITLE -->
          <div class="mb-10">
            <h2 class="text-4xl font-bold text-black dark:text-white">
              Create Account
            </h2>

            <p class="text-gray-500 dark:text-gray-400 mt-4 text-sm leading-7">
              Register to access your dashboard.
            </p>
          </div>

          <!-- INPUTS -->
          <form method="POST">

          <?php if ($error): ?>
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-600 text-sm font-medium">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="mb-4 p-3 rounded-xl bg-green-100 text-green-600 text-sm font-medium">
              <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>

          <div class="space-y-5">

            <input type="text" name="nama_lengkap" placeholder="Full Name" required autocomplete="name"
              class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-sm text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition">

            <input type="text" name="username" placeholder="Username" required autocomplete="username"
              class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-sm text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition">

            <div class="relative">
              <input type="password" name="password" id="regPassword" placeholder="Password" required minlength="4" autocomplete="new-password"
                class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-sm text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition pr-12">
              <button type="button" onclick="togglePassword('regPassword', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>

            <div class="relative">
              <input type="password" name="password_confirm" id="regConfirm" placeholder="Confirm Password" required autocomplete="new-password"
                class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-sm text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition pr-12">
              <button type="button" onclick="togglePassword('regConfirm', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>

          </div>

          <!-- BUTTON -->
          <button type="submit" class="w-full h-14 rounded-2xl bg-black dark:bg-[#cfd7b0] text-white dark:text-black font-semibold mt-8 hover:scale-[1.01] transition duration-300">
            Create Account
          </button>

          <!-- LOGIN -->
          <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Already have an account?
            <a href="login.php" class="text-black dark:text-white font-semibold hover:underline">
              Login
            </a>
          </div>

          </form>

        </div>

      </div>

    </div>

  </div>

</body>
</html>