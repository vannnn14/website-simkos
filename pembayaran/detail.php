<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Detail Pembayaran - SIMKOS</title>

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
<?php $active = 'pembayaran'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <!-- TOPBAR -->
  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">

    <div>

      <h1 class="text-3xl font-bold">
        Detail Pembayaran
      </h1>

      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Informasi rincian transaksi pembayaran penghuni
      </p>

    </div>

    <!-- ACTION -->
    <div class="flex gap-3">

      <!-- BACK -->
      <a href="index.php"
        class="px-5 py-3 rounded-2xl
        bg-white dark:bg-[#111] text-gray-700 dark:text-white
        border border-gray-200 dark:border-[#1f1f1f]
        hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition shadow-sm flex items-center gap-2">
        
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Kembali

      </a>

      <!-- INVOICE -->
      <button
        class="px-5 py-3 rounded-2xl
        bg-green-600 text-white
        hover:bg-green-700 transition shadow-lg flex items-center gap-2">

        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
        </svg>
        Unduh Invoice

      </button>

    </div>

  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- DETAIL CARD -->
    <div class="lg:col-span-2 space-y-8">
      
      <!-- RINCIAN TRANSAKSI -->
      <div class="bg-white dark:bg-[#111]
        p-8 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-100 dark:border-[#222]">
          
          <div>
            <h2 class="text-xl font-semibold mb-1">
              Rincian Transaksi
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              TRX-20260529-001
            </p>
          </div>

          <span class="px-4 py-2 rounded-full text-sm font-medium
            bg-green-100 text-green-600">
            Settlement
          </span>

        </div>

        <div class="space-y-6">
          
          <!-- ITEM -->
          <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400">Nama Penghuni</span>
            <span class="font-medium text-right">Ahmad Fauzi</span>
          </div>

          <!-- ITEM -->
          <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400">Kamar</span>
            <span class="font-medium text-right">A1</span>
          </div>
          
          <!-- ITEM -->
          <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400">Metode Pembayaran</span>
            <span class="font-medium text-right">BCA Virtual Account</span>
          </div>

          <!-- ITEM -->
          <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400">Waktu Pembayaran</span>
            <span class="font-medium text-right">29 Mei 2026, 14:30 WIB</span>
          </div>

        </div>

        <div class="mt-6 pt-6 border-t border-dashed border-gray-200 dark:border-[#333]">
          
          <!-- ITEM -->
          <div class="flex justify-between items-center mb-3">
            <span class="text-gray-500 dark:text-gray-400">Tagihan Sewa (Bulan Juni)</span>
            <span class="font-medium text-right">Rp 1.000.000</span>
          </div>
          
          <!-- ITEM -->
          <div class="flex justify-between items-center mb-3">
            <span class="text-gray-500 dark:text-gray-400">Biaya Tambahan (Listrik & Air)</span>
            <span class="font-medium text-right">Rp 200.000</span>
          </div>

          <!-- TOTAL -->
          <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-100 dark:border-[#222]">
            <span class="text-lg font-semibold">Total Pembayaran</span>
            <span class="text-2xl font-bold text-blue-600">Rp 1.200.000</span>
          </div>

        </div>

      </div>

    </div>

    <!-- SIDEBAR INFO -->
    <div class="space-y-8">
      
      <!-- LOG AKTIVITAS -->
      <div class="bg-white dark:bg-[#111]
        p-8 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <h2 class="text-xl font-semibold mb-6">
          Riwayat Status
        </h2>

        <div class="relative border-l-2 border-gray-200 dark:border-[#333] ml-3 space-y-6">
          
          <!-- LOG 1 -->
          <div class="relative pl-6">
            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-green-500 border-2 border-white dark:border-[#111]"></div>
            <p class="font-medium text-sm">Settlement</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">29 Mei 2026, 14:30 WIB</p>
            <p class="text-xs text-gray-500 mt-1">Pembayaran berhasil diterima.</p>
          </div>

          <!-- LOG 2 -->
          <div class="relative pl-6">
            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-500 border-2 border-white dark:border-[#111]"></div>
            <p class="font-medium text-sm">Pending</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">29 Mei 2026, 10:15 WIB</p>
            <p class="text-xs text-gray-500 mt-1">Tagihan dibuat dan dikirim ke penghuni.</p>
          </div>

        </div>

      </div>

      <!-- TINDAKAN -->
      <div class="bg-white dark:bg-[#111]
        p-8 rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]">

        <h2 class="text-xl font-semibold mb-6">
          Tindakan
        </h2>

        <div class="flex flex-col gap-3">
          
          <button class="w-full py-3 rounded-2xl bg-green-100 text-green-700 font-medium hover:bg-green-200 transition flex items-center justify-center gap-2">
            Kirim Resi via WhatsApp
          </button>
          
          <button class="w-full py-3 rounded-2xl bg-gray-100 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-200 dark:hover:bg-[#222] transition flex items-center justify-center gap-2">
            Cetak Struk
          </button>

        </div>

      </div>

    </div>

  </div>

</div>

</body>
</html>
