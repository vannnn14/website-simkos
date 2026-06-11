<!-- TOPBAR -->
<div class="flex items-center justify-between mb-8">

  <!-- LEFT -->
  <div>

    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
      Dashboard
    </h1>

    <p class="text-gray-500 dark:text-gray-400 mt-2">
      Selamat datang kembali admin SIMKOS
    </p>

  </div>

  <!-- RIGHT -->
  <div class="flex items-center gap-4">

    <!-- SEARCH -->
    <input
      type="text"
      placeholder="Cari..."
      class="h-12 w-72 px-5 rounded-2xl
      bg-white dark:bg-[#111]
      border border-gray-200 dark:border-[#1f1f1f]
      outline-none
      text-gray-800 dark:text-white"
    >

    <!-- THEME -->
    <button onclick="toggleTheme()"
      class="w-12 h-12 rounded-2xl
      bg-white dark:bg-[#111]
      border border-gray-200 dark:border-[#1f1f1f]
      flex items-center justify-center">

      <svg class="w-5 h-5 text-gray-600 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9 9 0 1020.354 15.354z"/>
      </svg>

      <svg class="w-5 h-5 text-gray-300 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/>
      </svg>

    </button>

    <!-- LOGOUT -->
    <a href="/simkos-web/auth/logout.php"
      class="h-12 px-4 rounded-2xl flex items-center gap-2
      bg-white dark:bg-[#111]
      border border-gray-200 dark:border-[#1f1f1f]
      text-sm text-gray-600 dark:text-gray-400 hover:text-red-500 transition">

      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
      </svg>
      Logout
    </a>

  </div>

</div>