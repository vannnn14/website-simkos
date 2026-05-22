<!DOCTYPE html>
<html lang="en" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Data Penghuni - SIMKOS</title>

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
  <?php include '../components/sidebar.php'; ?>

  <!-- MAIN -->
  <div class="ml-64 p-8">

    <!-- TOPBAR -->
    <?php include '../components/topbar.php'; ?>

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">

      <!-- LEFT -->
      <div>

        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
          Data Penghuni
        </h1>

        <p class="text-gray-500 dark:text-gray-400 mt-2">
          Kelola seluruh data penghuni kos
        </p>

      </div>

      <a href="tambah.php"
   class="h-12 px-5 rounded-2xl bg-blue-600 hover:bg-blue-700
   transition text-white font-medium shadow-lg flex items-center justify-center">
  + Tambah Penghuni
</a>

    </div>

    <!-- TABLE CARD -->
    <div class="bg-white dark:bg-[#111]
    rounded-3xl shadow-xl
    border border-gray-100 dark:border-[#1f1f1f]
    overflow-hidden">

      <!-- TOP ACTION -->
      <div class="p-6 border-b border-gray-100 dark:border-[#1f1f1f]">

        <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">

          <!-- SEARCH -->
          <div class="relative w-full md:w-80">

            <input
              type="text"
              placeholder="Cari penghuni..."
              class="w-full h-12 rounded-2xl
              bg-gray-100 dark:bg-[#0d0d0d]
              border border-gray-200 dark:border-[#222]
              px-5 outline-none
              text-gray-800 dark:text-white"
            >

          </div>

          <!-- FILTER -->
          <select
            class="h-12 px-4 rounded-2xl
            bg-gray-100 dark:bg-[#0d0d0d]
            border border-gray-200 dark:border-[#222]
            text-gray-700 dark:text-gray-300
            outline-none">

            <option>Semua Status</option>
            <option>Aktif</option>
            <option>Nonaktif</option>

          </select>

        </div>

      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto">

        <table class="w-full">

          <!-- HEAD -->
          <thead class="bg-gray-50 dark:bg-[#0d0d0d]">

            <tr class="text-left text-sm text-gray-500 dark:text-gray-400">

              <th class="px-6 py-5 font-medium">
                Penghuni
              </th>

              <th class="px-6 py-5 font-medium">
                Kamar
              </th>

              <th class="px-6 py-5 font-medium">
                No HP
              </th>

              <th class="px-6 py-5 font-medium">
                Tanggal Masuk
              </th>

              <th class="px-6 py-5 font-medium">
                Status
              </th>

              <th class="px-6 py-5 font-medium text-center">
                Aksi
              </th>

            </tr>

          </thead>

          <!-- BODY -->
          <tbody class="text-gray-700 dark:text-gray-300">

            <!-- ROW -->
            <tr class="border-t border-gray-100 dark:border-[#1f1f1f]
            hover:bg-gray-50 dark:hover:bg-[#151515] transition">

              <!-- USER -->
              <td class="px-6 py-5">

                <div class="flex items-center gap-4">

                  <img
                    src="https://i.pravatar.cc/100?img=12"
                    class="w-12 h-12 rounded-full object-cover"
                  >

                  <div>

                    <h3 class="font-semibold text-gray-800 dark:text-white">
                      Andi Saputra
                    </h3>

                    <p class="text-sm text-gray-500">
                      andi@gmail.com
                    </p>

                  </div>

                </div>

              </td>

              <!-- ROOM -->
              <td class="px-6 py-5">
                A1
              </td>

              <!-- PHONE -->
              <td class="px-6 py-5">
                08123456789
              </td>

              <!-- DATE -->
              <td class="px-6 py-5">
                12 Apr 2026
              </td>

              <!-- STATUS -->
              <td class="px-6 py-5">

                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                  Aktif
                </span>

              </td>

              <!-- ACTION -->
              <td class="px-6 py-5">

                <div class="flex items-center justify-center gap-2">

                  <button
                    class="px-4 py-2 rounded-xl
                    bg-blue-100 text-blue-600
                    text-sm font-medium hover:opacity-80">

                    Edit

                  </button>

                  <button
                    class="px-4 py-2 rounded-xl
                    bg-red-100 text-red-500
                    text-sm font-medium hover:opacity-80">

                    Hapus

                  </button>

                </div>

              </td>

            </tr>

            <!-- ROW -->
            <tr class="border-t border-gray-100 dark:border-[#1f1f1f]
            hover:bg-gray-50 dark:hover:bg-[#151515] transition">

              <!-- USER -->
              <td class="px-6 py-5">

                <div class="flex items-center gap-4">

                  <img
                    src="https://i.pravatar.cc/100?img=15"
                    class="w-12 h-12 rounded-full object-cover"
                  >

                  <div>

                    <h3 class="font-semibold text-gray-800 dark:text-white">
                      Budi Santoso
                    </h3>

                    <p class="text-sm text-gray-500">
                      budi@gmail.com
                    </p>

                  </div>

                </div>

              </td>

              <!-- ROOM -->
              <td class="px-6 py-5">
                A2
              </td>

              <!-- PHONE -->
              <td class="px-6 py-5">
                08129876543
              </td>

              <!-- DATE -->
              <td class="px-6 py-5">
                15 Apr 2026
              </td>

              <!-- STATUS -->
              <td class="px-6 py-5">

                <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-600">
                  Menunggak
                </span>

              </td>

              <!-- ACTION -->
              <td class="px-6 py-5">

                <div class="flex items-center justify-center gap-2">

                  <button
                    class="px-4 py-2 rounded-xl
                    bg-blue-100 text-blue-600
                    text-sm font-medium hover:opacity-80">

                    Edit

                  </button>

                  <button
                    class="px-4 py-2 rounded-xl
                    bg-red-100 text-red-500
                    text-sm font-medium hover:opacity-80">

                    Hapus

                  </button>

                </div>

              </td>

            </tr>

          </tbody>

        </table>

      </div>

    </div>

  </div>

  <!-- THEME -->
  <script>
    function toggleTheme(){
      document.documentElement.classList.toggle('dark')
    }
  </script>

</body>
</html>