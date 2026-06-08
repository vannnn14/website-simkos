<?php
include '../config/koneksi.php';

// ── Hitung bobot ─────────────────────────────────────────────────────────────
$qAktif      = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Aktif'");
$aktif       = mysqli_fetch_assoc($qAktif)['total'];

$qTidakAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Tidak Aktif'");
$tidakAktif  = mysqli_fetch_assoc($qTidakAktif)['total'];

$totalBobot  = ($aktif * 1) + ($tidakAktif * 0.5);
$totalSemuaPenghuni = $aktif + $tidakAktif;

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

    </div>

    <!-- PERIODE & TENGGAT MANUAL -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">

      <div>
        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">Bulan Tagihan</label>
        <?php
          $bulanList = [
            1=>'January', 2=>'February', 3=>'March', 4=>'April',
            5=>'May', 6=>'June', 7=>'July', 8=>'August',
            9=>'September', 10=>'October', 11=>'November', 12=>'December'
          ];
          $bulanLabel = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
          ];
          $bulanSekarang = intval(date('n'));
        ?>
        <select id="bulanTagihan" class="input">
          <?php foreach ($bulanList as $no => $eng): ?>
            <option value="<?= $eng ?>" <?= $no === $bulanSekarang ? 'selected' : '' ?>>
              <?= $bulanLabel[$no] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">Tahun Tagihan</label>
        <input type="number" id="tahunTagihan" class="input" value="<?= date('Y') ?>" min="2020" max="2099" placeholder="Tahun">
      </div>

      <div>
        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">Tenggat Pembayaran</label>
        <input type="date" id="tenggatTagihan" class="input" value="<?= date('Y-m-t') ?>">
      </div>

    </div>

    <div class="mt-3">
      <input type="text"
        value="Aktif: <?= $aktif ?> | Tidak Aktif: <?= $tidakAktif ?>"
        readonly
        class="input bg-gray-200 cursor-not-allowed w-full md:w-auto inline-block" style="width:auto;min-width:220px">
    </div>

    <!-- TOTAL -->
    <div class="mt-6 bg-blue-50 dark:bg-[#0b2239] p-6 rounded-2xl">
      <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</p>
      <h2 id="total" class="text-2xl font-bold text-blue-600 mt-2">Rp 0</h2>
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
            <th>Penghuni</th>
            <th>Tenggat</th>
          </tr>
        </thead>
        <tbody id="listTagihan">

          <?php if (mysqli_num_rows($qRiwayat) === 0): ?>

          <tr>
            <td colspan="4" class="py-6 text-center text-gray-400">
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

  <!-- PEMBAGIAN PER PENGHUNI - SEMUA RIWAYAT -->
  <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">

    <div class="mb-6">
      <h2 class="text-xl font-semibold">Pembagian Penghuni</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Rincian tagihan per penghuni untuk setiap periode</p>
    </div>

    <?php
    // Ambil SEMUA tagihan diurutkan terbaru dulu
    $qSemuaTagihan = mysqli_query($conn, "
      SELECT * FROM tagihan_utilitas
      ORDER BY tahun DESC, FIELD(bulan,
        'January','February','March','April','May','June',
        'July','August','September','October','November','December') DESC
    ");

    $bulanLabel = [
      'January'=>'Januari','February'=>'Februari','March'=>'Maret',
      'April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli',
      'August'=>'Agustus','September'=>'September','October'=>'Oktober',
      'November'=>'November','December'=>'Desember'
    ];

    if (mysqli_num_rows($qSemuaTagihan) === 0):
      // Belum ada tagihan — tampilkan simulasi dari penghuni aktif
      $queryPenghuni = mysqli_query($conn, "SELECT * FROM penghuni ORDER BY status_kamar DESC, nama_lengkap ASC");
    ?>
      <div class="border border-gray-100 dark:border-[#222] rounded-2xl overflow-hidden">
        <div class="bg-gray-50 dark:bg-[#1a1a1a] px-5 py-4 flex items-center gap-3">
          <span class="text-sm font-semibold text-gray-500">Simulasi Pembagian (Belum ada tagihan)</span>
        </div>
        <div class="overflow-x-auto p-4">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="text-gray-500 dark:text-gray-400">
                <th class="pb-3">Nama</th><th>Kamar</th><th>Status</th>
                <th>Listrik</th><th>Air</th><th>Wifi</th><th>Sampah</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($p = mysqli_fetch_assoc($queryPenghuni)):
                $bobot = ($p['status_kamar'] === 'Aktif') ? 1 : 0.5;
              ?>
              <tr class="penghuni-row border-t border-gray-100 dark:border-[#222]"
                  data-status="<?= $p['status_kamar'] ?>"
                  data-bobot="<?= $bobot ?>">
                <td class="py-3 font-medium"><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                <td><?= $p['no_kamar'] ?></td>
                <td>
                  <?php if ($p['status_kamar'] === 'Aktif'): ?>
                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-600 font-semibold">Aktif</span>
                  <?php else: ?>
                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-600 font-semibold">Setengah</span>
                  <?php endif; ?>
                </td>
                <td class="nominal-listrik text-gray-400">—</td>
                <td class="nominal-air text-gray-400">—</td>
                <td class="nominal-wifi text-gray-400">—</td>
                <td class="nominal-sampah text-gray-400">—</td>
                <td class="font-bold text-right text-blue-600 nominal-tagihan">—</td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php
    else:
      $isFirst = true;
      while ($tgh = mysqli_fetch_assoc($qSemuaTagihan)):
        $tId      = $tgh['id'];
        $tBulan   = $bulanLabel[$tgh['bulan']] ?? $tgh['bulan'];
        $tTahun   = $tgh['tahun'];
        $tTotal   = floatval($tgh['total_tagihan']);
        $tTenggat = date('d M Y', strtotime($tgh['tenggat_pembayaran']));
        $panelId  = 'panel-' . $tId;

        // Ambil detail penghuni untuk tagihan ini
        $qDet = mysqli_query($conn, "
          SELECT dt.*, p.nama_lengkap, p.no_kamar, p.status_kamar
          FROM detail_tagihan dt
          JOIN penghuni p ON dt.penghuni_id = p.no
          WHERE dt.tagihan_id = $tId
          ORDER BY p.status_kamar DESC, p.nama_lengkap ASC
        ");
    ?>

    <!-- ACCORDION ITEM -->
    <div class="border border-gray-100 dark:border-[#222] rounded-2xl overflow-hidden mb-3">

      <!-- HEADER (klik untuk toggle) -->
      <button type="button"
        onclick="togglePanel('<?= $panelId ?>')"
        class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 dark:bg-[#1a1a1a] hover:bg-gray-100 dark:hover:bg-[#222] transition text-left">

        <div class="flex items-center gap-3">
          <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center text-sm font-bold">
            <?= substr($tgh['bulan'], 0, 3) ?>
          </span>
          <div>
            <p class="font-semibold text-sm"><?= $tBulan . ' ' . $tTahun ?></p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Total: <strong>Rp <?= number_format($tTotal, 0, ',', '.') ?></strong>
              &nbsp;·&nbsp; <?= $tgh['total_penghuni'] ?> orang
              &nbsp;·&nbsp; Tenggat: <?= $tTenggat ?>
            </p>
          </div>
        </div>

        <svg id="icon-<?= $panelId ?>"
          class="w-5 h-5 text-gray-400 transition-transform duration-200 <?= $isFirst ? 'rotate-180' : '' ?>"
          fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>

      <!-- CONTENT -->
      <div id="<?= $panelId ?>" class="<?= $isFirst ? '' : 'hidden' ?> overflow-x-auto">
        <?php if (!$qDet || mysqli_num_rows($qDet) === 0): ?>
          <p class="px-5 py-4 text-sm text-gray-400">Tidak ada data detail penghuni untuk periode ini.</p>
        <?php else: ?>
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="text-gray-500 dark:text-gray-400 text-xs bg-gray-50 dark:bg-[#0d0d0d]">
              <th class="px-5 py-3">Nama</th>
              <th class="py-3">Kamar</th>
              <th class="py-3">Status</th>
              <th class="py-3">Listrik</th>
              <th class="py-3">Air</th>
              <th class="py-3">Wifi</th>
              <th class="py-3">Sampah</th>
              <th class="py-3 text-right pr-5">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($det = mysqli_fetch_assoc($qDet)): ?>
            <tr class="border-t border-gray-100 dark:border-[#1a1a1a] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition">
              <td class="px-5 py-3 font-medium"><?= htmlspecialchars($det['nama_lengkap']) ?></td>
              <td class="py-3"><?= $det['no_kamar'] ?></td>
              <td class="py-3">
                <?php if ($det['status_kamar'] === 'Aktif'): ?>
                  <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-600 font-semibold">Aktif</span>
                <?php else: ?>
                  <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-600 font-semibold">Setengah</span>
                <?php endif; ?>
              </td>
              <td class="py-3">Rp <?= number_format(floatval($det['tagihan_listrik']), 0, ',', '.') ?></td>
              <td class="py-3">Rp <?= number_format(floatval($det['tagihan_air']),     0, ',', '.') ?></td>
              <td class="py-3">Rp <?= number_format(floatval($det['tagihan_wifi']),    0, ',', '.') ?></td>
              <td class="py-3">Rp <?= number_format(floatval($det['tagihan_sampah']),  0, ',', '.') ?></td>
              <td class="py-3 font-bold text-right text-blue-600 pr-5">
                Rp <?= number_format(floatval($det['nominal_tagihan']), 0, ',', '.') ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
          <tfoot>
            <tr class="border-t-2 border-gray-200 dark:border-[#333] bg-gray-50 dark:bg-[#0d0d0d]">
              <td colspan="7" class="px-5 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300">Total Tagihan Bulan Ini</td>
              <td class="py-3 font-bold text-right text-blue-600 pr-5">
                Rp <?= number_format($tTotal, 0, ',', '.') ?>
              </td>
            </tr>
          </tfoot>
        </table>
        <?php endif; ?>
      </div>

    </div><!-- end accordion item -->

    <?php
        $isFirst = false;
      endwhile;
    endif;
    ?>

  </div>

</div>

<!-- SCRIPT -->
<script>
const totalBobot        = <?= $totalBobot ?>;
const jumlahAktif       = <?= $aktif ?>;
const totalSemuaPenghuni = <?= $totalSemuaPenghuni ?>;
const jumlahPenghuniDB  = <?= $totalSemuaPenghuni ?>; // dari PHP, selalu akurat

const totalText  = document.getElementById('total');
const notif      = document.getElementById('notif');

function formatRupiah(angka) {
  return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

// ── Accordion toggle ─────────────────────────────────────────────────────────
function togglePanel(id) {
  const panel = document.getElementById(id);
  const icon  = document.getElementById('icon-' + id);
  const isHidden = panel.classList.contains('hidden');
  panel.classList.toggle('hidden', !isHidden);
  icon.classList.toggle('rotate-180', isHidden);
}

function hitungTotal() {
  const listrik = Number(document.getElementById('listrik').value) || 0;
  const air     = Number(document.getElementById('air').value)     || 0;
  const wifi    = Number(document.getElementById('wifi').value)    || 0;
  const sampah  = Number(document.getElementById('sampah').value)  || 0;
  const total   = listrik + air + wifi + sampah;

  totalText.innerText = formatRupiah(total);

  if (totalBobot > 0) {
    document.querySelectorAll('.penghuni-row').forEach(row => {
      const bobot  = parseFloat(row.dataset.bobot);
      const status = row.dataset.status;

      // Aktif (Full): bayar Listrik + Air + Wifi + Sampah
      // Setengah     : bayar Air + Wifi + Sampah saja (TIDAK bayar Listrik)
      const tListrik = status === 'Aktif' && jumlahAktif > 0 ? listrik / jumlahAktif : 0;
      const tAir     = totalSemuaPenghuni > 0 ? air    / totalSemuaPenghuni : 0;
      const tWifi    = totalSemuaPenghuni > 0 ? wifi   / totalSemuaPenghuni : 0;
      const tSampah  = totalSemuaPenghuni > 0 ? sampah / totalSemuaPenghuni : 0;

      const nominal = tListrik + tAir + tWifi + tSampah;

      // Update breakdown components if they exist
      if (row.querySelector('.nominal-listrik')) {
        row.querySelector('.nominal-listrik').innerText = formatRupiah(tListrik);
        row.querySelector('.nominal-air').innerText = formatRupiah(tAir);
        row.querySelector('.nominal-wifi').innerText = formatRupiah(tWifi);
        row.querySelector('.nominal-sampah').innerText = formatRupiah(tSampah);
      }

      if (row.querySelector('.nominal-tagihan')) {
        row.querySelector('.nominal-tagihan').innerText = formatRupiah(nominal);
      }
    });
  }
}

document.querySelectorAll('#listrik,#air,#wifi,#sampah')
  .forEach(i => i.addEventListener('input', hitungTotal));

function resetForm() {
  document.querySelectorAll('#listrik,#air,#wifi,#sampah')
    .forEach(i => i.value = '');
  totalText.innerText = 'Rp 0';
  document.querySelectorAll('.nominal-tagihan')
    .forEach(el => el.innerText = 'Rp 0');
  document.querySelectorAll('.nominal-listrik,.nominal-air,.nominal-wifi,.nominal-sampah')
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

  // Gunakan data penghuni dari PHP agar tidak bergantung pada DOM yang mungkin kosong
  const jumlahPenghuni = jumlahPenghuniDB;

  if (total === 0) {
    showNotif('Isi minimal satu biaya utilitas!', false);
    return;
  }
  if (jumlahPenghuni === 0) {
    showNotif('Tidak ada penghuni terdaftar di database!', false);
    return;
  }

  const btn = document.getElementById('btnSimpan');
  btn.disabled   = true;
  btn.innerText  = 'Menyimpan...';

  const bulan   = document.getElementById('bulanTagihan').value;
  const tahun   = document.getElementById('tahunTagihan').value;
  const tenggat = document.getElementById('tenggatTagihan').value;

  if (!bulan || !tahun || !tenggat) {
    showNotif('Lengkapi bulan, tahun, dan tenggat pembayaran!', false);
    btn.disabled = false;
    btn.innerText = 'Simpan Tagihan';
    return;
  }

  try {
    const res = await fetch('simpan_tagihan.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ listrik, air, wifi, sampah, bulan, tahun, tenggat })
    });

    const data = await res.json();

    if (data.success) {
      showNotif('Tagihan ' + data.bulan + ' ' + data.tahun + ' berhasil disimpan!', true);

      // Tambahkan baris baru ke tabel riwayat tanpa reload
      const tbody = document.getElementById('listTagihan');
      const emptyRow = tbody.querySelector('td[colspan]');
      if (emptyRow) emptyRow.closest('tr').remove();

      // Format tanggal tenggat (YYYY-MM-DD) tanpa timezone shift
      const [ty, tm, td] = data.tenggat_pembayaran.split('-');
      const bulanNama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
      const tenggatStr = `${td} ${bulanNama[parseInt(tm)-1]} ${ty}`;

      const row = document.createElement('tr');
      row.className = 'border-t border-gray-100 dark:border-[#222] hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition';
      row.innerHTML = `
  <td class="py-4 font-medium">${data.bulan} ${data.tahun}</td>
  <td>Rp ${Math.round(data.total_tagihan).toLocaleString('id-ID')}</td>
  <td>${jumlahPenghuni} orang</td>
  <td>${tenggatStr}</td>
`;
      tbody.prepend(row);

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
