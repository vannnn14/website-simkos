<?php
require_once __DIR__ . '/../config/whatsapp.php';

$config = waLoadConfig();
$tokenMissing = empty($config['api_token']);

include __DIR__ . '/../config/koneksi.php';

$penghuni = [];
$totalBelum = 0;
$totalNominal = 0;

if (!$tokenMissing) {
    $q = mysqli_query($conn, "
        SELECT
            dt.id              AS detail_id,
            dt.nominal_tagihan,
            dt.status_bayar,
            dt.tagihan_listrik,
            dt.tagihan_air,
            dt.tagihan_wifi,
            dt.tagihan_sampah,
            dt.midtrans_va_number,
            dt.midtrans_va_bank,
            p.nama_lengkap,
            p.no_kamar,
            p.no_hp,
            tu.bulan,
            tu.tahun,
            tu.tenggat_pembayaran
        FROM detail_tagihan dt
        JOIN penghuni p         ON dt.penghuni_id = p.no
        JOIN tagihan_utilitas tu ON dt.tagihan_id  = tu.id
        WHERE dt.status_bayar IN ('Belum Bayar', 'Sebagian')
        ORDER BY p.nama_lengkap ASC
    ");

    while ($row = mysqli_fetch_assoc($q)) {
        $penghuni[] = $row;
        $totalBelum++;
        $totalNominal += $row['nominal_tagihan'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kirim Reminder WhatsApp - SIMKOS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>tailwind.config={darkMode:'class'}</script>
  <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'pembayaran'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">
    <div>
      <h1 class="text-3xl font-bold">Kirim Reminder WhatsApp</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Kirim pengingat pembayaran otomatis ke penghuni yang belum lunas
      </p>
    </div>
    <a href="index.php"
      class="px-5 py-3 rounded-2xl bg-white dark:bg-[#111] text-gray-700 dark:text-white border border-gray-200 dark:border-[#1f1f1f] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition shadow-sm">
      ← Kembali
    </a>
  </div>

  <?php if ($tokenMissing): ?>
  <!-- BELUM KONFIGURASI -->
  <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] text-center">
    <div class="text-6xl mb-4">⚙️</div>
    <h2 class="text-xl font-semibold mb-3">WhatsApp API Belum Dikonfigurasi</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">
      Silakan konfigurasi API token Fonnte terlebih dahulu sebelum dapat mengirim reminder.
    </p>
    <a href="../whatsapp-api/index.php"
      class="inline-block px-6 py-3 rounded-2xl bg-blue-600 text-white hover:bg-blue-700 transition">
      Konfigurasi WhatsApp API
    </a>
  </div>

  <?php elseif (empty($penghuni)): ?>
  <!-- SEMUA LUNAS -->
  <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] text-center">
    <div class="text-6xl mb-4">🎉</div>
    <h2 class="text-xl font-semibold mb-3">Semua Tagihan Lunas!</h2>
    <p class="text-gray-500 dark:text-gray-400">
      Tidak ada penghuni yang perlu diingatkan saat ini.
    </p>
  </div>

  <?php else: ?>
  <!-- STATS -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Belum Bayar</p>
      <h2 class="text-2xl font-bold text-red-600 mt-3"><?= $totalBelum ?> Orang</h2>
    </div>
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</p>
      <h2 class="text-2xl font-bold text-yellow-600 mt-3">Rp <?= number_format($totalNominal, 0, ',', '.') ?></h2>
    </div>
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Pesan akan dikirim</p>
      <h2 class="text-2xl font-bold text-blue-600 mt-3" id="selectedCount"><?= $totalBelum ?> Pesan</h2>
    </div>
  </div>

  <!-- PROGRESS (hidden by default) -->
  <div id="progressSection" class="hidden mb-8">
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold" id="progressLabel">Mengirim...</h2>
        <span class="text-sm text-gray-500" id="progressCount">0 / <?= $totalBelum ?></span>
      </div>
      <div class="w-full bg-gray-200 dark:bg-[#1a1a1a] rounded-full h-4 overflow-hidden">
        <div id="progressBar" class="bg-blue-500 h-full rounded-full transition-all duration-300" style="width:0%"></div>
      </div>
      <div id="progressResults" class="mt-4 space-y-2 max-h-60 overflow-y-auto"></div>
    </div>
  </div>

  <!-- MAIN CARD -->
  <div class="bg-white dark:bg-[#111] rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] overflow-hidden">

    <!-- TOP ACTION -->
    <div class="p-6 border-b border-gray-100 dark:border-[#1f1f1f]">
      <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div class="flex items-center gap-4">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" id="selectAll" checked
              class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            Pilih Semua
          </label>
          <span class="text-sm text-gray-500" id="selectedInfo"><?= $totalBelum ?> penghuni dipilih</span>
        </div>
        <button id="btnKirim"
          class="px-6 py-3 rounded-2xl bg-green-600 text-white hover:bg-green-700 transition shadow-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          <?= $totalBelum === 0 ? 'disabled' : '' ?>>
          Kirim WhatsApp ke <?= $totalBelum ?> Penghuni
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-gray-500 dark:text-gray-400 text-sm">
            <th class="pb-4 pl-6 w-12"></th>
            <th class="pb-4">Nama</th>
            <th class="pb-4">Kamar</th>
            <th class="pb-4">No. HP</th>
            <th class="pb-4">VA Number</th>
            <th class="pb-4">Total Tagihan</th>
            <th class="pb-4">Status</th>
            <th class="pb-4">Tenggat</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($penghuni as $i => $p): ?>
          <tr class="border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition"
            data-id="<?= $p['detail_id'] ?>">
            <td class="py-4 pl-6">
              <input type="checkbox" class="row-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
            </td>
            <td class="py-4 font-medium"><?= htmlspecialchars($p['nama_lengkap']) ?></td>
            <td class="py-4"><?= htmlspecialchars($p['no_kamar']) ?></td>
            <td class="py-4"><?= htmlspecialchars($p['no_hp']) ?></td>
            <td class="py-4">
              <?php if ($p['midtrans_va_number']): ?>
                <span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">
                  <?= strtoupper($p['midtrans_va_bank']) ?>: <?= $p['midtrans_va_number'] ?>
                </span>
              <?php else: ?>
                <span class="text-xs text-gray-400">&mdash;</span>
              <?php endif; ?>
            </td>
            <td class="py-4">Rp <?= number_format($p['nominal_tagihan'], 0, ',', '.') ?></td>
            <td class="py-4">
              <?php if ($p['status_bayar'] === 'Lunas'): ?>
                <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">Lunas</span>
              <?php elseif ($p['status_bayar'] === 'Sebagian'): ?>
                <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Sebagian</span>
              <?php else: ?>
                <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600">Belum Bayar</span>
              <?php endif; ?>
            </td>
            <td class="py-4"><?= $p['tenggat_pembayaran'] ? date('d/m/Y', strtotime($p['tenggat_pembayaran'])) : '-' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
  <?php endif; ?>

</div>

<script>
<?php if (!$tokenMissing && !empty($penghuni)): ?>

const checkboxes = document.querySelectorAll('.row-checkbox');
const selectAll = document.getElementById('selectAll');
const btnKirim = document.getElementById('btnKirim');
const selectedInfo = document.getElementById('selectedInfo');
const selectedCount = document.getElementById('selectedCount');
const progressSection = document.getElementById('progressSection');
const progressBar = document.getElementById('progressBar');
const progressLabel = document.getElementById('progressLabel');
const progressCount = document.getElementById('progressCount');
const progressResults = document.getElementById('progressResults');

function updateSelectedCount() {
  const checked = document.querySelectorAll('.row-checkbox:checked').length;
  selectedInfo.textContent = checked + ' penghuni dipilih';
  selectedCount.textContent = checked + ' Pesan';
  btnKirim.textContent = 'Kirim WhatsApp ke ' + checked + ' Penghuni';
  btnKirim.disabled = checked === 0;
}

selectAll.addEventListener('change', function() {
  checkboxes.forEach(cb => cb.checked = this.checked);
  updateSelectedCount();
});

checkboxes.forEach(cb => {
  cb.addEventListener('change', updateSelectedCount);
});

btnKirim.addEventListener('click', async function() {
  const checkedIds = [];
  document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
    const tr = cb.closest('tr');
    checkedIds.push(tr.dataset.id);
  });

  if (checkedIds.length === 0) return;

  btnKirim.disabled = true;
  btnKirim.textContent = 'Mengirim...';
  progressSection.classList.remove('hidden');
  progressResults.innerHTML = '';
  progressBar.style.width = '0%';
  progressCount.textContent = '0 / ' + checkedIds.length;

  try {
    const res = await fetch('proses_reminder.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ detail_ids: checkedIds })
    });
    const data = await res.json();

    if (data.success) {
      let completed = 0;
      data.results.forEach((r, idx) => {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 text-sm px-3 py-2 rounded-xl ' +
          (r.status === 'sent'
            ? 'bg-green-50 dark:bg-green-900/20 text-green-700'
            : 'bg-red-50 dark:bg-red-900/20 text-red-700');
        div.innerHTML = (r.status === 'sent' ? '✅' : '❌') +
          ' <span class="font-medium">' + r.nama + '</span>' +
          (r.status === 'sent' ? ' — Pesan terkirim' : ' — Gagal: ' + (r.error || 'Unknown'));
        progressResults.appendChild(div);

        completed++;
        const pct = Math.round((completed / data.results.length) * 100);
        progressBar.style.width = pct + '%';
        progressCount.textContent = completed + ' / ' + data.results.length;
      });

      progressLabel.textContent = data.failed > 0
        ? '✅ ' + data.sent + ' berhasil, ❌ ' + data.failed + ' gagal'
        : '✅ Semua pesan berhasil dikirim (' + data.sent + ')';

      if (data.failed > 0) {
        progressLabel.className = 'text-lg font-semibold text-yellow-600';
      } else {
        progressLabel.className = 'text-lg font-semibold text-green-600';
      }

      btnKirim.textContent = '✓ Selesai';
    } else {
      progressLabel.textContent = '❌ Gagal: ' + data.message;
      progressLabel.className = 'text-lg font-semibold text-red-600';
      btnKirim.disabled = false;
      btnKirim.textContent = 'Kirim WhatsApp';
    }
  } catch (e) {
    progressLabel.textContent = '❌ Terjadi kesalahan koneksi.';
    progressLabel.className = 'text-lg font-semibold text-red-600';
    btnKirim.disabled = false;
    btnKirim.textContent = 'Kirim WhatsApp';
    console.error(e);
  }
});

<?php endif; ?>
</script>

<style>
.input {
  width: 100%; height: 52px; padding: 0 16px;
  border-radius: 16px; background: #f3f4f6;
  border: 1px solid #e5e7eb; outline: none;
}
.dark .input {
  background: #0d0d0d; border: 1px solid #222; color: white;
}
</style>

</body>
</html>
