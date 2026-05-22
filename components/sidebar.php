<?php
function menuClass($menu, $active) {
  return $menu == $active
    ? 'block p-3 rounded-xl bg-blue-100 text-blue-600 font-medium'
    : 'block p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#111] transition';
}
?>

<!-- SIDEBAR -->
<div class="w-64 h-screen bg-white dark:bg-[#0b0b0b]
border-r border-gray-200 dark:border-[#1f1f1f]
p-6 fixed left-0 top-0">

  <!-- LOGO -->
  <div class="mb-10">
    <h1 class="text-2xl font-bold text-blue-600">
      SIMKOS
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400">
      Manajemen Kos
    </p>
  </div>

  <!-- MENU -->
  <nav class="space-y-3">

    <a href="/simkos-web/dashboard.php"
      class="<?= menuClass('dashboard', $active) ?>">
      Dashboard
    </a>

    <a href="/simkos-web/penghuni/index.php"
      class="<?= menuClass('penghuni', $active) ?>">
      Data Penghuni
    </a>

    <a href="/simkos-web/tagihan.php"
      class="<?= menuClass('tagihan', $active) ?>">
      Tagihan Utilitas
    </a>

    <a href="/simkos-web/pembayaran.php"
      class="<?= menuClass('pembayaran', $active) ?>">
      Pembayaran
    </a>

    <a href="/simkos-web/laporan.php"
      class="<?= menuClass('laporan', $active) ?>">
      Laporan
    </a>

  </nav>

</div>