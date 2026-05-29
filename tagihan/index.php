<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tagihan - SIMKOS</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class'
    }
  </script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'tagihan'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8">

    <h1 class="text-3xl font-bold">
      Tagihan Bulanan
    </h1>

    <p class="text-gray-500 dark:text-gray-400 mt-2">
      Kelola biaya utilitas kos
    </p>

  </div>

  <!-- FORM -->
  <div class="bg-white dark:bg-[#111]
    p-8 rounded-3xl shadow-xl mb-8
    border border-gray-100 dark:border-[#1f1f1f]">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <input type="number"
        id="listrik"
        placeholder="Biaya Listrik"
        class="input">

      <input type="number"
        id="air"
        placeholder="Biaya Air"
        class="input">

      <input type="number"
        id="wifi"
        placeholder="Biaya Wifi"
        class="input">

      <input type="number"
        id="sampah"
        placeholder="Biaya Sampah"
        class="input">

      <input type="number"
        id="penghuni"
        placeholder="Jumlah penghuni aktif"
        class="input">

    </div>

    <!-- TOTAL -->
    <div class="mt-6 bg-blue-50 dark:bg-[#0b2239]
      p-6 rounded-2xl">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Total Tagihan
      </p>

      <h2 id="total"
        class="text-2xl font-bold text-blue-600 mt-2">

        Rp 0

      </h2>

      <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
        Per Orang
      </p>

      <h3 id="perOrang"
        class="text-xl font-semibold text-green-600 mt-1">

        Rp 0

      </h3>

    </div>

    <!-- BUTTON -->
    <div class="flex justify-end gap-3 mt-6">

      <button onclick="resetForm()"
        class="px-6 py-3 rounded-xl
        bg-gray-200 dark:bg-[#1a1a1a]
        hover:opacity-80 transition">

        Reset

      </button>

      <button onclick="tambahTagihan()"
        class="px-6 py-3 rounded-xl
        bg-blue-600 text-white
        hover:bg-blue-700 transition">

        Simpan Tagihan

      </button>

    </div>

  </div>

  <!-- RIWAYAT TAGIHAN -->
  <div class="bg-white dark:bg-[#111]
    p-6 rounded-3xl shadow-xl mb-8
    border border-gray-100 dark:border-[#1f1f1f]">

    <h2 class="text-xl font-semibold mb-4">
      Riwayat Tagihan
    </h2>

    <div class="overflow-x-auto">

      <table class="w-full text-left">

        <thead>

          <tr class="text-gray-500 dark:text-gray-400 text-sm">

            <th class="py-3">Bulan</th>
            <th>Total</th>
            <th>/Orang</th>
            <th>Status</th>

          </tr>

        </thead>

        <tbody id="listTagihan">

          <tr class="border-t border-gray-100 dark:border-[#222]
            hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">

            <td class="py-4">
              April 2026
            </td>

            <td>
              Rp 7.240.000
            </td>

            <td>
              Rp 603.000
            </td>

            <td>

              <span class="px-3 py-1 text-xs rounded-full
                bg-green-100 text-green-600">

                Aktif

              </span>

            </td>

          </tr>

        </tbody>

      </table>

    </div>

  </div>

  <!-- PEMBAGIAN PENGHUNI -->
  <div class="bg-white dark:bg-[#111]
    p-6 rounded-3xl shadow-xl
    border border-gray-100 dark:border-[#1f1f1f]">

    <div class="mb-6">

      <h2 class="text-xl font-semibold">
        Pembagian Penghuni
      </h2>

      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        Simulasi pembagian tagihan tiap penghuni
      </p>

    </div>

    <div class="overflow-x-auto">

      <table class="w-full text-left">

        <thead>

          <tr class="text-gray-500 dark:text-gray-400 text-sm">

            <th class="pb-4">Nama</th>
            <th>Status Tinggal</th>
            <th>Bobot</th>
            <th>Tagihan</th>

          </tr>

        </thead>

        <tbody id="listPembagian">

          <tr class="border-t border-gray-100 dark:border-[#222]">

            <td class="py-4">
              Ahmad Fauzi
            </td>

            <td>

              <select
                class="bg-green-100 text-green-700
                px-3 py-1 rounded-full text-xs">

                <option>Full</option>
                <option>Setengah</option>

              </select>

            </td>

            <td>
              1x
            </td>

            <td class="font-medium">
              Rp 0
            </td>

          </tr>

          <tr class="border-t border-gray-100 dark:border-[#222]">

            <td class="py-4">
              Siti Nurhaliza
            </td>

            <td>

              <select
                class="bg-yellow-100 text-yellow-700
                px-3 py-1 rounded-full text-xs">

                <option>Setengah</option>
                <option>Full</option>

              </select>

            </td>

            <td>
              0.5x
            </td>

            <td class="font-medium">
              Rp 0
            </td>

          </tr>

        </tbody>

      </table>

    </div>

  </div>

</div>

<!-- SCRIPT -->
<script>

const inputs = document.querySelectorAll(
  '#listrik, #air, #wifi, #sampah, #penghuni'
);

const totalText = document.getElementById('total');
const perOrangText = document.getElementById('perOrang');
const listTagihan = document.getElementById('listTagihan');

function formatRupiah(angka) {
  return 'Rp ' + angka.toLocaleString('id-ID');
}

function hitungTotal() {

  const listrik = Number(document.getElementById('listrik').value) || 0;
  const air = Number(document.getElementById('air').value) || 0;
  const wifi = Number(document.getElementById('wifi').value) || 0;
  const sampah = Number(document.getElementById('sampah').value) || 0;
  const penghuni = Number(document.getElementById('penghuni').value) || 0;

  const total = listrik + air + wifi + sampah;

  totalText.innerText = formatRupiah(total);

  if (penghuni > 0) {

    const perOrang = total / penghuni;

    perOrangText.innerText =
      formatRupiah(Math.round(perOrang));

  } else {

    perOrangText.innerText = 'Rp 0';

  }
}

inputs.forEach(i =>
  i.addEventListener('input', hitungTotal)
);

function resetForm() {

  inputs.forEach(i => i.value = '');

  totalText.innerText = 'Rp 0';
  perOrangText.innerText = 'Rp 0';
}

function tambahTagihan() {

  const listrik = Number(document.getElementById('listrik').value) || 0;
  const air = Number(document.getElementById('air').value) || 0;
  const wifi = Number(document.getElementById('wifi').value) || 0;
  const sampah = Number(document.getElementById('sampah').value) || 0;
  const penghuni = Number(document.getElementById('penghuni').value) || 0;

  const total = listrik + air + wifi + sampah;

  if(total === 0 || penghuni === 0){

    alert('Lengkapi data dulu!');
    return;

  }

  const perOrang = total / penghuni;

  // RIWAYAT TAGIHAN
  const row = `

    <tr class="border-t border-gray-100 dark:border-[#222]
      hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">

      <td class="py-4">
        Mei 2026
      </td>

      <td>
        ${formatRupiah(total)}
      </td>

      <td>
        ${formatRupiah(Math.round(perOrang))}
      </td>

      <td>

        <span class="px-3 py-1 text-xs rounded-full
          bg-blue-100 text-blue-600">

          Baru

        </span>

      </td>

    </tr>

  `;

  listTagihan.innerHTML += row;

  // PEMBAGIAN PENGHUNI
  const totalBobot = 1 + 0.5;

  const full = total / totalBobot;
  const setengah = full * 0.5;

  const pembagian = `

    <tr class="border-t border-gray-100 dark:border-[#222]">

      <td class="py-4">
        Ahmad Fauzi
      </td>

      <td>

        <select
          class="bg-green-100 text-green-700
          px-3 py-1 rounded-full text-xs">

          <option>Full</option>
          <option>Setengah</option>

        </select>

      </td>

      <td>
        1x
      </td>

      <td class="font-semibold text-green-600">
        ${formatRupiah(Math.round(full))}
      </td>

    </tr>

    <tr class="border-t border-gray-100 dark:border-[#222]">

      <td class="py-4">
        Siti Nurhaliza
      </td>

      <td>

        <select
          class="bg-yellow-100 text-yellow-700
          px-3 py-1 rounded-full text-xs">

          <option>Setengah</option>
          <option>Full</option>

        </select>

      </td>

      <td>
        0.5x
      </td>

      <td class="font-semibold text-yellow-600">
        ${formatRupiah(Math.round(setengah))}
      </td>

    </tr>

  `;

  document.getElementById('listPembagian')
    .innerHTML = pembagian;

  resetForm();

  alert('Tagihan berhasil dibuat!');
}

</script>

<!-- STYLE -->
<style>

.input {

  width: 100%;
  height: 48px;
  padding: 0 16px;

  border-radius: 12px;

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