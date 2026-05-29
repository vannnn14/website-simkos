<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Payment Gateway - SIMKOS</title>

  <!-- TAILWIND -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- FONT -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<!-- ACTIVE MENU -->
<?php $active = 'payment-gateaway'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <!-- TOPBAR -->
  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8 flex justify-between items-center">

    <div>

      <h1 class="text-3xl font-bold">
        Payment Gateway
      </h1>

      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Kelola konfigurasi Midtrans untuk pembayaran penghuni kos
      </p>

    </div>

    <!-- STATUS -->
    <div class="flex items-center gap-3
      bg-green-100 dark:bg-green-900/20
      px-4 py-2 rounded-2xl">

      <div class="w-3 h-3 rounded-full bg-green-500"></div>

      <span class="text-sm font-medium text-green-600">
        Sandbox Active
      </span>

    </div>

  </div>

  <!-- GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- LEFT -->
    <div class="lg:col-span-2">

      <!-- FORM -->
      <div class="bg-white dark:bg-[#111]
        p-8 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <h2 class="text-xl font-semibold mb-6">
          Konfigurasi Midtrans
        </h2>

        <!-- SERVER KEY -->
        <div class="mb-6">

          <label class="block text-sm mb-2
            text-gray-500 dark:text-gray-400">

            Midtrans Server Key

          </label>

          <input
            type="password"
            value="SB-Mid-server-xxxxxxxxxxxx"
            class="input">

        </div>

        <!-- CLIENT KEY -->
        <div class="mb-6">

          <label class="block text-sm mb-2
            text-gray-500 dark:text-gray-400">

            Midtrans Client Key

          </label>

          <input
            type="text"
            value="SB-Mid-client-xxxxxxxxxxxx"
            class="input">

        </div>

        <!-- CALLBACK URL -->
        <div class="mb-6">

          <label class="block text-sm mb-2
            text-gray-500 dark:text-gray-400">

            Callback / Webhook URL

          </label>

          <input
            type="text"
            value="https://simkos.com/midtrans/callback.php"
            class="input">

        </div>

        <!-- MODE -->
        <div class="flex items-center gap-3 mb-8">

          <input type="checkbox" checked>

          <span class="text-sm text-gray-600 dark:text-gray-300">
            Gunakan Sandbox Mode
          </span>

        </div>

        <!-- BUTTON -->
        <div class="flex justify-end gap-3">

          <button
            class="px-5 py-3 rounded-2xl
            bg-gray-200 dark:bg-[#1a1a1a]
            hover:opacity-80 transition">

            Reset

          </button>

          <button
            class="px-5 py-3 rounded-2xl
            bg-blue-600 text-white
            hover:bg-blue-700 transition">

            Simpan Konfigurasi

          </button>

        </div>

      </div>

    </div>

    <!-- RIGHT -->
    <div class="space-y-6">

      <!-- STATUS CARD -->
      <div class="bg-white dark:bg-[#111]
        p-6 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <p class="text-sm text-gray-500 dark:text-gray-400">
          Status Koneksi
        </p>

        <div class="flex items-center gap-3 mt-4">

          <div class="w-4 h-4 rounded-full bg-green-500"></div>

          <h2 class="text-xl font-bold text-green-600">
            Connected
          </h2>

        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
          Midtrans berhasil terhubung
        </p>

      </div>

      <!-- ENVIRONMENT -->
      <div class="bg-white dark:bg-[#111]
        p-6 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <p class="text-sm text-gray-500 dark:text-gray-400">
          Environment
        </p>

        <h2 class="text-2xl font-bold text-yellow-500 mt-3">
          Sandbox
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
          Mode testing aktif
        </p>

      </div>

      <!-- INFO -->
      <div class="bg-white dark:bg-[#111]
        p-6 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <h2 class="text-lg font-semibold mb-4">
          Informasi
        </h2>

        <ul class="space-y-3 text-sm
          text-gray-500 dark:text-gray-400">

          <li>
            • Gunakan Server Key untuk backend
          </li>

          <li>
            • Gunakan Client Key untuk frontend
          </li>

          <li>
            • Aktifkan Production Mode saat live
          </li>

          <li>
            • Pastikan callback URL dapat diakses publik
          </li>

        </ul>

      </div>

    </div>

  </div>

</div>

<!-- STYLE -->
<style>

.input {

  width: 100%;
  height: 52px;

  padding: 0 16px;

  border-radius: 16px;

  background: #f3f4f6;
  border: 1px solid #e5e7eb;

  outline: none;

}

.dark .input {

  background: #0d0d0d;
  border: 1px solid #222;

  color: white;

}

</style>

</body>
</html>