<!DOCTYPE html>
<html lang="en" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Dashboard - SIMKOS</title>

  <!-- TAILWIND -->
  <script src="https://cdn.tailwindcss.com"></script>

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

    <!-- CONTENT -->
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

    </div>

  </div>

  <script>
    function toggleTheme(){
      document.documentElement.classList.toggle('dark')
    }
  </script>

</body>
</html>