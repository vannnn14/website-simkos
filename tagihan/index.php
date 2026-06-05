<?php
include '../config/koneksi.php';

// ── Hitung bobot ─────────────────────────────────────────────────────────────
$qAktif      = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Aktif'");
$aktif       = mysqli_fetch_assoc($qAktif)['total'];

$qTidakAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Tidak Aktif'");
$tidakAktif  = mysqli_fetch_assoc($qTidakAktif)['total'];

$totalBobot  = ($aktif * 1) + ($tidakAktif * 0.5);

// ── Ambil riwayat tagihan dari DB ─────────────────────────────────────────────
$qRiwayat = mysqli_query($conn, "
    SELECT * FROM tagihan_utilitas
    ORDER BY tahun DESC, FIELD(bulan,
        'January','February','March','April','May','June',
        'July','August','September','October','November','December') DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tagihan - SIMKOS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>tailwind.config = { darkMode: 'class' }</script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .input {
      width: 100%; height: 48px; padding: 0 16px;
      border-radius: 12px;
      background: #f3f4f6; border: 1px solid #e5e7eb; outline: none;
    }
    .dark .input {
      background: #0d0d0d; border: 1px solid #222; color: white;
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
    <h1 class="text-3xl font-bold">Tagihan Bulanan</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-2">Kelola biaya utilitas kos</p>
  </div>

  <!-- FORM INPUT TAGIHAN -->
  <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl mb-8 border border-gray-100 dark:border-[#1f1f1f]">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <input type="number" id="listrik" placeholder="Biaya Listrik" class="input" min="0">
      <input type="number" id="air"     placeholder="Biaya Air"     class="input" min="0">
      <input type="number" id="wifi"    placeholder="Biaya Wifi"    class="input" min="0">
      <input type="number" id="sampah"  placeholder="Biaya Sampah"  class="input" min="0">

      <input type="text"
        value="Aktif: <?= $aktif ?> | Tidak Aktif: <?= $tidakAktif ?>"
        readonly
        class="input bg-gray-200 cursor-not-allowed">

    </div>

    <!-- TOTAL -->
    <div class="mt-6 bg-blue-50 dark:bg-[#0b2239] p-6 rounded-2xl">
      <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</p>
      <h2 id="total" class="text-2xl font-bold text-blue-600 mt-2">Rp 0</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Tarif per Bobot</p>
      <h3 id="perOrang" class="text-xl font-semibold text-green-600 mt-1">Rp 0</h3>
    </div>

    <!-- NOTIFIKASI -->
    <div id="notif" class="hidden mt-4 px-4 py-3 rounded-xl text-sm font-medium"></div>

    <!-- BUTTON -->
    <div class="flex justify-end gap-3 mt-6">
      <button onclick="resetForm()"
        class="px-6 py-3 rounded-xl bg-gray-200 dark:bg-[#1a1a1a] hover:opacity-80 transition">
        Reset
      </button>
      <button id="btnSimpan" onclick="tambahTagihan()"
        class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
        Simpan Tagihan
      </button>
    </div>

  </div>

  <!-- RIWAYAT TAGIHAN DARI DATABASE -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl mb-8 border border-gray-100 dark:border-[#1f1f1f]">

    <h2 class="text-xl font-semibold mb-4">Riwayat Tagihan</h2>

    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-gray-500 dark:text-gray-400 text-sm">
            <th class="py-3">Bulan</th>
            <th>Total Tagihan</th>
            <th>Tarif/Bobot</th>
            <th>Penghuni</th>
            <th>Tenggat</th>
          </tr>
        </thead>
        <tbody id="listTagihan">

          <?php if (mysqli_num_rows($qRiwayat) === 0): ?>

          <tr>
            <td colspan="5" class="py-6 text-center text-gray-400">
              Belum ada tagihan tercatat.
            </td>
          </tr>

          <?php else: ?>
          <?php while ($t = mysqli_fetch_assoc($qRiwayat)): ?>

          <tr class="border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">

            <td class="py-4 font-medium">
              <?= $t['bulan'] . ' ' . $t['tahun'] ?>
            </td>

            <td>
              Rp <?= number_format($t['total_tagihan'], 0, ',', '.') ?>
            </td>

            <td>
              Rp <?= number_format($t['tarif_per_bobot'], 0, ',', '.') ?>
            </td>

            <td>
              <?= $t['total_penghuni'] ?> orang
            </td>

            <td>
              <?= date('d M Y', strtotime($t['tenggat_pembayaran'])) ?>
            </td>

          </tr>

          <?php endwhile; ?>
          <?php endif; ?>

        </tbody>
      </table>
    </div>

  </div>

  <!-- PEMBAGIAN PER PENGHUNI -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">

    <div class="mb-6">
      <h2 class="text-xl font-semibold">Pembagian Penghuni</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Simulasi pembagian tagihan tiap penghuni</p>
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

          <?php
          $queryPenghuni = mysqli_query($conn, "SELECT * FROM penghuni");
          while ($p = mysqli_fetch_assoc($queryPenghuni)):
            $bobot = ($p['status_kamar'] === 'Aktif') ? 1 : 0.5;
          ?>

          <tr class="penghuni-row border-t border-gray-100 dark:border-[#222]"
              data-status="<?= $p['status_kamar'] ?>"
              data-bobot="<?= $bobot ?>">

            <td class="py-4"><?= htmlspecialchars($p['nama_lengkap']) ?></td>

            <td>
              <?php if ($p['status_kamar'] === 'Aktif'): ?>
                <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">Aktif</span>
              <?php else: ?>
                <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-600">Tidak Aktif</span>
              <?php endif; ?>
            </td>

            <td><?= $bobot ?>x</td>

            <td class="font-medium nominal-tagihan">Rp 0</td>

          </tr>

          <?php endwhile; ?>

        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- SCRIPT -->
<script>
const totalBobot  = <?= $totalBobot ?>;
const jumlahAktif = <?= $aktif ?>;

const totalText  = document.getElementById('total');
const perOrangText = document.getElementById('perOrang');
const notif      = document.getElementById('notif');

function formatRupiah(angka) {
  return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

function hitungTotal() {
  const listrik = Number(document.getElementById('listrik').value) || 0;
  const air     = Number(document.getElementById('air').value)     || 0;
  const wifi    = Number(document.getElementById('wifi').value)    || 0;
  const sampah  = Number(document.getElementById('sampah').value)  || 0;
  const total   = listrik + air + wifi + sampah;

  totalText.innerText = formatRupiah(total);

  if (totalBobot > 0) {
    perOrangText.innerText = formatRupiah(total / totalBobot);

    document.querySelectorAll('.penghuni-row').forEach(row => {
      const bobot  = parseFloat(row.dataset.bobot);
      const status = row.dataset.status;

      const tListrik = (listrik / totalBobot) * bobot;
      const tAir     = status === 'Aktif' && jumlahAktif > 0 ? air    / jumlahAktif : 0;
      const tWifi    = status === 'Aktif' && jumlahAktif > 0 ? wifi   / jumlahAktif : 0;
      const tSampah  = status === 'Aktif' && jumlahAktif > 0 ? sampah / jumlahAktif : 0;

      row.querySelector('.nominal-tagihan').innerText =
        formatRupiah(tListrik + tAir + tWifi + tSampah);
    });
  }
}

document.querySelectorAll('#listrik,#air,#wifi,#sampah')
  .forEach(i => i.addEventListener('input', hitungTotal));

function resetForm() {
  document.querySelectorAll('#listrik,#air,#wifi,#sampah')
    .forEach(i => i.value = '');
  totalText.innerText   = 'Rp 0';
  perOrangText.innerText = 'Rp 0';
  document.querySelectorAll('.nominal-tagihan')
    .forEach(el => el.innerText = 'Rp 0');
}

function showNotif(pesan, sukses) {
  notif.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
  notif.classList.add(sukses ? 'bg-green-100' : 'bg-red-100', sukses ? 'text-green-700' : 'text-red-700');
  notif.innerText = pesan;
  notif.classList.remove('hidden');
  setTimeout(() => notif.classList.add('hidden'), 4000);
}

async function tambahTagihan() {
  const listrik = Number(document.getElementById('listrik').value) || 0;
  const air     = Number(document.getElementById('air').value)     || 0;
  const wifi    = Number(document.getElementById('wifi').value)    || 0;
  const sampah  = Number(document.getElementById('sampah').value)  || 0;
  const total   = listrik + air + wifi + sampah;

  const jumlahPenghuni = document.querySelectorAll('.penghuni-row').length;

  if (total === 0) {
    showNotif('Isi minimal satu biaya utilitas!', false);
    return;
  }
  if (jumlahPenghuni === 0) {
    showNotif('Tidak ada penghuni terdaftar!', false);
    return;
  }

  const btn = document.getElementById('btnSimpan');
  btn.disabled   = true;
  btn.innerText  = 'Menyimpan...';

  try {
    const res = await fetch('simpan_tagihan.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ listrik, air, wifi, sampah })
    });

    const data = await res.json();

    if (data.success) {
      showNotif('Tagihan ' + data.bulan + ' berhasil disimpan!', true);

      // Tambahkan baris baru ke tabel riwayat tanpa reload
      const tbody = document.getElementById('listTagihan');
      const emptyRow = tbody.querySelector('td[colspan]');
      if (emptyRow) emptyRow.closest('tr').remove();

      const row = document.createElement('tr');
      row.className = 'border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition';
      row.innerHTML = `
        <td class="py-4 font-medium">${data.bulan}</td>
        <td>Rp ${Math.round(data.total_tagihan).toLocaleString('id-ID')}</td>
        <td>Rp ${Math.round(data.tarif_per_bobot).toLocaleString('id-ID')}</td>
        <td>${jumlahPenghuni} orang</td>
        <td>—</td>
      `;
      tbody.prepend(row);
      resetForm();

    } else {
      showNotif('Gagal: ' + data.message, false);
    }

  } catch (err) {
    showNotif('Terjadi kesalahan koneksi ke server.', false);
    console.error(err);
  }

  btn.disabled  = false;
  btn.innerText = 'Simpan Tagihan';
}
</script>

</body>
</html>
