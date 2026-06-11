<?php
include '../config/koneksi.php';
include '../config/auth.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php?msg=' . urlencode('ID tagihan tidak valid') . '&type=error');
    exit;
}

$q = mysqli_query($conn, "SELECT * FROM tagihan_utilitas WHERE id = $id");
$tagihan = mysqli_fetch_assoc($q);

if (!$tagihan) {
    header('Location: index.php?msg=' . urlencode('Tagihan tidak ditemukan') . '&type=error');
    exit;
}

$bulanList = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
$bulanLabel = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$error = '';

if (isset($_POST['simpan'])) {
    $biaya_listrik = floatval($_POST['listrik'] ?? 0);
    $biaya_air     = floatval($_POST['air'] ?? 0);
    $biaya_wifi    = floatval($_POST['wifi'] ?? 0);
    $biaya_sampah  = floatval($_POST['sampah'] ?? 0);
    $bulan_text    = $_POST['bulan'];
    $tahun         = intval($_POST['tahun']);
    $tenggat       = $_POST['tenggat'];

    if ($biaya_listrik < 0 || $biaya_air < 0 || $biaya_wifi < 0 || $biaya_sampah < 0) {
        $error = 'Nilai biaya tidak boleh negatif';
    } elseif (($biaya_listrik + $biaya_air + $biaya_wifi + $biaya_sampah) <= 0) {
        $error = 'Total tagihan harus lebih dari 0';
    } elseif (!in_array($bulan_text, $bulanList)) {
        $error = 'Bulan tidak valid';
    } elseif ($tahun < 2020) {
        $error = 'Tahun tidak valid';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tenggat)) {
        $error = 'Format tenggat tidak valid';
    }

    if (empty($error)) {
        mysqli_begin_transaction($conn);
        try {
            $total_tagihan = $biaya_listrik + $biaya_air + $biaya_wifi + $biaya_sampah;

            $qAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Aktif'");
            $jumlahAktif = mysqli_fetch_assoc($qAktif)['total'];

            $qTidak = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Tidak Aktif'");
            $jumlahTidakAktif = mysqli_fetch_assoc($qTidak)['total'];

            $totalPenghuni = $jumlahAktif + $jumlahTidakAktif;
            $totalBobot = ($jumlahAktif * 1.0) + ($jumlahTidakAktif * 0.5);
            $totalSemuaPenghuni = $totalPenghuni;
            $tarifPerBobot = $totalSemuaPenghuni > 0 ? $total_tagihan / $totalSemuaPenghuni : 0;

            if ($totalBobot <= 0) {
                throw new Exception('Tidak ada penghuni aktif');
            }

            $stmt = mysqli_prepare($conn, "
                UPDATE tagihan_utilitas SET
                    bulan = ?, tahun = ?,
                    biaya_listrik = ?, biaya_air = ?, biaya_wifi = ?, biaya_sampah = ?,
                    total_penghuni = ?, total_tagihan = ?, total_bobot = ?,
                    tarif_per_bobot = ?, tenggat_pembayaran = ?
                WHERE id = ?
            ");
            mysqli_stmt_bind_param($stmt, 'siddddidddsi',
                $bulan_text, $tahun,
                $biaya_listrik, $biaya_air, $biaya_wifi, $biaya_sampah,
                $totalPenghuni, $total_tagihan, $totalBobot,
                $tarifPerBobot, $tenggat,
                $id
            );
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Gagal update tagihan');
            }

            mysqli_query($conn, "DELETE FROM detail_tagihan WHERE tagihan_id = $id");

            $qPenghuni = mysqli_query($conn, "SELECT no, nama_lengkap, status_kamar FROM penghuni ORDER BY nama_lengkap ASC");
            $stmtDetail = mysqli_prepare($conn, "
                INSERT INTO detail_tagihan
                    (tagihan_id, penghuni_id, bobot, nominal_tagihan, status_bayar,
                     tagihan_listrik, tagihan_wifi, tagihan_air, tagihan_sampah)
                VALUES (?, ?, ?, ?, 'Belum Bayar', ?, ?, ?, ?)
            ");

            while ($p = mysqli_fetch_assoc($qPenghuni)) {
                $bobot = ($p['status_kamar'] === 'Aktif') ? 1.0 : 0.5;

                if ($p['status_kamar'] === 'Aktif') {
                    $tListrik = $jumlahAktif > 0 ? $biaya_listrik / $jumlahAktif : 0;
                } else {
                    $tListrik = 0;
                }

                $tAir    = $totalSemuaPenghuni > 0 ? $biaya_air    / $totalSemuaPenghuni : 0;
                $tWifi   = $totalSemuaPenghuni > 0 ? $biaya_wifi   / $totalSemuaPenghuni : 0;
                $tSampah = $totalSemuaPenghuni > 0 ? $biaya_sampah / $totalSemuaPenghuni : 0;

                $nominal = $tListrik + $tAir + $tWifi + $tSampah;

                mysqli_stmt_bind_param($stmtDetail, 'iiddsdddd',
                    $id, $p['no'], $bobot, $nominal,
                    $tListrik, $tWifi, $tAir, $tSampah
                );
                if (!mysqli_stmt_execute($stmtDetail)) {
                    throw new Exception('Gagal insert detail untuk ' . $p['nama_lengkap']);
                }
            }

            mysqli_commit($conn);
            header('Location: index.php?msg=' . urlencode('Tagihan berhasil diperbarui') . '&type=success');
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tagihan - SIMKOS</title>
    <?php include '../components/theme.php'; ?>
    <style>.input{width:100%;height:48px;padding:0 16px;border-radius:12px;background:#f3f4f6;border:1px solid #e5e7eb;outline:none}.dark .input{background:#0d0d0d;border:1px solid #222;color:white}</style>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $pageTitle = 'Edit Tagihan'; ?>
<?php $active = 'tagihan'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="lg:ml-64 p-4 lg:p-8 pt-4">

    <?php include '../components/topbar.php'; ?>

    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Tagihan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Ubah data tagihan utilitas</p>
    </div>

    <?php if ($error): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="max-w-3xl bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">

        <form method="POST">
            <div class="grid md:grid-cols-2 gap-4">
                <input type="number" name="listrik" placeholder="Biaya Listrik" class="input" min="0" step="0.01"
                    value="<?= htmlspecialchars($tagihan['biaya_listrik']) ?>" required>
                <input type="number" name="air" placeholder="Biaya Air" class="input" min="0" step="0.01"
                    value="<?= htmlspecialchars($tagihan['biaya_air']) ?>" required>
                <input type="number" name="wifi" placeholder="Biaya Wifi" class="input" min="0" step="0.01"
                    value="<?= htmlspecialchars($tagihan['biaya_wifi']) ?>" required>
                <input type="number" name="sampah" placeholder="Biaya Sampah" class="input" min="0" step="0.01"
                    value="<?= htmlspecialchars($tagihan['biaya_sampah']) ?>" required>
            </div>

            <div class="mt-4 grid md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">Bulan</label>
                    <select name="bulan" class="input">
                        <?php foreach ($bulanList as $no => $eng): ?>
                            <option value="<?= $eng ?>" <?= $tagihan['bulan'] === $eng ? 'selected' : '' ?>>
                                <?= $bulanLabel[$no] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">Tahun</label>
                    <input type="number" name="tahun" class="input" min="2020" max="2099"
                        value="<?= (int)$tagihan['tahun'] ?>" required>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">Tenggat Pembayaran</label>
                    <input type="date" name="tenggat" class="input"
                        value="<?= htmlspecialchars($tagihan['tenggat_pembayaran']) ?>" required>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 dark:bg-[#0b2239] p-4 rounded-2xl">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Tagihan</p>
                <h2 id="totalDisplay" class="text-xl font-bold text-blue-600 mt-1">
                    Rp <?= number_format($tagihan['total_tagihan'], 0, ',', '.') ?>
                </h2>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="index.php"
                    class="px-6 py-3 rounded-xl bg-gray-200 dark:bg-[#1a1a1a] hover:opacity-80 transition text-center">
                    Batal
                </a>
                <button type="submit" name="simpan"
                    class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}
document.querySelectorAll('input[name="listrik"],input[name="air"],input[name="wifi"],input[name="sampah"]').forEach(function(el) {
    el.addEventListener('input', function() {
        var listrik = Number(document.querySelector('input[name="listrik"]').value) || 0;
        var air     = Number(document.querySelector('input[name="air"]').value) || 0;
        var wifi    = Number(document.querySelector('input[name="wifi"]').value) || 0;
        var sampah  = Number(document.querySelector('input[name="sampah"]').value) || 0;
        document.getElementById('totalDisplay').innerText = formatRupiah(listrik + air + wifi + sampah);
    });
});
</script>

</body>
</html>
