<?php
include '../config/koneksi.php';

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

require_once __DIR__ . '/../config/midtrans.php';
$mtConnected = !empty(mtLoadConfig()['server_key']);

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

      <a href="kirim_reminder.php"
        class="px-5 py-3 rounded-2xl inline-block
        bg-green-600 text-white
        hover:bg-green-700 transition shadow-lg">
        Kirim Reminder WhatsApp
      </a>

      <?php if ($mtConnected): ?>
      <button onclick="openGenVAModal()" id="btnGenVAMassal"
        class="px-5 py-3 rounded-2xl inline-block
        bg-purple-600 text-white
        hover:bg-purple-700 transition shadow-lg">
        Generate VA Massal
      </button>
      <?php endif; ?>

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
            <th>No. HP</th>
            <th>Total Tagihan</th>
            <th>VA Number</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>

        </thead>

<tbody id="paymentTable">

<?php while($row = mysqli_fetch_assoc($qPembayaran)): ?>

<tr class="border-t border-gray-100 dark:border-[#222]
hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition"
data-nama="<?= strtolower($row['nama_lengkap']) ?>"
data-status="<?= $row['status_bayar'] ?>"
data-id="<?= $row['id'] ?>"
data-penghuni-id="<?= $row['penghuni_id'] ?>">

    <td class="py-4 font-medium"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
    <td><?= $row['no_kamar'] ?></td>
    <td><?= $row['no_hp'] ?></td>
    <td>Rp <?= number_format($row['nominal_tagihan'],0,',','.') ?></td>

    <td>
      <?php if ($row['midtrans_va_number']): ?>
        <span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">
          <?= strtoupper($row['midtrans_va_bank']) ?>: <?= $row['midtrans_va_number'] ?>
        </span>
      <?php elseif ($row['status_bayar'] !== 'Lunas' && $mtConnected): ?>
        <button onclick="genVASingle(<?= $row['id'] ?>, this)"
          class="text-xs px-3 py-1.5 rounded-xl bg-purple-100 text-purple-700 hover:bg-purple-200 transition font-medium">
          + VA
        </button>
      <?php else: ?>
        <span class="text-xs text-gray-400">&mdash;</span>
      <?php endif; ?>
    </td>

    <td>
        <?php if($row['status_bayar'] == 'Lunas'): ?>
            <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">Lunas</span>
        <?php elseif($row['status_bayar'] == 'Sebagian'): ?>
            <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Sebagian</span>
        <?php else: ?>
            <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600">Belum Bayar</span>
        <?php endif; ?>
    </td>

    <td>
        <div class="flex gap-2">
            <?php if ($row['status_bayar'] !== 'Lunas'): ?>
            <button onclick="updateStatus(this, 'Lunas')"
              class="btn-status px-3 py-2 rounded-xl bg-green-100 text-green-700 text-sm font-medium hover:bg-green-200 transition">
                ✓ Tandai Lunas
            </button>
            <?php endif; ?>
            <?php if ($row['status_bayar'] === 'Lunas'): ?>
            <button onclick="updateStatus(this, 'Belum Bayar')"
              class="btn-status px-3 py-2 rounded-xl bg-gray-100 text-gray-600 text-sm font-medium hover:bg-gray-200 transition">
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

<!-- MODAL GENERATE VA -->
<div id="genVAModal"
  class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] w-full max-w-md mx-4">
    <h3 class="text-xl font-semibold mb-2">Generate VA Massal</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
      Pilih bank untuk membuat Virtual Account bagi penghuni yang belum memiliki VA.
    </p>
    <select id="genBankSelect" class="input mb-6">
      <option value="bca">BCA</option>
      <option value="bni">BNI</option>
      <option value="mandiri">Mandiri</option>
      <option value="bri">BRI</option>
    </select>
    <div class="flex justify-end gap-3">
      <button onclick="closeGenVAModal()"
        class="px-5 py-3 rounded-2xl bg-gray-200 dark:bg-[#1a1a1a] hover:opacity-80 transition">
        Batal
      </button>
      <button onclick="genVAMassal()" id="btnGenVAConfirm"
        class="px-5 py-3 rounded-2xl bg-purple-600 text-white hover:bg-purple-700 transition">
        Generate
      </button>
    </div>
    <div id="genVAResult" class="mt-4 hidden"></div>
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

