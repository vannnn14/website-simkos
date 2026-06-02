<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIMKOS Login</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class'
    }
  </script>

  <style>
    body{
      font-family:'Inter',sans-serif;
      overflow:hidden;
    }
  </style>
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
          <div class="mb-6">

            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-3">
              Username
            </label>

            <input
              type="text"
              placeholder="Enter your username"
              class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition"
            >

          </div>

          <!-- PASSWORD -->
          <div>

            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-3">
              Password
            </label>

            <input
              type="password"
              placeholder="••••••••"
              class="w-full h-14 rounded-2xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-5 text-black dark:text-white outline-none focus:border-[#cfd7b0] focus:ring-4 focus:ring-[#cfd7b0]/10 transition"
            >

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
            onclick="window.location.href='dashboard/index.php'"
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

          <!-- FOOTER -->
          <div class="mt-10 text-center text-sm text-gray-500 dark:text-gray-600">

            © 2026 SIMKOS. All rights reserved.

          </div>

        </div>

      </div>

    </div>

  </div>

  <!-- SCRIPT -->
  <script>
    function toggleTheme(){
      document.documentElement.classList.toggle('dark')
    }
  </script>

</body>
</html>