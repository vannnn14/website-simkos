<?php
include '../config/koneksi.php';
include '../config/auth.php';

$pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(jumlah_bayar), 0) total FROM pembayaran WHERE status = 'Diterima'
"))['total'];

$aktif = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total FROM penghuni WHERE status_kamar = 'Aktif'
"))['total'];

$belumBayar = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) total FROM detail_tagihan WHERE status_bayar = 'Belum Bayar'
"))['total'];

$totalTagihan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(nominal_tagihan), 0) total
    FROM detail_tagihan WHERE status_bayar != 'Lunas'
"))['total'];

$qChart = mysqli_query($conn, "
    SELECT
        DATE_FORMAT(tanggal_pembayaran, '%Y-%m') bulan_key,
        DATE_FORMAT(tanggal_pembayaran, '%b') bulan_label,
        SUM(jumlah_bayar) total
    FROM pembayaran
    WHERE status = 'Diterima'
      AND tanggal_pembayaran >= DATE_SUB(NOW(), INTERVAL 5 MONTH)
    GROUP BY bulan_key
    ORDER BY bulan_key ASC
");

$chartLabels = [];
$chartData   = [];
while ($r = mysqli_fetch_assoc($qChart)) {
    $chartLabels[] = $r['bulan_label'];
    $chartData[]   = (int) $r['total'];
}

if (empty($chartLabels)) {
    $chartLabels = ['Bulan ini'];
    $chartData   = [0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - SIMKOS</title>
  <?php include '../components/theme.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f]">

  <?php $pageTitle = 'Dashboard'; ?>
  <?php $active = 'dashboard'; ?>
  <?php include '../components/sidebar.php'; ?>

  <div class="lg:ml-64 p-4 lg:p-8 pt-4">

    <?php include '../components/topbar.php'; ?>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

      <div class="relative p-6 rounded-3xl bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10 shadow-xl border border-green-200/50 dark:border-green-800/30 overflow-hidden">
        <div class="absolute top-3 right-3 w-12 h-12 rounded-full bg-green-200/50 dark:bg-green-700/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
        <h3 class="text-2xl font-bold text-green-600 mt-3">
          Rp<?= number_format($pemasukan, 0, ',', '.') ?>
        </h3>
        <p class="text-xs text-gray-400 mt-1">Sepanjang waktu</p>
      </div>

      <div class="relative p-6 rounded-3xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 shadow-xl border border-blue-200/50 dark:border-blue-800/30 overflow-hidden">
        <div class="absolute top-3 right-3 w-12 h-12 rounded-full bg-blue-200/50 dark:bg-blue-700/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Penghuni Aktif</p>
        <h3 class="text-2xl font-bold text-blue-600 mt-3">
          <?= $aktif ?> Orang
        </h3>
        <p class="text-xs text-gray-400 mt-1">Total penghuni</p>
      </div>

      <div class="relative p-6 rounded-3xl bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/10 shadow-xl border border-red-200/50 dark:border-red-800/30 overflow-hidden">
        <div class="absolute top-3 right-3 w-12 h-12 rounded-full bg-red-200/50 dark:bg-red-700/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum Bayar</p>
        <h3 class="text-2xl font-bold text-red-500 mt-3">
          <?= $belumBayar ?> Orang
        </h3>
        <p class="text-xs text-gray-400 mt-1">Perlu perhatian</p>
      </div>

      <div class="relative p-6 rounded-3xl bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-900/10 shadow-xl border border-yellow-200/50 dark:border-yellow-800/30 overflow-hidden">
        <div class="absolute top-3 right-3 w-12 h-12 rounded-full bg-yellow-200/50 dark:bg-yellow-700/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
          </svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Outstanding Tagihan</p>
        <h3 class="text-2xl font-bold text-yellow-500 mt-3">
          Rp<?= number_format($totalTagihan, 0, ',', '.') ?>
        </h3>
        <p class="text-xs text-gray-400 mt-1">Belum dibayar</p>
      </div>

    </div>

    <!-- QUICK MENU -->
    <div class="mt-10">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">Menu Cepat</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <a href="../penghuni/index.php"
          class="group p-5 bg-white dark:bg-[#111] rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500">Penghuni</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-1 group-hover:text-blue-600 transition-colors">Kelola Data</h3>
        </a>

        <a href="../tagihan/index.php"
          class="group p-5 bg-white dark:bg-[#111] rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500">Tagihan</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-1 group-hover:text-purple-600 transition-colors">Input Biaya</h3>
        </a>

        <a href="../pembayaran/index.php"
          class="group p-5 bg-white dark:bg-[#111] rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500">Pembayaran</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-1 group-hover:text-green-600 transition-colors">Cek Status</h3>
        </a>

        <a href="../pembayaran/kirim_reminder.php"
          class="group p-5 bg-white dark:bg-[#111] rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500">Reminder</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-1 group-hover:text-orange-600 transition-colors">Kirim WA</h3>
        </a>

      </div>
    </div>

    <!-- INFO -->
    <div class="mt-10 p-6 bg-white dark:bg-[#111] rounded-3xl shadow border border-gray-100 dark:border-[#1f1f1f]">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
          <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="font-semibold text-gray-800 dark:text-white">Status Hari Ini</h3>
      </div>
      <p class="text-gray-500 dark:text-gray-400 text-sm">
        <?= $belumBayar ?> penghuni belum melakukan pembayaran bulan ini.
      </p>
    </div>

    <!-- CHART -->
    <div class="mt-10 p-6 bg-white dark:bg-[#111] rounded-3xl shadow border border-gray-100 dark:border-[#1f1f1f]">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
          <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </div>
        <div>
          <h3 class="font-semibold text-gray-800 dark:text-white">Grafik Pemasukan</h3>
          <p class="text-xs text-gray-400">5 bulan terakhir</p>
        </div>
      </div>
      <canvas id="incomeChart" height="120"></canvas>
    </div>

  </div>

  <script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const gridColor = isDark ? '#1f1f1f' : '#e5e7eb';

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59,130,246,0.3)');
    gradient.addColorStop(1, 'rgba(59,130,246,0.02)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
          label: 'Pemasukan (Rp)',
          data: <?= json_encode($chartData) ?>,
          borderColor: '#3b82f6',
          backgroundColor: gradient,
          borderWidth: 2,
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#3b82f6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            labels: { color: textColor, font: { size: 12 } }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
              }
            }
          }
        },
        scales: {
          x: {
            ticks: { color: textColor },
            grid: { color: gridColor }
          },
          y: {
            ticks: { color: textColor },
            grid: { color: gridColor },
            beginAtZero: true
          }
        }
      }
    });
  </script>

</body>
</html>
