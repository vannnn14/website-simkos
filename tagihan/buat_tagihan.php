<?php
include '../config/koneksi.php';
include '../config/auth.php';

$message = '';
$error = '';

// Handle form submission untuk create tagihan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $biaya_listrik = floatval($_POST['listrik'] ?? 0);
    $biaya_air = floatval($_POST['air'] ?? 0);
    $biaya_wifi = floatval($_POST['wifi'] ?? 0);
    $biaya_sampah = floatval($_POST['sampah'] ?? 0);
    $total_tagihan = $biaya_listrik + $biaya_air + $biaya_wifi + $biaya_sampah;

    if ($total_tagihan <= 0) {
        $error = 'Total tagihan harus lebih dari 0';
    } else {
        // Get penghuni counts
        $qAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Aktif'");
        $jumlahAktif = mysqli_fetch_assoc($qAktif)['total'];

        $qTidakAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penghuni WHERE status_kamar='Tidak Aktif'");
        $jumlahTidakAktif = mysqli_fetch_assoc($qTidakAktif)['total'];

        $totalPenghuni = $jumlahAktif + $jumlahTidakAktif;
        $totalBobot = ($jumlahAktif * 1.0) + ($jumlahTidakAktif * 0.5);

        if ($totalBobot <= 0) {
            $error = 'Tidak ada penghuni aktif untuk membuat tagihan';
        } else {
            $tarifPerBobot = $total_tagihan / $totalBobot;
            $bulan = date('F');
            $tahun = date('Y');
            $tenggat = date('Y-m-t');

            // Start transaction
            mysqli_begin_transaction($conn);

            try {
                // Insert tagihan_utilitas
                $stmtTagihan = mysqli_prepare($conn, "
                    INSERT INTO tagihan_utilitas 
                        (bulan, tahun, biaya_listrik, biaya_air, biaya_wifi, biaya_sampah,
                         total_penghuni, total_tagihan, total_bobot, tarif_per_bobot, tenggat_pembayaran)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                mysqli_stmt_bind_param(
                    $stmtTagihan,
                    'siddddiddds',
                    $bulan,
                    $tahun,
                    $biaya_listrik,
                    $biaya_air,
                    $biaya_wifi,
                    $biaya_sampah,
                    $totalPenghuni,
                    $total_tagihan,
                    $totalBobot,
                    $tarifPerBobot,
                    $tenggat
                );

                if (!mysqli_stmt_execute($stmtTagihan)) {
                    throw new Exception('Gagal insert tagihan: ' . mysqli_error($conn));
                }

                $tagihan_id = mysqli_insert_id($conn);

                // Insert detail_tagihan per penghuni
                $qPenghuni = mysqli_query($conn, "
                    SELECT no, nama_lengkap, status_kamar 
                    FROM penghuni 
                    ORDER BY status_kamar DESC, nama_lengkap ASC
                ");

                $stmtDetail = mysqli_prepare($conn, "
                    INSERT INTO detail_tagihan 
                        (tagihan_id, penghuni_id, bobot, nominal_tagihan, status_bayar,
                         tagihan_listrik, tagihan_wifi, tagihan_air, tagihan_sampah)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                while ($p = mysqli_fetch_assoc($qPenghuni)) {
                    $penghuni_id = $p['no'];
                    $bobot = ($p['status_kamar'] === 'Aktif') ? 1.0 : 0.5;

                    // Listrik dibagi proporsional berdasarkan bobot
                    $tListrik = ($biaya_listrik / $totalBobot) * $bobot;

                    // Air, Wifi, Sampah hanya untuk penghuni Aktif
                    if ($p['status_kamar'] === 'Aktif') {
                        $tAir = $jumlahAktif > 0 ? $biaya_air / $jumlahAktif : 0;
                        $tWifi = $jumlahAktif > 0 ? $biaya_wifi / $jumlahAktif : 0;
                        $tSampah = $jumlahAktif > 0 ? $biaya_sampah / $jumlahAktif : 0;
                    } else {
                        $tAir = 0;
                        $tWifi = 0;
                        $tSampah = 0;
                    }

                    $nominal_penghuni = $tListrik + $tAir + $tWifi + $tSampah;
                    $status_bayar = 'Belum Bayar';

                    mysqli_stmt_bind_param(
                        $stmtDetail,
                        'iiddsdddd',
                        $tagihan_id,
                        $penghuni_id,
                        $bobot,
                        $nominal_penghuni,
                        $status_bayar,
                        $tListrik,
                        $tWifi,
                        $tAir,
                        $tSampah
                    );

                    if (!mysqli_stmt_execute($stmtDetail)) {
                        throw new Exception('Gagal insert detail: ' . mysqli_error($conn));
                    }
                }

                mysqli_commit($conn);
                $message = '✓ Tagihan berhasil dibuat! Total: Rp ' . number_format($total_tagihan, 0);

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}

// Get penghuni info
$qPenghuni = mysqli_query($conn, "
    SELECT 
        (SELECT COUNT(*) FROM penghuni WHERE status_kamar='Aktif') as aktif,
        (SELECT COUNT(*) FROM penghuni WHERE status_kamar='Tidak Aktif') as tidak_aktif
");
$penghuni_info = mysqli_fetch_assoc($qPenghuni);
$jumlah_aktif = $penghuni_info['aktif'];
$jumlah_tidak_aktif = $penghuni_info['tidak_aktif'];
$total_bobot_calc = ($jumlah_aktif * 1.0) + ($jumlah_tidak_aktif * 0.5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tagihan - SIMKOS</title>

        <?php include '../components/theme.php'; ?>


</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'tagihan'; ?>
<?php include '../components/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="ml-64 p-8">
    <?php include '../components/topbar.php'; ?>

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">📝 Buat Tagihan Bulanan</h1>
            <p class="text-gray-600 dark:text-gray-400">Buat dan distribusikan tagihan ke penghuni berdasarkan sistem bobot durasi tinggal</p>
        </div>

        <!-- Status Penghuni -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold mb-4">📊 Status Penghuni Saat Ini</h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-300">Penghuni Aktif (1 bulan)</div>
                    <div class="text-3xl font-bold text-green-700 dark:text-green-200"><?php echo $jumlah_aktif; ?></div>
                    <div class="text-xs text-green-600 dark:text-green-300 mt-2">Bobot: <?php echo $jumlah_aktif * 1.0; ?></div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                    <div class="text-sm text-orange-600 dark:text-orange-300">Penghuni Setengah Bulan</div>
                    <div class="text-3xl font-bold text-orange-700 dark:text-orange-200"><?php echo $jumlah_tidak_aktif; ?></div>
                    <div class="text-xs text-orange-600 dark:text-orange-300 mt-2">Bobot: <?php echo $jumlah_tidak_aktif * 0.5; ?></div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-300">Total Bobot</div>
                    <div class="text-3xl font-bold text-purple-700 dark:text-purple-200"><?php echo $total_bobot_calc; ?></div>
                    <div class="text-xs text-purple-600 dark:text-purple-300 mt-2">Untuk pembagian</div>
                </div>
            </div>
        </div>

        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                <p class="text-red-700 dark:text-red-200">❌ <?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                <p class="text-green-700 dark:text-green-200"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Form Buat Tagihan -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-bold mb-4">💰 Input Biaya Bulanan</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Biaya Listrik</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                        <input type="number" name="listrik" placeholder="100000" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700"
                               required min="0" step="1000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">💡 Dibagi ke SEMUA penghuni berdasarkan bobot</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Biaya Air</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                        <input type="number" name="air" placeholder="50000" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700"
                               required min="0" step="1000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">💧 Hanya untuk penghuni AKTIF (full 1 bulan)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Biaya Wifi</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                        <input type="number" name="wifi" placeholder="100000" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700"
                               required min="0" step="1000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">📡 Hanya untuk penghuni AKTIF (full 1 bulan)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Biaya Sampah</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                        <input type="number" name="sampah" placeholder="50000" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700"
                               required min="0" step="1000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">🗑️ Hanya untuk penghuni AKTIF (full 1 bulan)</p>
                </div>

                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition">
                    ✓ Buat Tagihan Bulanan
                </button>
            </form>
        </div>

        <!-- Contoh Perhitungan -->
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6">
            <h3 class="font-bold text-lg mb-3">📖 Contoh Perhitungan</h3>
            <div class="space-y-3 text-sm">
                <p><strong>Jika Total Tagihan Rp 300.000:</strong></p>
                <div class="bg-white dark:bg-blue-800 p-4 rounded font-mono space-y-2">
                    <p>Total Bobot = (<?php echo $jumlah_aktif; ?> × 1.0) + (<?php echo $jumlah_tidak_aktif; ?> × 0.5) = <?php echo $total_bobot_calc; ?></p>
                    <p>Tarif per Bobot = Rp 300.000 ÷ <?php echo $total_bobot_calc; ?> = Rp <?php echo number_format(300000 / $total_bobot_calc, 0); ?></p>
                    <hr class="my-2">
                    <?php 
                    $tarif_contoh = 300000 / $total_bobot_calc;
                    $bayar_aktif = $tarif_contoh * 1.0;
                    $bayar_setengah = $tarif_contoh * 0.5;
                    ?>
                    <p>✓ Penghuni Aktif: Rp <?php echo number_format($bayar_aktif, 0); ?> × 1.0 = Rp <?php echo number_format($bayar_aktif, 0); ?></p>
                    <p>✓ Penghuni Setengah: Rp <?php echo number_format($tarif_contoh, 0); ?> × 0.5 = Rp <?php echo number_format($bayar_setengah, 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6">
            <h3 class="font-bold text-lg mb-3">💡 Cara Kerja Sistem</h3>
            <ul class="space-y-2 text-sm">
                <li>✓ <strong>Listrik:</strong> Dibagi ke SEMUA penghuni berdasarkan bobot (adil untuk durasi tinggal)</li>
                <li>✓ <strong>Air, Wifi, Sampah:</strong> Hanya untuk penghuni AKTIF (yang tinggal full 1 bulan)</li>
                <li>✓ <strong>Penghuni Setengah Bulan:</strong> Hanya bayar Listrik yang proporsional</li>
                <li>✓ <strong>Sistem Adil:</strong> Tidak ada biaya terbuang untuk penghuni yang hanya tinggal setengah bulan</li>
            </ul>
        </div>
    </div>
</div>

</body>
</html>
