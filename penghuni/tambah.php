<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <title>Tambah Penghuni - SIMKOS</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class'
    }
  </script>

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

  <?php include '../components/sidebar.php'; ?>
  <?php $active = 'tambah'; ?>
  <div class="ml-64 p-8">

    <?php include '../components/topbar.php'; ?>

    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
        Tambah Penghuni
      </h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Tambahkan data penghuni kos baru
      </p>
    </div>

    <!-- FORM -->
    <div class="max-w-2xl bg-white dark:bg-[#111]
      rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]
      p-8">

      <form class="space-y-6">

        <!-- Kamar -->
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
            Kamar
          </label>
          <input type="text"
            placeholder="Contoh: A1"
            class="w-full h-12 px-5 rounded-2xl
            bg-gray-100 dark:bg-[#0d0d0d]
            border border-gray-200 dark:border-[#222]
            text-gray-800 dark:text-white
            outline-none">
        </div>

        <!-- No HP -->
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
            No HP
          </label>
          <input type="text"
            placeholder="08xxxxxxxxxx"
            class="w-full h-12 px-5 rounded-2xl
            bg-gray-100 dark:bg-[#0d0d0d]
            border border-gray-200 dark:border-[#222]
            text-gray-800 dark:text-white
            outline-none">
        </div>

        <!-- Tanggal Masuk -->
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
            Tanggal Masuk
          </label>
          <input type="date"
            class="w-full h-12 px-5 rounded-2xl
            bg-gray-100 dark:bg-[#0d0d0d]
            border border-gray-200 dark:border-[#222]
            text-gray-800 dark:text-white
            outline-none">
        </div>

        <!-- Status -->
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
            Status
          </label>
          <select
            class="w-full h-12 px-5 rounded-2xl
            bg-gray-100 dark:bg-[#0d0d0d]
            border border-gray-200 dark:border-[#222]
            text-gray-800 dark:text-white
            outline-none">
            <option value="aktif">Aktif</option>
            <option value="menunggak">Menunggak</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-[#1f1f1f]">

          <a href="index.php"
            class="h-12 px-6 rounded-2xl
            bg-gray-200 dark:bg-[#1a1a1a]
            text-gray-700 dark:text-gray-300
            flex items-center justify-center
            hover:opacity-80 transition">
            Batal
          </a>

          <button type="button"
            class="h-12 px-6 rounded-2xl
            bg-blue-600 hover:bg-blue-700
            text-white font-medium shadow-lg transition">
            Simpan
          </button>

        </div>

      </form>

    </div>
  </div>

</body>
</html>