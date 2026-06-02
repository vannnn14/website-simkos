<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIMKOS Register</title>

  <script src="https://cdn.tailwindcss.com"></script>

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
      <div class="w-full lg:w-1/2 bg-white dark:bg-black flex items-center justify-center px-10 lg:px-20">

        <div class="w-full max-w-md">

          <!-- MOBILE -->
          <div class="lg:hidden text-center mb-8">
            <h1 class="text-3xl font-bold text-black dark:text-white">SIMKOS</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">
              Sistem Informasi Manajemen Kos
            </p>
          </div>

          <!-- TITLE -->
          <div class="mb-8">
            <h2 class="text-4xl font-bold text-black dark:text-white">
              Create Account
            </h2>

            <p class="text-gray-500 dark:text-gray-400 mt-3 text-sm">
              Register to access your dashboard.
            </p>
          </div>

          <!-- INPUTS -->
          <div class="space-y-4">

            <input type="text" placeholder="Full Name"
              class="w-full h-12 rounded-xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-4 text-sm text-black dark:text-white focus:ring-2 focus:ring-[#cfd7b0]/20 outline-none">

            <input type="text" placeholder="Username"
              class="w-full h-12 rounded-xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-4 text-sm text-black dark:text-white focus:ring-2 focus:ring-[#cfd7b0]/20 outline-none">

            <input type="email" placeholder="Email"
              class="w-full h-12 rounded-xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-4 text-sm text-black dark:text-white focus:ring-2 focus:ring-[#cfd7b0]/20 outline-none">

            <input type="password" placeholder="Password"
              class="w-full h-12 rounded-xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-4 text-sm text-black dark:text-white focus:ring-2 focus:ring-[#cfd7b0]/20 outline-none">

            <input type="password" placeholder="Confirm Password"
              class="w-full h-12 rounded-xl border border-gray-300 dark:border-[#252525] bg-gray-100 dark:bg-[#0d0d0d] px-4 text-sm text-black dark:text-white focus:ring-2 focus:ring-[#cfd7b0]/20 outline-none">

          </div>

          <!-- CHECKBOX -->
          <div class="flex items-center gap-2 mt-4 text-sm text-gray-600 dark:text-gray-400">
            <input type="checkbox">
            I agree to Terms & Privacy
          </div>

          <!-- BUTTON -->
          <button class="w-full h-12 rounded-xl bg-black dark:bg-[#cfd7b0] text-white dark:text-black font-semibold mt-6">
            Create Account
          </button>

          <!-- LOGIN -->
          <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Already have an account?
            <a href="login.php" class="text-black dark:text-white font-semibold hover:underline">
              Login
            </a>
          </div>

        </div>

      </div>

    </div>

  </div>

  <script>
    function toggleTheme(){
      document.documentElement.classList.toggle('dark')
    }
  </script>

</body>
</html>