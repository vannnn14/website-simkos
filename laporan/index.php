<?php
include '../config/koneksi.php';
include '../config/auth.php';

// ── Data untuk filter ─────────────────────────────────────────────────────────
$qMonths = mysqli_query($conn, "
    SELECT DISTINCT bulan, tahun FROM tagihan_utilitas
    ORDER BY tahun DESC, id DESC
");
$availableMonths = [];
while ($m = mysqli_fetch_assoc($qMonths)) {
    $availableMonths[] = $m;
}

$latestMonth = !empty($availableMonths) ? $availableMonths[0]['bulan'] : date('F');
$latestYear  = !empty($availableMonths) ? $availableMonths[0]['tahun'] : date('Y');

$selectedBulan = isset($_GET['bulan']) ? mysqli_real_escape_string($conn, $_GET['bulan']) : $latestMonth;
$selectedTahun = isset($_GET['tahun']) ? mysqli_real_escape_string($conn, $_GET['tahun']) : $latestYear;

// ── Summary cards ──────────────────────────────────────────────────────────────
$qPemasukan = mysqli_query($conn, "
    SELECT COALESCE(SUM(pb.jumlah_bayar), 0) total
    FROM pembayaran pb
    JOIN detail_tagihan dt ON pb.detail_tagihan_id = dt.id
    JOIN tagihan_utilitas tu ON dt.tagihan_id = tu.id
    WHERE pb.status = 'Diterima'
      AND tu.bulan = '$selectedBulan'
      AND tu.tahun = '$selectedTahun'
");
$pemasukan = (float)(mysqli_fetch_assoc($qPemasukan)['total'] ?? 0);

$qTagihan = mysqli_query($conn, "
    SELECT COALESCE(SUM(total_tagihan), 0) total
    FROM tagihan_utilitas
    WHERE bulan = '$selectedBulan' AND tahun = '$selectedTahun'
");
$tagihan = (float)(mysqli_fetch_assoc($qTagihan)['total'] ?? 0);

$qAktif = mysqli_query($conn, "SELECT COUNT(*) total FROM penghuni WHERE status_kamar = 'Aktif'");
$aktif = (int)(mysqli_fetch_assoc($qAktif)['total'] ?? 0);

// ── Table 1: Rekap Keuangan Per Bulan ─────────────────────────────────────────
$qRekap = mysqli_query($conn, "
    SELECT
        tu.bulan,
        tu.tahun,
        tu.total_tagihan,
        COALESCE((
            SELECT SUM(pb.jumlah_bayar)
            FROM pembayaran pb
            JOIN detail_tagihan dt2 ON pb.detail_tagihan_id = dt2.id
            WHERE dt2.tagihan_id = tu.id AND pb.status = 'Diterima'
        ), 0) as total_dibayar,
        COUNT(dt.id) as total_penghuni,
        SUM(CASE WHEN dt.status_bayar = 'Lunas' THEN 1 ELSE 0 END) as lunas_penghuni
    FROM tagihan_utilitas tu
    JOIN detail_tagihan dt ON dt.tagihan_id = tu.id
    GROUP BY tu.id
    ORDER BY tu.id DESC
");

$grandTagihan = 0;
$grandDibayar = 0;

// ── Table 2: Detail Penghuni (filtered by selected month) ─────────────────────
$qDetail = mysqli_query($conn, "
    SELECT
        pn.nama_lengkap,
        pn.no_kamar,
        pn.no_hp,
        dt.nominal_tagihan,
        COALESCE((
            SELECT SUM(pb.jumlah_bayar)
            FROM pembayaran pb
            WHERE pb.detail_tagihan_id = dt.id AND pb.status = 'Diterima'
        ), 0) as total_dibayar,
        dt.status_bayar,
        dt.id
    FROM detail_tagihan dt
    JOIN penghuni pn ON dt.penghuni_id = pn.no
    JOIN tagihan_utilitas tu ON dt.tagihan_id = tu.id
    WHERE tu.bulan = '$selectedBulan' AND tu.tahun = '$selectedTahun'
    ORDER BY pn.no_kamar ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Keuangan - SIMKOS</title>
  <?php include '../components/theme.php'; ?>
  <style>
    @media print {
      @page{margin:1.5cm}
      *{box-shadow:none!important;text-shadow:none!important}
      body{-webkit-print-color-adjust:exact;print-color-adjust:exact;font-size:11pt;line-height:1.4;color:#000!important;background:#fff!important}
      .no-print{display:none!important}
      .w-64.fixed{display:none!important}
      .ml-64{margin-left:0!important;padding:12pt!important}
      .print-only{display:block!important}
      .print-break{page-break-before:always}
      .print-break-after{page-break-after:always}
      .dark\:bg-\[#111\],.dark\:bg-\[#0f0f0f\],.dark\:bg-\[#0d0d0d\],.dark\:bg-\[#1a1a1a\],.bg-gray-100{background:#fff!important;color:#000!important}
      .dark\:text-white,.dark\:text-gray-300,.dark\:text-gray-400,.text-gray-500{color:#000!important}
      .dark\:border-\[#1f1f1f\],.dark\:border-\[#222\]{border-color:#ccc!important}
      .rounded-3xl,.rounded-2xl{border-radius:0!important}
      .grid{display:block!important}
      .grid > div{display:inline-block!important;width:23%!important;margin:0.5%!important;padding:8pt!important;border:1px solid #aaa!important;text-align:center!important;vertical-align:top!important}
      table{width:100%!important;border-collapse:collapse!important;font-size:10pt!important;margin-bottom:12pt!important}
      table th,table td{padding:6pt 8pt!important;border:1px solid #333!important;text-align:left!important}
      thead{display:table-header-group!important}
      tbody{page-break-inside:avoid}
      .shadow-xl{border:1px solid #aaa!important;margin-bottom:16pt!important}
      .px-6,.px-4{padding-left:6pt!important;padding-right:6pt!important}
      .py-5,.py-4,.py-3{padding-top:4pt!important;padding-bottom:4pt!important}
      .text-green-600,.text-blue-600,.text-purple-600,.text-red-500,.text-red-600,.text-yellow-600{color:#000!important}
      .rounded-full{border-radius:0!important;border:1px solid #333!important;padding:2pt 6pt!important;font-size:9pt!important}
      .bg-green-100{background:#e8f5e9!important}
      .bg-red-100{background:#ffebee!important}
      .bg-yellow-100{background:#fff8e1!important}
      .bg-gray-100,.bg-gray-50{background:#f5f5f5!important}
      .h-2{height:auto!important}
      .h-2 > div{height:10pt!important}
      .p-8{padding:0!important}
      .gap-4,.gap-6{display:block!important}
      .gap-4 > *,.gap-6 > *{margin-bottom:4pt!important}
      .flex.items-center{gap:4pt!important}
    }
    .print-only{display:none}
  </style>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'laporan'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="flex items-center justify-between mb-8 no-print">
    <div>
      <h1 class="text-3xl font-bold">Laporan Keuangan</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2">Rekap pemasukan, tagihan, dan status pembayaran penghuni</p>
    </div>
    <button onclick="window.print()"
      class="h-12 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-lg flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
      </svg>
      Cetak
    </button>
  </div>

  <!-- FILTER -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] mb-8 no-print">
    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
      <div>
        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
        <select name="bulan"
          class="w-full md:w-44 h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] outline-none text-gray-800 dark:text-white">
          <?php
          $monthsList = ['January','February','March','April','May','June','July','August','September','October','November','December'];
          foreach ($monthsList as $m):
          ?>
            <option value="<?= $m ?>" <?= $m === $selectedBulan ? 'selected' : '' ?>><?= $m ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
        <select name="tahun"
          class="w-full md:w-36 h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] outline-none text-gray-800 dark:text-white">
          <?php for ($t = date('Y'); $t >= 2024; $t--): ?>
            <option value="<?= $t ?>" <?= (int)$selectedTahun === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <button type="submit"
        class="h-12 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-medium">
        Tampilkan
      </button>
    </form>
  </div>

  <!-- PRINT HEADER -->
  <div class="print-only mb-6">
    <h1 style="font-size:18pt;font-weight:700;margin:0 0 4pt 0">LAPORAN KEUANGAN KOS</h1>
    <p style="font-size:11pt;margin:0 0 2pt 0;color:#555">SIMKOS — Sistem Informasi Manajemen Kos</p>
    <p style="font-size:11pt;margin:0 0 8pt 0;color:#555">Periode: <?= htmlspecialchars($selectedBulan) ?> <?= htmlspecialchars($selectedTahun) ?></p>
    <hr style="border:none;border-top:2px solid #333;margin:0 0 12pt 0">
  </div>

  <!-- SUMMARY CARDS -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-[#111] p-5 rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pemasukan</p>
      <p class="text-xl font-bold text-green-600 mt-1">Rp <?= number_format($pemasukan, 0, ',', '.') ?></p>
      <p class="text-xs text-gray-400"><?= htmlspecialchars($selectedBulan) ?></p>
    </div>
    <div class="bg-white dark:bg-[#111] p-5 rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tagihan</p>
      <p class="text-xl font-bold text-blue-600 mt-1">Rp <?= number_format($tagihan, 0, ',', '.') ?></p>
      <p class="text-xs text-gray-400"><?= htmlspecialchars($selectedBulan) ?></p>
    </div>
    <div class="bg-white dark:bg-[#111] p-5 rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Penghuni Aktif</p>
      <p class="text-xl font-bold text-purple-600 mt-1"><?= $aktif ?> Orang</p>
      <p class="text-xs text-gray-400">Total</p>
    </div>
    <div class="bg-white dark:bg-[#111] p-5 rounded-2xl shadow border border-gray-100 dark:border-[#1f1f1f]">
      <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sisa Tagihan</p>
      <p class="text-xl font-bold <?= ($tagihan - $pemasukan) > 0 ? 'text-red-600' : 'text-green-600' ?> mt-1">
        Rp <?= number_format(max(0, $tagihan - $pemasukan), 0, ',', '.') ?>
      </p>
      <p class="text-xs text-gray-400"><?= htmlspecialchars($selectedBulan) ?></p>
    </div>
  </div>

  <!-- TABLE 1: REKAP KEUANGAN PER BULAN -->
  <div class="bg-white dark:bg-[#111] rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] overflow-hidden mb-8">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-[#1f1f1f]">
      <h2 class="text-lg font-semibold">Rekap Keuangan Per Bulan</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-[#0d0d0d]">
          <tr class="text-sm text-gray-500 dark:text-gray-400">
            <th class="px-6 py-4 font-medium">Bulan</th>
            <th class="px-6 py-4 font-medium">Total Tagihan</th>
            <th class="px-6 py-4 font-medium">Total Dibayar</th>
            <th class="px-6 py-4 font-medium">Sisa</th>
            <th class="px-6 py-4 font-medium">Lunas</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($qRekap) === 0): ?>
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data tagihan.</td></tr>
          <?php endif; ?>
          <?php while ($r = mysqli_fetch_assoc($qRekap)):
            $tagih = (float)$r['total_tagihan'];
            $bayar = (float)$r['total_dibayar'];
            $sisaR = max(0, $tagih - $bayar);
            $grandTagihan += $tagih;
            $grandDibayar += $bayar;
            $pct = $tagih > 0 ? round(($bayar / $tagih) * 100) : 0;
          ?>
          <tr class="border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">
            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($r['bulan'] . ' ' . $r['tahun']) ?></td>
            <td class="px-6 py-4">Rp <?= number_format($tagih, 0, ',', '.') ?></td>
            <td class="px-6 py-4 text-green-600 font-medium">Rp <?= number_format($bayar, 0, ',', '.') ?></td>
            <td class="px-6 py-4 <?= $sisaR > 0 ? 'text-red-500' : 'text-gray-500' ?>">Rp <?= number_format($sisaR, 0, ',', '.') ?></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <div class="w-24 bg-gray-200 dark:bg-[#1a1a1a] rounded-full h-2 overflow-hidden">
                  <div class="bg-green-500 h-full rounded-full" style="width:<?= $pct ?>%"></div>
                </div>
                <span class="text-xs <?= $pct >= 100 ? 'text-green-600' : 'text-yellow-600' ?>"><?= $pct ?>%</span>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot class="bg-gray-50 dark:bg-[#0d0d0d] font-semibold">
          <tr class="border-t-2 border-gray-200 dark:border-[#333]">
            <td class="px-6 py-4">Total</td>
            <td class="px-6 py-4">Rp <?= number_format($grandTagihan, 0, ',', '.') ?></td>
            <td class="px-6 py-4 text-green-600">Rp <?= number_format($grandDibayar, 0, ',', '.') ?></td>
            <td class="px-6 py-4 <?= ($grandTagihan - $grandDibayar) > 0 ? 'text-red-500' : 'text-gray-500' ?>">
              Rp <?= number_format(max(0, $grandTagihan - $grandDibayar), 0, ',', '.') ?>
            </td>
            <td class="px-6 py-4"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- TABLE 2: DETAIL PENGHUNI -->
  <div class="bg-white dark:bg-[#111] rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-[#1f1f1f] flex justify-between items-center">
      <h2 class="text-lg font-semibold">Detail Penghuni — <?= htmlspecialchars($selectedBulan) ?> <?= htmlspecialchars($selectedTahun) ?></h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-[#0d0d0d]">
          <tr class="text-sm text-gray-500 dark:text-gray-400">
            <th class="px-6 py-4 font-medium">Nama</th>
            <th class="px-6 py-4 font-medium">Kamar</th>
            <th class="px-6 py-4 font-medium">No. HP</th>
            <th class="px-6 py-4 font-medium">Tagihan</th>
            <th class="px-6 py-4 font-medium">Dibayar</th>
            <th class="px-6 py-4 font-medium">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $subTagihan = 0;
          $subDibayar = 0;
          $hasDetail = false;
          ?>
          <?php while ($r = mysqli_fetch_assoc($qDetail)):
            $hasDetail = true;
            $subTagihan += (float)$r['nominal_tagihan'];
            $subDibayar += (float)$r['total_dibayar'];
          ?>
          <tr class="border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">
            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($r['nama_lengkap']) ?></td>
            <td class="px-6 py-4"><?= (int)$r['no_kamar'] ?></td>
            <td class="px-6 py-4"><?= htmlspecialchars($r['no_hp']) ?></td>
            <td class="px-6 py-4">Rp <?= number_format($r['nominal_tagihan'], 0, ',', '.') ?></td>
            <td class="px-6 py-4 <?= (float)$r['total_dibayar'] > 0 ? 'text-green-600' : '' ?>">
              Rp <?= number_format($r['total_dibayar'], 0, ',', '.') ?>
            </td>
            <td class="px-6 py-4">
              <?php if ($r['status_bayar'] === 'Lunas'): ?>
                <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-600 font-medium">Lunas</span>
              <?php elseif ((float)$r['total_dibayar'] > 0): ?>
                <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">Sebagian</span>
              <?php else: ?>
                <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-600 font-medium">Belum Bayar</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php if (!$hasDetail): ?>
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td></tr>
          <?php endif; ?>
        </tbody>
        <?php if ($hasDetail): ?>
        <tfoot class="bg-gray-50 dark:bg-[#0d0d0d] font-semibold">
          <tr class="border-t-2 border-gray-200 dark:border-[#333]">
            <td class="px-6 py-4" colspan="3">Subtotal</td>
            <td class="px-6 py-4">Rp <?= number_format($subTagihan, 0, ',', '.') ?></td>
            <td class="px-6 py-4 text-green-600">Rp <?= number_format($subDibayar, 0, ',', '.') ?></td>
            <td class="px-6 py-4">
              <?php $pctSub = $subTagihan > 0 ? round(($subDibayar / $subTagihan) * 100) : 0; ?>
              <span class="<?= $pctSub >= 100 ? 'text-green-600' : 'text-yellow-600' ?>"><?= $pctSub ?>% Lunas</span>
            </td>
          </tr>
        </tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <!-- SIGNATURE -->
  <div class="print-only mt-10" style="text-align:right;margin-top:24pt">
    <table style="width:100%;border:none!important">
      <tr style="border:none!important">
        <td style="border:none!important;width:60%"></td>
        <td style="border:none!important;text-align:center">
          <p style="margin:0 0 4pt 0;font-size:10pt">Mengetahui,</p>
          <p style="margin:0 0 40pt 0;font-size:10pt">Pengelola Kos</p>
          <p style="margin:0;font-size:10pt">( _______________________ )</p>
        </td>
      </tr>
    </table>
  </div>

  <!-- PRINT FOOTER -->
  <div class="print-only mt-8 text-center" style="font-size:9pt;color:#999;margin-top:8pt;border-top:1px solid #ccc;padding-top:4pt">
    <p>Dicetak pada <?= date('d/m/Y H:i') ?> — SIMKOS</p>
  </div>

</div>

<script>
  // Auto-submit form when dropdowns change
  document.querySelectorAll('select[name="bulan"], select[name="tahun"]').forEach(el => {
    el.addEventListener('change', () => el.closest('form').submit());
  });
</script>
</body>
</html>
