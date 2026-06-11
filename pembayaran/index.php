<?php
include '../config/koneksi.php';
include '../config/auth.php';

// ── Stat Cards ─────────────────────────────────────────────────────────────────
$totalPenghuni = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM penghuni")
)['total'];

$totalLunas = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM detail_tagihan WHERE status_bayar='Lunas'")
)['total'];

$totalBelumLunas = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM detail_tagihan WHERE status_bayar='Belum Bayar'")
)['total'];

$totalMenunggak = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM detail_tagihan WHERE status_bayar='Sebagian'")
)['total'];

$totalTagihan = $totalLunas + $totalBelumLunas + $totalMenunggak;

// ── Midtrans ───────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../config/midtrans.php';
$mtConnected = !empty(mtLoadConfig()['server_key']);

// ── Data grouped by month ──────────────────────────────────────────────────────
$rows = mysqli_query($conn, "
    SELECT dt.*, p.nama_lengkap, p.no_kamar, p.nik, p.no_hp,
           tu.bulan, tu.tahun, tu.tenggat_pembayaran
    FROM detail_tagihan dt
    JOIN penghuni p ON dt.penghuni_id = p.no
    JOIN tagihan_utilitas tu ON dt.tagihan_id = tu.id
    ORDER BY tu.tahun DESC,
             FIELD(tu.bulan,'January','February','March','April','May','June',
                            'July','August','September','October','November','December') DESC,
             p.no_kamar ASC
");

$months = [];
while ($row = mysqli_fetch_assoc($rows)) {
    $key = $row['tahun'] . '|' . $row['bulan'];
    $months[$key][] = $row;
}

// ── Progress bar: latest month ─────────────────────────────────────────────────
$persentase = 0;
$latestLabel = '';
reset($months);
$latestKey = key($months);
if ($latestKey && isset($months[$latestKey])) {
    $latestMonth = $months[$latestKey];
    $totalLatest = count($latestMonth);
    $lunasLatest = count(array_filter($latestMonth, function($r) {
        return $r['status_bayar'] === 'Lunas';
    }));
    $persentase = $totalLatest > 0 ? round(($lunasLatest / $totalLatest) * 100) : 0;
    $latestLabel = $months[$latestKey][0]['bulan'] . ' ' . $months[$latestKey][0]['tahun'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran - SIMKOS</title>
  <?php include '../components/theme.php'; ?>
  <style>
    .input{width:100%;height:48px;padding:0 16px;border-radius:14px;background:#f3f4f6;border:1px solid #e5e7eb;outline:none}
    .dark .input{background:#0d0d0d;border:1px solid #222;color:white}
    details summary::-webkit-details-marker{display:none}
    details summary{cursor:pointer;list-style:none}
  </style>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'pembayaran'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">
    <div>
      <h1 class="text-3xl font-bold">Pembayaran</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2">Monitoring pembayaran penghuni kos terintegrasi Midtrans</p>
    </div>
    <div class="flex gap-3">
      <a href="kirim_reminder.php"
        class="px-5 py-3 rounded-2xl inline-block bg-green-600 text-white hover:bg-green-700 transition shadow-lg">
        Kirim Reminder WhatsApp
      </a>
      <?php if ($mtConnected): ?>
      <button onclick="openGenVAModal()" id="btnGenVAMassal"
        class="px-5 py-3 rounded-2xl inline-block bg-purple-600 text-white hover:bg-purple-700 transition shadow-lg">
        Generate VA Massal
      </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- PROGRESS -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
    <div class="flex justify-between items-center mb-3">
      <h2 class="font-semibold">Progress Pembayaran <?= htmlspecialchars($latestLabel) ?></h2>
      <span class="text-sm text-green-600 font-medium"><?= $persentase ?>% Selesai</span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-[#1a1a1a] rounded-full h-3 overflow-hidden">
      <div class="bg-green-500 h-full rounded-full" style="width:<?= $persentase ?>%"></div>
    </div>
  </div>

  <!-- STATISTIK -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8 mb-8">
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Total Penghuni</p>
      <h2 class="text-2xl font-bold text-blue-600 mt-3"><?= $totalPenghuni ?> Orang</h2>
    </div>
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Lunas</p>
      <h2 class="text-2xl font-bold text-green-600 mt-3"><?= $totalLunas ?> Tagihan</h2>
    </div>
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Belum Lunas</p>
      <h2 class="text-2xl font-bold text-yellow-500 mt-3"><?= $totalBelumLunas ?> Tagihan</h2>
    </div>
    <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Menunggak</p>
      <h2 class="text-2xl font-bold text-red-600 mt-3"><?= $totalMenunggak ?> Tagihan</h2>
    </div>
  </div>

  <!-- FILTER -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl mb-8 border border-gray-100 dark:border-[#1f1f1f]">
    <div class="flex flex-col md:flex-row gap-4">
      <input type="text" id="searchInput" placeholder="Cari penghuni..." class="input md:w-1/3">
      <select id="statusFilter" class="input md:w-44">
        <option value="">Semua Status</option>
        <option value="Lunas">Lunas</option>
        <option value="Belum Bayar">Belum Bayar</option>
        <option value="Sebagian">Menunggak</option>
      </select>
      <select id="monthFilter" class="input md:w-44">
        <option value="">Semua Bulan</option>
        <?php foreach ($months as $key => $m): ?>
          <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($m[0]['bulan'] . ' ' . $m[0]['tahun']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- ACCORDION PER BULAN -->
  <div class="space-y-4" id="accordionContainer">

<?php if (empty($months)): ?>
  <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] text-center text-gray-500 dark:text-gray-400">
    Belum ada data tagihan. Buat tagihan terlebih dahulu di menu <a href="../tagihan/index.php" class="text-blue-600 underline">Tagihan Utilitas</a>.
  </div>
<?php endif; ?>

<?php $first = true; ?>
<?php foreach ($months as $key => $items): ?>
  <?php
    $totalItems = count($items);
    $lunasItems = count(array_filter($items, fn($r) => $r['status_bayar'] === 'Lunas'));
    $blnLabel = $items[0]['bulan'] . ' ' . $items[0]['tahun'];
    $countID = preg_replace('/[^a-zA-Z0-9]/', '_', $key);
  ?>
  <details class="month-group bg-white dark:bg-[#111] rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] overflow-hidden" <?= $first ? 'open' : '' ?> data-month="<?= htmlspecialchars($key) ?>">
    <summary class="flex items-center justify-between p-6 hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">
      <div class="flex items-center gap-4">
        <span class="text-xl font-bold"><?= htmlspecialchars($blnLabel) ?></span>
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-[#222] text-gray-600 dark:text-gray-400">
          <?= $lunasItems ?>/<?= $totalItems ?> Lunas
        </span>
      </div>
      <svg class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </summary>

    <div class="border-t border-gray-100 dark:border-[#1f1f1f]">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-gray-500 dark:text-gray-400 text-sm">
              <th class="px-6 py-4">Nama Lengkap</th>
              <th class="px-6 py-4">No. Kamar</th>
              <th class="px-6 py-4">No. HP</th>
              <th class="px-6 py-4">Total Tagihan</th>
              <th class="px-6 py-4">VA Number</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Aksi</th>
            </tr>
          </thead>
          <tbody class="month-body">
            <?php foreach ($items as $row): ?>
            <tr class="border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition payment-row"
              data-nama="<?= strtolower(htmlspecialchars($row['nama_lengkap'])) ?>"
              data-status="<?= htmlspecialchars($row['status_bayar']) ?>"
              data-id="<?= (int)$row['id'] ?>"
              data-penghuni-id="<?= (int)$row['penghuni_id'] ?>">

              <td class="px-6 py-4 font-medium"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
              <td class="px-6 py-4"><?= (int)$row['no_kamar'] ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($row['no_hp']) ?></td>
              <td class="px-6 py-4">Rp <?= number_format($row['nominal_tagihan'],0,',','.') ?></td>

              <td class="px-6 py-4">
                <?php if ($row['midtrans_va_number']): ?>
                  <span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">
                    <?= strtoupper(htmlspecialchars($row['midtrans_va_bank'])) ?>: <?= htmlspecialchars($row['midtrans_va_number']) ?>
                  </span>
                <?php elseif ($row['status_bayar'] !== 'Lunas' && $mtConnected): ?>
                  <button onclick="genVASingle(<?= (int)$row['id'] ?>, this)"
                    class="text-xs px-3 py-1.5 rounded-xl bg-purple-100 text-purple-700 hover:bg-purple-200 transition font-medium">
                    + VA
                  </button>
                <?php else: ?>
                  <span class="text-xs text-gray-400">&mdash;</span>
                <?php endif; ?>
              </td>

              <td class="px-6 py-4">
                <?php if($row['status_bayar'] == 'Lunas'): ?>
                  <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">Lunas</span>
                <?php elseif($row['status_bayar'] == 'Sebagian'): ?>
                  <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Sebagian</span>
                <?php else: ?>
                  <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600">Belum Bayar</span>
                <?php endif; ?>
              </td>

              <td class="px-6 py-4">
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
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </details>
<?php $first = false; ?>
<?php endforeach; ?>

  </div>

</div>

<!-- NOTIF -->
<div id="globalNotif"
  class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl shadow-xl text-sm font-medium transition-all duration-300">
</div>

<!-- MODAL VA -->
<div id="genVAModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] w-full max-w-md mx-4">
    <h3 class="text-xl font-semibold mb-2">Generate VA Massal</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Pilih bank untuk membuat Virtual Account.</p>
    <select id="genBankSelect" class="input mb-6">
      <option value="bca">BCA</option>
      <option value="bni">BNI</option>
      <option value="mandiri">Mandiri</option>
      <option value="bri">BRI</option>
    </select>
    <div class="flex justify-end gap-3">
      <button onclick="closeGenVAModal()"
        class="px-5 py-3 rounded-2xl bg-gray-200 dark:bg-[#1a1a1a] hover:opacity-80 transition">Batal</button>
      <button onclick="genVAMassal()" id="btnGenVAConfirm"
        class="px-5 py-3 rounded-2xl bg-purple-600 text-white hover:bg-purple-700 transition">Generate</button>
    </div>
    <div id="genVAResult" class="mt-4 hidden"></div>
  </div>
</div>

<script>
// ── Filter ─────────────────────────────────────────────────────────────────────
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const monthFilter = document.getElementById('monthFilter');

function filterAll() {
  const keyword = searchInput.value.toLowerCase();
  const status = statusFilter.value;
  const month = monthFilter.value;

  document.querySelectorAll('.month-group').forEach(group => {
    const groupMonth = group.dataset.month;
    const rows = group.querySelectorAll('.payment-row');
    let visibleRows = 0;

    rows.forEach(row => {
      const nama = row.dataset.nama;
      const stat = row.dataset.status;
      const matchName = nama.includes(keyword);
      const matchStatus = status === '' || stat === status;
      const show = matchName && matchStatus;
      row.style.display = show ? '' : 'none';
      if (show) visibleRows++;
    });

    const matchMonth = month === '' || groupMonth === month;
    group.style.display = matchMonth && visibleRows > 0 ? '' : 'none';
  });
}

searchInput.addEventListener('keyup', filterAll);
statusFilter.addEventListener('change', filterAll);
monthFilter.addEventListener('change', filterAll);

// ── Rotate chevron on toggle ───────────────────────────────────────────────────
document.querySelectorAll('.month-group summary').forEach(summary => {
  summary.addEventListener('click', function() {
    const detail = this.parentElement;
    // Small delay so the open/closed state has updated
    setTimeout(() => {
      const svg = this.querySelector('svg');
      if (detail.open) {
        svg.classList.add('rotate-180');
      } else {
        svg.classList.remove('rotate-180');
      }
    }, 10);
  });
});

// Set initial chevron rotation for open groups
document.querySelectorAll('.month-group[open] summary svg').forEach(svg => {
  svg.classList.add('rotate-180');
});

// ── Update status ──────────────────────────────────────────────────────────────
async function updateStatus(btn, statusBaru) {
  const tr = btn.closest('tr');
  const detailId = tr.dataset.id;
  const penghuniId = tr.dataset.penghuniId;

  btn.disabled = true;
  btn.innerText = 'Menyimpan...';

  try {
    const res = await fetch('update_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ detail_id: detailId, penghuni_id: penghuniId, status: statusBaru })
    });
    const data = await res.json();

    if (data.success) {
      const badgeTd = tr.querySelector('td:nth-child(6)');
      if (statusBaru === 'Lunas') {
        badgeTd.innerHTML = '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600">Lunas</span>';
      } else {
        badgeTd.innerHTML = '<span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600">Belum Bayar</span>';
      }

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

      // Update accordion header count
      updateMonthCounts();
    } else {
      btn.disabled = false;
      btn.innerText = statusBaru === 'Lunas' ? '✓ Tandai Lunas' : '↩ Batal Lunas';
      showGlobalNotif('Gagal: ' + data.message, false);
    }
  } catch (e) {
    btn.disabled = false;
    btn.innerText = statusBaru === 'Lunas' ? '✓ Tandai Lunas' : '↩ Batal Lunas';
    showGlobalNotif('Terjadi kesalahan koneksi.', false);
    console.error(e);
  }
}

function updateMonthCounts() {
  document.querySelectorAll('.month-group').forEach(group => {
    const rows = group.querySelectorAll('.payment-row');
    const total = rows.length;
    let lunasCount = 0;
    rows.forEach(row => {
      const badge = row.querySelector('td:nth-child(6) span');
      if (badge && badge.textContent.trim() === 'Lunas') lunasCount++;
    });
    const el = group.querySelector('summary .rounded-full');
    if (el) el.textContent = lunasCount + '/' + total + ' Lunas';
  });
}

function showGlobalNotif(msg, sukses) {
  const el = document.getElementById('globalNotif');
  el.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl shadow-xl text-sm font-medium transition-all duration-300 ${ sukses ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }`;
  el.innerText = msg;
  el.classList.remove('hidden');
  clearTimeout(el._t);
  el._t = setTimeout(() => el.classList.add('hidden'), 4000);
}

// ── VA ─────────────────────────────────────────────────────────────────────────
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
      td.innerHTML = `<span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">${r.bank.toUpperCase()}: ${r.va_number}</span>`;
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
  document.querySelectorAll('.payment-row').forEach(tr => {
    const status = tr.dataset.status;
    const vaTd = tr.querySelector('td:nth-child(5)');
    if ((status === 'Belum Bayar' || status === 'Sebagian') && vaTd && vaTd.querySelector('button')) {
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
        const tr = document.querySelector(`.payment-row[data-id="${r.detail_id}"]`);
        if (tr) {
          const vaTd = tr.querySelector('td:nth-child(5)');
          if (vaTd) {
            const btn2 = vaTd.querySelector('button');
            if (btn2) {
              btn2.outerHTML = `<span class="text-xs font-mono bg-gray-100 dark:bg-[#1a1a1a] px-2 py-1 rounded-lg">${r.bank.toUpperCase()}: ${r.va_number}</span>`;
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
    resultDiv.className = 'mt-4 p-3 rounded-xl text-sm ' + (failed > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
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
