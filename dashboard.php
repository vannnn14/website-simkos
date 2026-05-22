<!DOCTYPE html>
<html lang="en" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Dashboard - SIMKOS</title>

  <!-- TAILWIND -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- CHART JS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- FONT -->
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
  >

  <script>
    tailwind.config = {
      darkMode: 'class'
    }
  </script>

  <style>
    body{
      font-family:'Inter',sans-serif;
    }
  </style>

</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

  <!-- SIDEBAR -->
  <?php $active = 'dashboard'; ?>
  <?php include 'components/sidebar.php'; ?>

  <!-- MAIN -->
  <div class="ml-64 p-8">

    <!-- TOPBAR -->
    <?php include 'components/topbar.php'; ?>

    <!-- ===== SUMMARY CARD ===== -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Total Pemasukan
        </p>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-3">
          Rp12.450.000
        </h3>
      </div>

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Penghuni Aktif
        </p>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-3">
          12
        </h3>
      </div>

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Belum Bayar
        </p>
        <h3 class="text-2xl font-bold text-red-500 mt-3">
          5 Orang
        </h3>
      </div>

      <div class="p-6 rounded-3xl bg-white dark:bg-[#111] shadow-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Total Tagihan
        </p>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-3">
          Rp7.240.000
        </h3>
      </div>

    </div>

    <!-- ===== QUICK MENU ===== -->
    <div class="mt-10">

      <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">
        Menu Cepat
      </h2>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <a href="penghuni/index.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">

          <p class="text-sm text-gray-500">Penghuni</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">
            Kelola Data
          </h3>

        </a>

        <a href="tagihan.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">

          <p class="text-sm text-gray-500">Tagihan</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">
            Input Biaya
          </h3>

        </a>

        <a href="pembayaran.php"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">

          <p class="text-sm text-gray-500">Pembayaran</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">
            Cek Status
          </h3>

        </a>

        <a href="#"
          class="p-5 bg-white dark:bg-[#111] rounded-2xl shadow hover:scale-105 transition">

          <p class="text-sm text-gray-500">Reminder</p>
          <h3 class="font-semibold text-gray-800 dark:text-white mt-2">
            Kirim WA
          </h3>

        </a>

      </div>

    </div>

    <!-- ===== INFO BOX ===== -->
    <div class="mt-10 p-6 bg-white dark:bg-[#111] rounded-3xl shadow">

      <h3 class="font-semibold text-gray-800 dark:text-white mb-4">
        Status Hari Ini
      </h3>

      <p class="text-gray-500 dark:text-gray-400 text-sm">
        5 penghuni belum melakukan pembayaran bulan ini.
      </p>

    </div>

    <!-- ===== CHART ===== -->
    <div class="mt-10 p-6 bg-white dark:bg-[#111] rounded-3xl shadow">

      <h3 class="font-semibold text-gray-800 dark:text-white mb-4">
        Grafik Pemasukan
      </h3>

      <canvas id="incomeChart" height="100"></canvas>

    </div>

  </div>

  <!-- ===== SCRIPT CHART ===== -->
  <script>
    const ctx = document.getElementById('incomeChart');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
          label: 'Pemasukan',
          data: [5000000, 7000000, 6500000, 8000000, 12450000, 9000000],
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        plugins: {
          legend: {
            labels: {
              color: '#9ca3af'
            }
          }
        },
        scales: {
          x: {
            ticks: {
              color: '#9ca3af'
            }
          },
          y: {
            ticks: {
              color: '#9ca3af'
            }
          }
        }
      }
    });
  </script>

  <script>
    function toggleTheme(){
      document.documentElement.classList.toggle('dark')
    }
  </script>

</body>
</html>