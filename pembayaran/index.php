<?php
include '../config/koneksi.php';

// =======================
// STATISTIK PEMBAYARAN
// =======================

$totalPenghuni = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM penghuni")
)['total'];

$totalLunas = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) total
        FROM detail_tagihan
        WHERE status_bayar='Lunas'
    ")
)['total'];

$totalBelumLunas = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) total
        FROM detail_tagihan
        WHERE status_bayar='Belum Bayar'
    ")
)['total'];

$totalMenunggak = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) total
        FROM detail_tagihan
        WHERE status_bayar='Sebagian'
    ")
)['total'];

$totalTagihan = $totalLunas + $totalBelumLunas + $totalMenunggak;

$persentase = 0;

if($totalTagihan > 0){
    $persentase = round(($totalLunas/$totalTagihan)*100);
}

$qPembayaran = mysqli_query($conn,"
SELECT
dt.*,
p.nama_lengkap,
p.no_kamar,
p.nik,
p.no_hp
FROM detail_tagihan dt
JOIN penghuni p
ON dt.penghuni_id = p.no
ORDER BY dt.id DESC
");
?>




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
                <a href="generate_pembayaran.php"
          class="px-5 py-3 rounded-2xl
          bg-blue-600 text-white
          hover:bg-blue-700 transition shadow-lg">

          + Generate Pembayaran

          </a>

    </div>

  </div>

  <!-- PROGRESS -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">

    <div class="flex justify-between items-center mb-3">

      <h2 class="font-semibold">
        Progress Pembayaran Bulan Ini
      </h2>

      <span class="text-sm text-green-600 font-medium">
      <?= $persentase ?>% Selesai
      </span>

    </div>

    <div class="w-full bg-gray-200 dark:bg-[#1a1a1a]
      rounded-full h-3 overflow-hidden">

      <div class="bg-green-500 h-full rounded-full"
style="width:<?= $persentase ?>%"      </div>

    </div>

  </div>

  <!-- STATISTIK -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8 mb-8">

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Total Penghuni
      </p>

      <h2 class="text-2xl font-bold text-blue-600 mt-3">
      <?= $totalPenghuni ?> Orang      
      </h2>

    </div>

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Lunas
      </p>

      <h2 class="text-2xl font-bold text-green-600 mt-3">
      <?= $totalLunas ?> Orang
      </h2>

    </div>

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Belum Lunas
      </p>

      <h2 class="text-2xl font-bold text-yellow-500 mt-3">
      <?= $totalBelumLunas ?> Tagihan
      </h2>

    </div>

    <!-- CARD -->
    <div class="bg-white dark:bg-[#111]
      p-6 rounded-3xl shadow-xl
      border border-gray-100 dark:border-[#1f1f1f]">

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Menunggak
      </p>

      <h2 class="text-2xl font-bold text-red-600 mt-3">
      <?= $totalMenunggak ?> Orang
      </h2>

    </div>

  </div>

  <!-- FILTER -->
  <div class="bg-white dark:bg-[#111]
    p-6 rounded-3xl shadow-xl mb-8
    border border-gray-100 dark:border-[#1f1f1f]">

    <div class="flex flex-col md:flex-row gap-4 justify-between">

      <!-- SEARCH -->
      <input
type="text"
id="searchInput"
placeholder="Cari penghuni..."
class="input md:w-1/3">

<select
id="statusFilter"
class="input md:w-52">

<option value="">Semua Status</option>
<option value="Lunas">Lunas</option>
<option value="Belum Bayar">Belum Bayar</option>
<option value="Sebagian">Menunggak</option>

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

            <th class="pb-4">Nama Lengkap</th>
            <th>No. Kamar</th>
            <th>NIK</th>
            <th>No. HP</th>
            <th>Total Tagihan</th>
            <th>Status Pembayaran</th>
            <th>Aksi</th>


          </tr>

        </thead>

<tbody id="paymentTable">

<?php while($row = mysqli_fetch_assoc($qPembayaran)): ?>

<tr class="border-t border-gray-100 dark:border-[#222]
hover:bg-gray-50 dark:hover:bg-[#1a1a1a]
transition"
data-nama="<?= strtolower($row['nama_lengkap']) ?>"
data-status="<?= $row['status_bayar'] ?>"
data-id="<?= $row['id'] ?>"
data-penghuni-id="<?= $row['penghuni_id'] ?>"
>

    <td class="py-4 font-medium">
        <?= htmlspecialchars($row['nama_lengkap']) ?>
    </td>

    <td>
        <?= $row['no_kamar'] ?>
    </td>

    <td>
        <?= $row['nik'] ?>
    </td>

    <td>
        <?= $row['no_hp'] ?>
    </td>

    <td>
        Rp <?= number_format($row['nominal_tagihan'],0,',','.') ?>
    </td>

    <td>

        <?php if($row['status_bayar'] == 'Lunas'): ?>

            <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">
                Lunas
            </span>

        <?php elseif($row['status_bayar'] == 'Sebagian'): ?>

            <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                Sebagian
            </span>

        <?php else: ?>

            <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600">
                Belum Bayar
            </span>

        <?php endif; ?>

    </td>

    <td>

        <div class="flex gap-2">

            <?php if ($row['status_bayar'] !== 'Lunas'): ?>
            <button
            onclick="updateStatus(this, 'Lunas')"
            class="btn-status px-3 py-2 rounded-xl
            bg-green-100 text-green-700 text-sm font-medium
            hover:bg-green-200 transition">
                ✓ Tandai Lunas
            </button>
            <?php endif; ?>

            <?php if ($row['status_bayar'] === 'Lunas'): ?>
            <button
            onclick="updateStatus(this, 'Belum Bayar')"
            class="btn-status px-3 py-2 rounded-xl
            bg-gray-100 text-gray-600 text-sm font-medium
            hover:bg-gray-200 transition">
                ↩ Batal Lunas
            </button>
            <?php endif; ?>

        </div>

    </td>

</tr>

<?php endwhile; ?>

</tbody>


      </table>

    </div>

  </div>

</div>

<!-- NOTIF GLOBAL -->
<div id="globalNotif"
  class="hidden fixed bottom-6 right-6 z-50
  px-5 py-3 rounded-2xl shadow-xl text-sm font-medium
  transition-all duration-300">
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
<script>

const searchInput = document.getElementById("searchInput");
const statusFilter = document.getElementById("statusFilter");

function filterTable(){

    let keyword = searchInput.value.toLowerCase();
    let status = statusFilter.value;

    document.querySelectorAll("#paymentTable tr").forEach(row=>{

        let nama = row.dataset.nama;
        let statusRow = row.dataset.status;

        let cocokNama = nama.includes(keyword);
        let cocokStatus = status === "" || statusRow === status;

        row.style.display =
            (cocokNama && cocokStatus)
            ? ""
            : "none";

    });

}

searchInput.addEventListener("keyup", filterTable);
statusFilter.addEventListener("change", filterTable);

// ── Update status pembayaran ─────────────────────────────────────────────────
async function updateStatus(btn, statusBaru) {
  const tr         = btn.closest('tr');
  const detailId   = tr.dataset.id;
  const penghuniId = tr.dataset.penghuniId;

  btn.disabled  = true;
  btn.innerText = 'Menyimpan...';

  try {
    const res  = await fetch('update_status.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        detail_id:   detailId,
        penghuni_id: penghuniId,
        status:      statusBaru
      })
    });
    const data = await res.json();

    if (data.success) {
      // ── Perbarui badge status ──────────────────────────────────────────────
      const badgeTd = tr.querySelector('td:nth-child(6)');
      if (statusBaru === 'Lunas') {
        badgeTd.innerHTML = '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">Lunas</span>';
      } else if (statusBaru === 'Sebagian') {
        badgeTd.innerHTML = '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Sebagian</span>';
      } else {
        badgeTd.innerHTML = '<span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600">Belum Bayar</span>';
      }

      // ── Perbarui tombol aksi ───────────────────────────────────────────────
      tr.dataset.status = statusBaru;
      const aksDiv = tr.querySelector('td:last-child div');
      if (statusBaru === 'Lunas') {
        aksDiv.innerHTML = `<button onclick="updateStatus(this,'Belum Bayar')"
          class="btn-status px-3 py-2 rounded-xl bg-gray-100 text-gray-600 text-sm font-medium hover:bg-gray-200 transition">
          ↩ Batal Lunas</button>`;
      } else {
        aksDiv.innerHTML = `<button onclick="updateStatus(this,'Lunas')"
          class="btn-status px-3 py-2 rounded-xl bg-green-100 text-green-700 text-sm font-medium hover:bg-green-200 transition">
          ✓ Tandai Lunas</button>`;
      }

      showGlobalNotif(
        statusBaru === 'Lunas'
          ? '✅ Pembayaran ditandai Lunas! Status penghuni: ' + data.status_penghuni
          : '↩ Status dikembalikan ke ' + statusBaru,
        true
      );
    } else {
      btn.disabled  = false;
      btn.innerText = statusBaru === 'Lunas' ? '✓ Tandai Lunas' : '↩ Batal Lunas';
      showGlobalNotif('Gagal: ' + data.message, false);
    }
  } catch (e) {
    btn.disabled  = false;
    btn.innerText = statusBaru === 'Lunas' ? '✓ Tandai Lunas' : '↩ Batal Lunas';
    showGlobalNotif('Terjadi kesalahan koneksi.', false);
    console.error(e);
  }
}

function showGlobalNotif(msg, sukses) {
  const el = document.getElementById('globalNotif');
  el.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl shadow-xl text-sm font-medium transition-all duration-300
    ${ sukses ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }`;
  el.innerText = msg;
  el.classList.remove('hidden');
  clearTimeout(el._t);
  el._t = setTimeout(() => el.classList.add('hidden'), 4000);
}

</script>
</body>
</html>