<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Pembayaran - SIMKOS</title>

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
        Pembayaran
      </h1>

      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Monitoring pembayaran penghuni kos terintegrasi Midtrans
      </p>

    </div>

    <!-- ACTION -->
    <div class="flex gap-3">

      <!-- ANNOUNCEMENT -->
      <button
        class="px-5 py-3 rounded-2xl
        bg-green-600 text-white
        hover:bg-green-700 transition shadow-lg">

        Kirim Reminder WhatsApp

      </button>

      <!-- GENERATE -->
      <button
        class="px-5 py-3 rounded-2xl
        bg-blue-600 text-white
        hover:bg-blue-700 transition shadow-lg">

        + Generate Pembayaran

      </button>

    </div>

  </div>

  <!-- PROGRESS -->
  <div class="bg-white dark:bg-[#111]
    p-6 rounded-3xl shadow-xl mb-8
    border border-gray-100 dark:border-[#1f1f1f]">

    <div class="flex justify-between items-center mb-3">

      <h2 class="font-semibold">
        Progress Pembayaran Bulan Ini
      </h2>

      <span class="text-sm text-green-600 font-medium">
        80% Selesai
      </span>

    </div>

    <div class="w-full bg-gray-200 dark:bg-[#1a1a1a]
      rounded-full h-3 overflow-hidden">

      <div class="bg-green-500 h-full rounded-full"
        style="width:80%">
      </div>

    </div>

  </div>

  <!-- STATISTIK -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Total Tagihan
      </p>

      <h2 class="text-2xl font-bold text-blue-600 mt-3">
        Rp 7.240.000
      </h2>

    </div>

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Settlement
      </p>

      <h2 class="text-2xl font-bold text-green-600 mt-3">
        8 Orang
      </h2>

    </div>

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Pending
      </p>

      <h2 class="text-2xl font-bold text-yellow-500 mt-3">
        2 Orang
      </h2>

    </div>

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Total Pemasukan
      </p>

      <h2 class="text-2xl font-bold text-purple-600 mt-3">
        Rp 5.400.000
      </h2>

    </div>

  </div>

  <!-- FILTER -->
  <div class="bg-white dark:bg-[#111]
    p-6 rounded-3xl shadow-xl mb-8
    border border-gray-100 dark:border-[#1f1f1f]">

    <div class="flex flex-col md:flex-row gap-4 justify-between">

      <!-- SEARCH -->
      <input type="text"
        placeholder="Cari penghuni..."
        class="input md:w-1/3">

      <!-- FILTER STATUS -->
      <select class="input md:w-52">

        <option>Semua Status</option>
        <option>Settlement</option>
        <option>Pending</option>
        <option>Expired</option>
        <option>Cancel</option>

      </select>

    </div>

  </div>

  <!-- TABEL PEMBAYARAN -->
  <div class="bg-white dark:bg-[#111]
    p-6 rounded-3xl shadow-xl
    border border-gray-100 dark:border-[#1f1f1f]">

    <div class="flex justify-between items-center mb-6">

      <div>

        <h2 class="text-xl font-semibold">
          Riwayat Pembayaran
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Seluruh transaksi pembayaran penghuni
        </p>

      </div>

    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">

      <table class="w-full text-left">

        <thead>

          <tr class="text-gray-500 dark:text-gray-400 text-sm">

            <th class="pb-4">Nama</th>
            <th>Kamar</th>
            <th>Nominal</th>
            <th>Deadline</th>
            <th>Transaction ID</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>

          </tr>

        </thead>

        <tbody id="paymentTable">

          <!-- ROW -->
          <tr class="border-t border-gray-100 dark:border-[#222]
            hover:bg-gray-50 dark:hover:bg-[#1a1a1a]
            transition">

            <td class="py-4 font-medium">
              Ahmad Fauzi
            </td>

            <td>
              A1
            </td>

            <td>
              Rp 1.200.000
            </td>

            <td>
              05 Juni 2026
            </td>

            <td class="text-sm text-gray-500">
              TRX-20260529-001
            </td>

            <td>

              <span class="px-3 py-1 rounded-full text-xs
                bg-green-100 text-green-600">

                Settlement

              </span>

            </td>

            <td>
              29 Mei 2026
            </td>

            <td>

              <div class="flex gap-2">

                <button
                  class="px-3 py-2 rounded-xl
                  bg-blue-100 text-blue-600 text-sm
                  hover:opacity-80 transition">

                  Detail

                </button>

                <button
                  class="px-3 py-2 rounded-xl
                  bg-green-100 text-green-600 text-sm
                  hover:opacity-80 transition">

                  Invoice

                </button>

              </div>

            </td>

          </tr>

          <!-- ROW -->
          <tr class="border-t border-gray-100 dark:border-[#222]
            hover:bg-gray-50 dark:hover:bg-[#1a1a1a]
            transition">

            <td class="py-4 font-medium">
              Siti Nurhaliza
            </td>

            <td>
              A2
            </td>

            <td>
              Rp 600.000
            </td>

            <td>
              05 Juni 2026
            </td>

            <td class="text-sm text-gray-500">
              TRX-20260529-002
            </td>

            <td>

              <span class="px-3 py-1 rounded-full text-xs
                bg-yellow-100 text-yellow-700">

                Pending

              </span>

            </td>

            <td>
              -
            </td>

            <td>

              <div class="flex gap-2">

                <button
                  class="px-3 py-2 rounded-xl
                  bg-green-100 text-green-600 text-sm
                  hover:opacity-80 transition">

                  WhatsApp

                </button>

                <button
                  class="px-3 py-2 rounded-xl
                  bg-gray-200 dark:bg-[#1a1a1a]
                  text-sm
                  hover:opacity-80 transition">

                  Detail

                </button>

              </div>

            </td>

          </tr>

          <!-- ROW -->
          <tr class="border-t border-gray-100 dark:border-[#222]
            hover:bg-gray-50 dark:hover:bg-[#1a1a1a]
            transition">

            <td class="py-4 font-medium">
              Rizky Maulana
            </td>

            <td>
              B1
            </td>

            <td>
              Rp 850.000
            </td>

            <td>
              03 Juni 2026
            </td>

            <td class="text-sm text-gray-500">
              TRX-20260529-003
            </td>

            <td>

              <span class="px-3 py-1 rounded-full text-xs
                bg-red-100 text-red-600">

                Expired

              </span>

            </td>

            <td>
              28 Mei 2026
            </td>

            <td>

              <div class="flex gap-2">

                <button
                  class="px-3 py-2 rounded-xl
                  bg-red-100 text-red-600 text-sm
                  hover:opacity-80 transition">

                  Generate Ulang

                </button>

                <button
                  class="px-3 py-2 rounded-xl
                  bg-green-100 text-green-600 text-sm
                  hover:opacity-80 transition">

                  WhatsApp

                </button>

              </div>

            </td>

          </tr>

        </tbody>

      </table>

    </div>

  </div>

</div>

<!-- STYLE -->
<style>

.input {

  width: 100%;
  height: 48px;

  padding: 0 16px;

  border-radius: 14px;

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