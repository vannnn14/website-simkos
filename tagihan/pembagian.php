<?php
include '../config/koneksi.php';

// Ambil daftar tagihan
$qTagihan = mysqli_query($conn, "
    SELECT 
        tu.id,
        tu.bulan,
        tu.tahun,
        tu.total_tagihan,
        tu.total_bobot,
        tu.created_at,
        COUNT(dt.id) as jumlah_penghuni
    FROM tagihan_utilitas tu
    LEFT JOIN detail_tagihan dt ON tu.id = dt.tagihan_id
    GROUP BY tu.id
    ORDER BY tu.tahun DESC, tu.bulan DESC
");

$daftar_tagihan = [];
if ($qTagihan) {
    while ($row = mysqli_fetch_assoc($qTagihan)) {
        $daftar_tagihan[] = $row;
    }
}

$selected_tagihan_id = isset($_GET['tagihan_id']) ? intval($_GET['tagihan_id']) : null;
$pembagian_data = null;

if ($selected_tagihan_id) {
    $qTagihanDetail = mysqli_query($conn, "
        SELECT 
            dt.id,
            dt.penghuni_id,
            dt.bobot,
            dt.nominal_tagihan,
            dt.status_bayar,
            dt.tagihan_listrik,
            dt.tagihan_air,
            dt.tagihan_wifi,
            dt.tagihan_sampah,
            p.nama_lengkap,
            p.no_kamar,
            p.status_kamar,
            tu.bulan,
            tu.tahun,
            tu.total_tagihan,
            tu.total_bobot,
            tu.tarif_per_bobot,
            tu.biaya_listrik,
            tu.biaya_air,
            tu.biaya_wifi,
            tu.biaya_sampah
        FROM detail_tagihan dt
        JOIN penghuni p ON dt.penghuni_id = p.no
        JOIN tagihan_utilitas tu ON dt.tagihan_id = tu.id
        WHERE dt.tagihan_id = $selected_tagihan_id
        ORDER BY p.status_kamar DESC, p.nama_lengkap ASC
    ");

    if ($qTagihanDetail && mysqli_num_rows($qTagihanDetail) > 0) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($qTagihanDetail)) {
            $rows[] = $row;
        }
        
        if (count($rows) > 0) {
            $pembagian_data = $rows;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembagian Tagihan - SIMKOS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'tagihan'; ?>
<?php include '../components/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="ml-64 p-8">
    <?php include '../components/topbar.php'; ?>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">📊 Pembagian Tagihan</h1>
            <p class="text-gray-600 dark:text-gray-400">Lihat detail bagaimana tagihan dibagi ke setiap penghuni</p>
        </div>

        <!-- Select Tagihan -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <label class="block text-sm font-semibold mb-3">Pilih Bulan Tagihan:</label>
            <?php if (count($daftar_tagihan) > 0): ?>
                <form method="GET" class="flex gap-4 items-end">
                    <select name="tagihan_id" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700" onchange="this.form.submit()">
                        <option value="">-- Pilih Tagihan --</option>
                        <?php foreach ($daftar_tagihan as $t): ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo $selected_tagihan_id == $t['id'] ? 'selected' : ''; ?>>
                                <?php echo ucfirst($t['bulan']) . ' ' . $t['tahun']; ?> 
                                (Rp <?php echo number_format(floatval($t['total_tagihan']), 0); ?>) - <?php echo intval($t['jumlah_penghuni']); ?> penghuni
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php else: ?>
                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                    <p class="text-blue-700 dark:text-blue-200">📋 Belum ada tagihan. <a href="buat_tagihan.php" class="font-bold underline">Buat tagihan baru</a></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($pembagian_data): ?>
            <?php 
                $first = $pembagian_data[0];
                $total_tagihan = floatval($first['total_tagihan']);
                $total_bobot = floatval($first['total_bobot']);
                $tarif_per_bobot = floatval($first['tarif_per_bobot']);
                
                $penghuni_aktif = 0;
                $penghuni_tidak_aktif = 0;
                $total_aktif = 0;
                $total_tidak_aktif = 0;
                
                foreach ($pembagian_data as $p) {
                    if ($p['status_kamar'] === 'Aktif') {
                        $penghuni_aktif++;
                        $total_aktif += floatval($p['nominal_tagihan']);
                    } else {
                        $penghuni_tidak_aktif++;
                        $total_tidak_aktif += floatval($p['nominal_tagihan']);
                    }
                }
            ?>

            <!-- Ringkasan Tagihan -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Total Tagihan</div>
                    <div class="text-2xl font-bold">Rp <?php echo number_format($total_tagihan, 0); ?></div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Penghuni Aktif</div>
                    <div class="text-2xl font-bold"><?php echo $penghuni_aktif; ?> orang</div>
                    <div class="text-xs mt-2">Rp <?php echo number_format($total_aktif, 0); ?></div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Penghuni Setengah Bulan</div>
                    <div class="text-2xl font-bold"><?php echo $penghuni_tidak_aktif; ?> orang</div>
                    <div class="text-xs mt-2">Rp <?php echo number_format($total_tidak_aktif, 0); ?></div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Tarif per Bobot</div>
                    <div class="text-2xl font-bold">Rp <?php echo number_format($tarif_per_bobot, 0); ?></div>
                </div>
            </div>

            <!-- Penjelasan Sistem -->
            <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-lg mb-3 flex items-center">
                    <span class="text-2xl mr-2">⚖️</span> Sistem Perhitungan Bobot Durasi Tinggal
                </h3>
                <div class="space-y-2 text-sm">
                    <p>✓ <strong>Penghuni Aktif (1 bulan):</strong> Bobot = 1.0</p>
                    <p>✓ <strong>Penghuni Setengah Bulan:</strong> Bobot = 0.5</p>
                    <p>✓ <strong>Total Bobot:</strong> (<?php echo $penghuni_aktif; ?> × 1.0) + (<?php echo $penghuni_tidak_aktif; ?> × 0.5) = <?php echo $total_bobot; ?></p>
                    <p>✓ <strong>Tarif per Bobot:</strong> Rp <?php echo number_format($total_tagihan, 0); ?> ÷ <?php echo $total_bobot; ?> = Rp <?php echo number_format($tarif_per_bobot, 2); ?></p>
                </div>
            </div>

            <!-- Tabel Pembagian -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold">Detail Pembagian Tagihan</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Per Penghuni</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-6 py-3 text-left text-sm font-semibold">No</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Penghuni</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Kamar</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Bobot</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Listrik</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Air</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Wifi</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Sampah</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Total Tagihan</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($pembagian_data as $p): ?>
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-3 text-sm"><?php echo $no++; ?></td>
                                    <td class="px-6 py-3 text-sm font-medium"><?php echo $p['nama_lengkap']; ?></td>
                                    <td class="px-6 py-3 text-sm"><?php echo $p['no_kamar']; ?></td>
                                    <td class="px-6 py-3 text-sm">
                                        <?php if ($p['status_kamar'] === 'Aktif'): ?>
                                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">FULL (1 Bulan)</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full text-xs font-semibold">SETENGAH</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right font-semibold"><?php echo floatval($p['bobot']); ?></td>
                                    <td class="px-6 py-3 text-sm text-right">Rp <?php echo number_format(floatval($p['tagihan_listrik']), 0); ?></td>
                                    <td class="px-6 py-3 text-sm text-right">Rp <?php echo number_format(floatval($p['tagihan_air']), 0); ?></td>
                                    <td class="px-6 py-3 text-sm text-right">Rp <?php echo number_format(floatval($p['tagihan_wifi']), 0); ?></td>
                                    <td class="px-6 py-3 text-sm text-right">Rp <?php echo number_format(floatval($p['tagihan_sampah']), 0); ?></td>
                                    <td class="px-6 py-3 text-sm text-right font-bold text-lg">Rp <?php echo number_format(floatval($p['nominal_tagihan']), 0); ?></td>
                                    <td class="px-6 py-3 text-sm">
                                        <?php if ($p['status_bayar'] === 'Lunas'): ?>
                                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">✓ Lunas</span>
                                        <?php elseif ($p['status_bayar'] === 'Sebagian'): ?>
                                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full text-xs font-semibold">⏳ Sebagian</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-xs font-semibold">⚠️ Belum Bayar</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary -->
                <div class="p-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center font-bold text-lg">
                        <span>TOTAL</span>
                        <span>Rp <?php echo number_format($total_tagihan, 0); ?></span>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Penjelasan -->
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-3">💡 Penjelasan</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Bagaimana Pembagian Bekerja?</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-2 text-gray-700 dark:text-gray-300">
                            <li>Sistem bobot memastikan pembagian yang adil</li>
                            <li>Penghuni aktif bayar full untuk 1 bulan penuh</li>
                            <li>Penghuni setengah bulan hanya bayar setengah</li>
                            <li>Setiap biaya dibagi proporsional berdasarkan bobot</li>
                        </ul>
                    </div>
                </div>

                <!-- Contoh -->
                <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-3">📝 Contoh Perhitungan</h3>
                    <div class="space-y-2 text-sm font-mono">
                        <p>Penghuni Aktif:</p>
                        <p class="ml-4">Rp <?php echo number_format($tarif_per_bobot, 0); ?> × 1.0 = Rp <?php echo number_format($tarif_per_bobot * 1, 0); ?></p>
                        <br />
                        <p>Penghuni Setengah:</p>
                        <p class="ml-4">Rp <?php echo number_format($tarif_per_bobot, 0); ?> × 0.5 = Rp <?php echo number_format($tarif_per_bobot * 0.5, 0); ?></p>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-8 text-center">
                <p class="text-lg text-gray-700 dark:text-gray-300">📋 Pilih bulan tagihan untuk melihat detail pembagian</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
