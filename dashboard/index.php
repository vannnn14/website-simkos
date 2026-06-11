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

  <?php $active = 'dashboard'; ?>
  <?php include '../components/sidebar.php'; ?>

  <div class="ml-64 p-8">

    <?php include '../components/topbar.php'; ?>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
        <h3 class="text-2xl font-bold text-green-600 mt-3">
          Rp<?= number_format($pemasukan, 0, ',', '.') ?>
        </h3>
      </div>

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">Penghuni Aktif</p>
        <h3 class="text-2xl font-bold text-blue-600 mt-3">
          <?= $aktif ?> Orang
        </h3>
      </div>

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum Bayar</p>
        <h3 class="text-2xl font-bold text-red-500 mt-3">
          <?= $belumBayar ?> Orang
        </h3>
      </div>

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">Outstanding Tagihan</p>
        <h3 class="text-2xl font-bold text-yellow-500 mt-3">
          Rp<?= number_format($totalTagihan, 0, ',', '.') ?>
        </h3>
      </div>

    </div>

    <!-- QUICK MENU -->
    <div class="mt-10">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">Menu Cepat</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <a href="../penghuni/index.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">
          <p class="text-sm text-gray-500">Penghuni</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">Kelola Data</h3>
        </a>

        <a href="../tagihan/index.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">
          <p class="text-sm text-gray-500">Tagihan</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">Input Biaya</h3>
        </a>

        <a href="../pembayaran/index.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">
          <p class="text-sm text-gray-500">Pembayaran</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">Cek Status</h3>
        </a>

        <a href="../pembayaran/kirim_reminder.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">
          <p class="text-sm text-gray-500">Reminder</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">Kirim WA</h3>
        </a>

      </div>
    </div>

    <!-- INFO -->
    <div class="mt-10 p-6 bg-white dark:bg-[#111] rounded-3xl shadow">
      <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Status Hari Ini</h3>
      <p class="text-gray-500 dark:text-gray-400 text-sm">
        <?= $belumBayar ?> penghuni belum melakukan pembayaran bulan ini.
        <?php if ($aktif > 0): ?>
          Tingkat pelunasan: <?= round((($aktif - $belumBayar) / $aktif) * 100) ?>%.
        <?php endif; ?>
      </p>
    </div>

    <!-- CHART -->
    <div class="mt-10 p-6 bg-white dark:bg-[#111] rounded-3xl shadow">
      <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Grafik Pemasukan</h3>
      <canvas id="incomeChart" height="100"></canvas>
    </div>

  </div>

  <script>
    new Chart(document.getElementById('incomeChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
          label: 'Pemasukan',
          data: <?= json_encode($chartData) ?>,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,0.1)',
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        plugins: {
          legend: {
            labels: { color: '#9ca3af' }
          }
        },
        scales: {
          x: { ticks: { color: '#9ca3af' } },
          y: { ticks: { color: '#9ca3af' } }
        }
      }
    });
  </script>

</body>
</html>
