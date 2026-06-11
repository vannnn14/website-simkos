<?php
$user = $_SESSION['user'] ?? [];
$userName = $user['nama_lengkap'] ?? 'Admin';
$userInitials = '';
foreach (explode(' ', $userName) as $part) {
  $userInitials .= strtoupper(substr($part, 0, 1));
}
$userInitials = substr($userInitials, 0, 2);

function menuClass($menu, $active) {
  $base = 'flex items-center gap-3 p-3 rounded-xl transition duration-200';
  if ($menu == $active) {
    return $base . ' bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium border-l-4 border-blue-600 rounded-r-xl rounded-l-none';
  }
  return $base . ' text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#151515] hover:text-gray-800 dark:hover:text-gray-200 border-l-4 border-transparent rounded-r-xl rounded-l-none';
}
?>

<!-- SIDEBAR OVERLAY (mobile) -->
<div id="sidebarOverlay" onclick="toggleSidebar()" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

<!-- SIDEBAR -->
<div id="sidebar"
  class="w-64 h-screen bg-white dark:bg-[#0a0a0a]
  border-r border-gray-200 dark:border-[#1a1a1a]
  p-5 fixed left-0 top-0 overflow-y-auto z-40
  -translate-x-full lg:translate-x-0 transition-transform duration-300">

  <!-- LOGO + CLOSE (mobile) -->
  <div class="flex items-center justify-between mb-8">

    <div>
      <h1 class="text-xl font-bold text-blue-600 tracking-tight">
        SIMKOS
      </h1>
      <p class="text-xs text-gray-500 dark:text-gray-500">
        Manajemen Kos
      </p>
    </div>

    <!-- CLOSE BUTTON (mobile) -->
    <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-[#1a1a1a] transition">
      <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

  </div>

  <!-- USER AVATAR -->
  <a href="/simkos-web/penghuni/index.php"
    class="flex items-center gap-3 px-3 py-3 mb-6 rounded-xl bg-gray-50 dark:bg-[#111] hover:bg-gray-100 dark:hover:bg-[#181818] transition group">

    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shrink-0">
      <?= $userInitials ?>
    </div>

    <div class="min-w-0">
      <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
        <?= htmlspecialchars($userName) ?>
      </p>
      <p class="text-xs text-gray-400">
        Pengelola Kos
      </p>
    </div>

  </a>

  <!-- MENU -->
  <nav class="space-y-1">

    <a href="/simkos-web/dashboard/index.php"
      class="<?= menuClass('dashboard', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      Dashboard
    </a>

    <a href="/simkos-web/penghuni/index.php"
      class="<?= menuClass('penghuni', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      Data Penghuni
    </a>

    <a href="/simkos-web/tagihan/index.php"
      class="<?= menuClass('tagihan', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
      </svg>
      Tagihan Utilitas
    </a>

    <a href="/simkos-web/pembayaran/index.php"
      class="<?= menuClass('pembayaran', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Pembayaran
    </a>

    <a href="/simkos-web/payment-gateway/index.php"
      class="<?= menuClass('payment-gateway', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
      </svg>
      Payment Gateway
    </a>

    <a href="/simkos-web/whatsapp-api/index.php"
      class="<?= menuClass('whatsapp-api', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
      </svg>
      WhatsApp API
    </a>

    <a href="/simkos-web/laporan/index.php"
      class="<?= menuClass('laporan', $active) ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Laporan
    </a>

  </nav>

  <!-- BOTTOM SPACER + LOGOUT -->
  <div class="mt-auto pt-6 border-t border-gray-100 dark:border-[#1a1a1a] mt-6">

    <a href="/simkos-web/auth/logout.php"
      onclick="return confirm('Yakin ingin logout?')"
      class="flex items-center gap-3 p-3 rounded-xl text-gray-500 dark:text-gray-500 hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-600 dark:hover:text-red-400 transition duration-200">

      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
      </svg>
      Logout
    </a>

  </div>

</div>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  sidebar.classList.toggle('-translate-x-full');
  sidebar.classList.toggle('translate-x-0');
  overlay.classList.toggle('hidden');
}
</script>