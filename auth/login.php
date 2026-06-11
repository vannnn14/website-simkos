<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: /simkos-web/dashboard/index.php');
    exit;
}

include '../config/koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($q);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id'           => (int)$user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
        ];
        header('Location: /simkos-web/dashboard/index.php');
        exit;
    } else {
        $error = 'Username atau password salah';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIMKOS Login</title>

  <?php include '../components/theme.php'; ?>
</head>

<body class="bg-[#a1a197] dark:bg-[#8f9187] transition duration-500">

  <!-- THEME BUTTON -->
  <button
    onclick="toggleTheme()"
    class="fixed top-5 right-5 z-50 w-12 h-12 rounded-full 
    bg-white/90 dark:bg-black/80 
    backdrop-blur-md
    border border-gray-200 dark:border-[#252525]
    shadow-lg
    flex items-center justify-center
    hover:scale-105 transition duration-300"
  >

    <!-- SUN -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="w-5 h-5 text-black dark:hidden"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"
      />
    </svg>

    <!-- MOON -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="w-5 h-5 text-white hidden dark:block"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M20.354 15.354A9 9 0 018.646 3.646 9 9 0 1020.354 15.354z"
      />
    </svg>

  </button>

  <!-- MAIN -->
  <div class="w-full h-screen flex items-center justify-center p-6">

    <!-- CARD -->
    <div class="w-full max-w-7xl h-[90vh] rounded-[32px] overflow-hidden shadow-2xl flex">

      <!-- LEFT -->
      <div class="hidden lg:block w-1/2 relative">

        <!-- IMAGE -->
        <img
          src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1400&auto=format&fit=crop"
          class="w-full h-full object-cover"
          alt=""
        >

        <!-- OVERLAY -->
        <div class="absolute inset-0 bg-black/45"></div>

        <!-- CONTENT -->
        <div class="absolute inset-0 z-10 p-14 flex flex-col justify-between">

          <!-- LOGO -->
          <div>

            <h1 class="text-white text-6xl font-bold tracking-wide">
              SIMKOS
            </h1>

            <p class="text-gray-300 mt-3 text-lg">
              Sistem Informasi Manajemen Kos
            </p>

          </div>

          <!-- TEXT -->
          <div class="max-w-xl">

            <h2 class="text-white text-6xl leading-tight font-bold mb-6">
              Design your future,<br>
              one blueprint at a time.
            </h2>

            <p class="text-gray-200 text-2xl mb-3">
              Join a modern premium dashboard experience.
            </p>

            <span class="text-gray-300 text-lg">
              Elegant • Modern • Professional
            </span>

          </div>

        </div>

      </div>

      <!-- RIGHT -->
      <div class="w-full lg:w-1/2 bg-white dark:bg-black flex items-center justify-center px-10 lg:px-24 transition duration-500">

        <div class="w-full max-w-md">

          <!-- MOBILE LOGO -->
          <div class="lg:hidden text-center mb-10">

            <h1 class="text-4xl font-bold text-black dark:text-white">
              SIMKOS
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mt-2">
              Sistem Informasi Manajemen Kos
            </p>

          </div>

          <!-- TITLE -->
          <div class="mb-10">

            <h2 class="text-5xl font-bold text-black dark:text-white leading-tight">
              Welcome Back
            </h2>

            <p class="text-gray-500 dark:text-gray-400 mt-4 leading-7 text-base">
              Login to continue accessing your dashboard and management system.
            </p>

          </div>

          <!-- USERNAME -->
          <form method="POST" class="mt-10">

          <?php if ($error): ?>
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-600 text-sm font-medium">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <div class="mb-6">

            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-3">
              Username
            </label>

            <input
              type="text"
              name="username"
              placeholder="Enter your username"
              required
              class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition"
            >

          </div>

          <!-- PASSWORD -->
          <div>

            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-3">
              Password
            </label>

            <div class="relative">
              <input
                type="password"
                name="password"
                id="loginPassword"
                placeholder="••••••••"
                required
                autocomplete="current-password"
                class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition pr-12"
              >
              <button type="button" onclick="togglePassword('loginPassword', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>

          </div>

          <!-- OPTION -->
          <div class="flex items-center justify-between mt-5">

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">

              <input type="checkbox">

              Remember me

            </label>

            <a
              href="#"
              class="text-sm text-black dark:text-white hover:underline"
            >
              Forgot Password?
            </a>

          </div>

          <!-- BUTTON -->
            <button
            type="submit"
            class="w-full h-14 rounded-2xl bg-black dark:bg-[#cfd7b0] text-white dark:text-black font-semibold mt-8 hover:scale-[1.01] transition duration-300"
            >
            Login to Dashboard
            </button>

          <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
  Don't have an account?
  <a href="register.php" class="text-black dark:text-white font-semibold hover:underline">
    Register
  </a>
</div>

          </form>

          <!-- FOOTER -->
          <div class="mt-10 text-center text-sm text-gray-500 dark:text-gray-600">

            © 2026 SIMKOS. All rights reserved.

          </div>

        </div>

      </div>

    </div>

  </div>

</body>
</html>