// ── Generate VA ──────────────────────────────────────────────────────────────
function openGenVAModal() {
  document.getElementById('genVAModal').classList.remove('hidden');
  document.getElementById('genVAResult').classList.add('hidden');
}

function closeGenVAModal() {
  document.getElementById('genVAModal').classList.add('hidden');
}

async function genVASingle(detailId, btn) {
  const bank = prompt('Pilih bank (bca / bni / mandiri / bri):', 'bca');
  if (!bank) return;

  btn.disabled = true;
  btn.textContent = 'Memproses...';

  try {
    const res = await fetch('gen_va.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ detail_ids: [detailId], bank: bank })
    });
    const data = await res.json();

    if (data.success && data.results[0]?.status === 'success') {
      const r = data.results[0];
      const td = btn.closest('td');
      td.innerHTML = `<span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">
        ${r.bank.toUpperCase()}: ${r.va_number}</span>`;
      showGlobalNotif('✅ VA berhasil digenerate untuk ' + r.nama, true);
    } else {
      const err = data.results[0]?.error || 'Gagal generate VA';
      btn.disabled = false;
      btn.textContent = '+ VA';
      showGlobalNotif('❌ ' + err, false);
    }
  } catch (e) {
    btn.disabled = false;
    btn.textContent = '+ VA';
    showGlobalNotif('❌ Terjadi kesalahan koneksi.', false);
    console.error(e);
  }
}

async function genVAMassal() {
  const bank = document.getElementById('genBankSelect').value;
  const btn = document.getElementById('btnGenVAConfirm');
  const resultDiv = document.getElementById('genVAResult');

  btn.disabled = true;
  btn.textContent = 'Memproses...';
  resultDiv.classList.add('hidden');

  const ids = [];
  document.querySelectorAll('#paymentTable tr').forEach(tr => {
    const status = tr.dataset.status;
    const vaTd = tr.querySelector('td:nth-child(5)');
    if ((status === 'Belum Bayar' || status === 'Sebagian') && vaTd && vaTd.textContent.trim() === '') {
      ids.push(tr.dataset.id);
    }
  });

  if (ids.length === 0) {
    resultDiv.className = 'mt-4 p-3 rounded-xl bg-yellow-100 text-yellow-700 text-sm';
    resultDiv.textContent = 'Semua penghuni sudah memiliki VA.';
    resultDiv.classList.remove('hidden');
    btn.disabled = false;
    btn.textContent = 'Generate';
    return;
  }

  try {
    const res = await fetch('gen_va.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ detail_ids: ids, bank: bank })
    });
    const data = await res.json();

    let html = '';
    let success = 0, failed = 0, skipped = 0;

    data.results.forEach(r => {
      if (r.status === 'success') {
        success++;
        const tr = document.querySelector(`#paymentTable tr[data-id="${r.detail_id}"]`);
        if (tr) {
          const vaTd = tr.querySelector('td:nth-child(5)');
          if (vaTd) {
            const btn = vaTd.querySelector('button');
            if (btn) {
              btn.outerHTML = `<span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">
                ${r.bank.toUpperCase()}: ${r.va_number}</span>`;
            }
          }
        }
      } else if (r.status === 'skipped') {
        skipped++;
      } else {
        failed++;
        html += `<div class="text-red-600">❌ ${r.nama}: ${r.error}</div>`;
      }
    });

    const totalMsg = `✅ ${success} berhasil`;
    const skipMsg = skipped > 0 ? `, ⏭ ${skipped} sudah ada` : '';
    const failMsg = failed > 0 ? `, ❌ ${failed} gagal` : '';

    resultDiv.className = 'mt-4 p-3 rounded-xl text-sm ' +
      (failed > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
    resultDiv.innerHTML = '<strong>' + totalMsg + skipMsg + failMsg + '</strong>' + html;
    resultDiv.classList.remove('hidden');

    showGlobalNotif(totalMsg + skipMsg + failMsg, failed === 0);
  } catch (e) {
    resultDiv.className = 'mt-4 p-3 rounded-xl bg-red-100 text-red-700 text-sm';
    resultDiv.textContent = '❌ Terjadi kesalahan koneksi.';
    resultDiv.classList.remove('hidden');
    showGlobalNotif('❌ Gagal generate VA.', false);
    console.error(e);
  }

  btn.disabled = false;
  btn.textContent = 'Generate';
}

</script>
</body>
</html>