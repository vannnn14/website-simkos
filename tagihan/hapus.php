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

$bulanLabel = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli',
    'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober',
    'November' => 'November', 'December' => 'Desember'
];

$qDetail = mysqli_query($conn, "
    SELECT dt.*, p.nama_lengkap, p.no_kamar
    FROM detail_tagihan dt
    JOIN penghuni p ON dt.penghuni_id = p.no
    WHERE dt.tagihan_id = $id
    ORDER BY p.nama_lengkap ASC
");

if (isset($_POST['hapus'])) {
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM detail_tagihan WHERE tagihan_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        $stmt2 = mysqli_prepare($conn, "DELETE FROM tagihan_utilitas WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, 'i', $id);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);
        header('Location: index.php?msg=' . urlencode('Tagihan berhasil dihapus') . '&type=success');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = 'Gagal menghapus tagihan: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Tagihan - SIMKOS</title>
    <?php include '../components/theme.php'; ?>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $pageTitle = 'Hapus Tagihan'; ?>
<?php $active = 'tagihan'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="lg:ml-64 p-4 lg:p-8 pt-4">

    <?php include '../components/topbar.php'; ?>

    <div class="max-w-2xl mx-auto mt-10">

        <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f] text-center">

            <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Hapus Tagihan</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">
                Apakah Anda yakin ingin menghapus tagihan berikut?
                <br>Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="bg-gray-50 dark:bg-[#0d0d0d] rounded-2xl p-6 mb-8 text-left">
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 text-gray-500 dark:text-gray-400 pr-4">Periode</td>
                        <td class="font-medium"><?= ($bulanLabel[$tagihan['bulan']] ?? $tagihan['bulan']) . ' ' . $tagihan['tahun'] ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500 dark:text-gray-400 pr-4">Total Tagihan</td>
                        <td class="font-medium">Rp <?= number_format($tagihan['total_tagihan'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500 dark:text-gray-400 pr-4">Penghuni</td>
                        <td class="font-medium"><?= $tagihan['total_penghuni'] ?> orang</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-500 dark:text-gray-400 pr-4">Tenggat</td>
                        <td class="font-medium"><?= date('d M Y', strtotime($tagihan['tenggat_pembayaran'])) ?></td>
                    </tr>
                </table>
            </div>

            <?php if (mysqli_num_rows($qDetail) > 0): ?>
            <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4 mb-8 text-left">
                <p class="text-sm text-yellow-700 dark:text-yellow-400 flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Data tagihan berikut juga akan ikut terhapus:
                </p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-6 list-disc">
                    <?php while ($det = mysqli_fetch_assoc($qDetail)): ?>
                        <li><?= htmlspecialchars($det['nama_lengkap']) ?> (Kamar <?= (int)$det['no_kamar'] ?>) — Rp <?= number_format($det['nominal_tagihan'], 0, ',', '.') ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-600 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="flex justify-center gap-3">
                <a href="index.php"
                    class="px-6 py-3 rounded-xl bg-gray-200 dark:bg-[#1a1a1a] hover:opacity-80 transition font-medium">
                    Batal
                </a>
                <button type="submit" name="hapus"
                    class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium transition"
                    onclick="return confirm('Yakin ingin menghapus? Semua data detail tagihan akan ikut terhapus.')">
                    Ya, Hapus Tagihan
                </button>
            </form>

        </div>

    </div>

</div>

</body>
</html>
