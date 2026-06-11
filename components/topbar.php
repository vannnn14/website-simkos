<!-- TOPBAR -->
<div class="flex items-center justify-between mb-8">

  <!-- LEFT -->
  <div class="flex items-center gap-4">

    <!-- HAMBURGER (mobile) -->
    <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100 dark:hover:bg-[#1a1a1a] transition">
      <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <div>
      <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white">
        <?= $pageTitle ?? 'Dashboard' ?>
      </h1>
      <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm lg:text-base">
        Selamat datang kembali, <?= htmlspecialchars($_SESSION['user']['nama_lengkap'] ?? 'admin') ?>
      </p>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="flex items-center gap-3">

    <!-- THEME -->
    <button onclick="toggleTheme()"
      class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl lg:rounded-2xl
      bg-white dark:bg-[#111]
      border border-gray-200 dark:border-[#1f1f1f]
      flex items-center justify-center hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">

      <!-- SUN (shown in light mode) -->
      <svg class="w-5 h-5 text-gray-600 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/>
      </svg>

      <!-- MOON (shown in dark mode) -->
      <svg class="w-5 h-5 text-gray-300 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9 9 0 1020.354 15.354z"/>
      </svg>

    </button>

  </div>

</div>