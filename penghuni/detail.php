<?php
include '../config/koneksi.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo "<script>alert('ID tidak valid');window.location='index.php';</script>";
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM penghuni WHERE no = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan');window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penghuni - SIMKOS</title>

    <script src="https://cdn.tailwindcss.com"></script>

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

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

    <?php $active = 'penghuni'; ?>
    <?php include '../components/sidebar.php'; ?>

    <div class="ml-64 p-8">

        <?php include '../components/topbar.php'; ?>

        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                    Detail Penghuni
                </h1>

                <p class="text-gray-500 dark:text-gray-400 mt-2">
                    Informasi lengkap penghuni kos
                </p>
            </div>

            <a href="index.php"
                class="px-5 h-12 rounded-2xl bg-gray-200 dark:bg-[#1b1b1b]
                text-gray-700 dark:text-white flex items-center justify-center">
                ← Kembali
            </a>

        </div>

        <div class="bg-white dark:bg-[#111]
        border border-gray-100 dark:border-[#1f1f1f]
        rounded-3xl shadow-xl overflow-hidden">

            <div class="p-8 border-b border-gray-100 dark:border-[#1f1f1f]">

                <div class="flex flex-col md:flex-row gap-6 items-center">

                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($data['nama_lengkap']); ?>&background=2563eb&color=fff&size=200"
                        class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">

                    <div>

                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                            <?= htmlspecialchars($data['nama_lengkap']); ?>
                        </h2>

                        <p class="text-gray-500 mt-2">
                            Penghuni Kamar <?= htmlspecialchars($data['no_kamar']); ?>
                        </p>

                        <?php if($data['status_kamar'] == 'Aktif') : ?>

                            <span class="inline-block mt-4 px-4 py-2 rounded-full bg-green-100 text-green-600 text-sm font-medium">
                                Aktif
                            </span>

                        <?php else : ?>

                            <span class="inline-block mt-4 px-4 py-2 rounded-full bg-red-100 text-red-600 text-sm font-medium">
                                Tidak Aktif
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <div class="grid md:grid-cols-2 gap-6 p-8">

                <div>
                    <label class="text-gray-500 text-sm">Nomor HP</label>
                    <div class="mt-2 p-4 rounded-2xl bg-gray-50 dark:bg-[#181818] text-gray-800 dark:text-white">
                        <?= htmlspecialchars($data['no_hp']); ?>
                    </div>
                </div>

                <div>
                    <label class="text-gray-500 text-sm">Nomor NIK</label>
                    <div class="mt-2 p-4 rounded-2xl bg-gray-50 dark:bg-[#181818] text-gray-800 dark:text-white">
                        <?= htmlspecialchars($data['nik']); ?>
                    </div>
                </div>

                <div>
                    <label class="text-gray-500 text-sm">Nomor Kamar</label>
                    <div class="mt-2 p-4 rounded-2xl bg-gray-50 dark:bg-[#181818] text-gray-800 dark:text-white">
                        <?= htmlspecialchars($data['no_kamar']); ?>
                    </div>
                </div>

                <div>
                    <label class="text-gray-500 text-sm">Status Kamar</label>
                    <div class="mt-2 p-4 rounded-2xl bg-gray-50 dark:bg-[#181818]">
                        <?php if($data['status_kamar'] == 'Aktif') : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">Aktif</span>
                        <?php else : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Tidak Aktif</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="text-gray-500 text-sm">Status Pembayaran</label>
                    <div class="mt-2 p-4 rounded-2xl bg-gray-50 dark:bg-[#181818]">
                        <?php if($data['status_pembayaran'] == 'Lunas') : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">Lunas</span>
                        <?php elseif($data['status_pembayaran'] == 'Menunggak') : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-600">Menunggak</span>
                        <?php else : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Belum Lunas</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="text-gray-500 text-sm">Alamat</label>
                    <div class="mt-2 p-4 rounded-2xl bg-gray-50 dark:bg-[#181818] text-gray-800 dark:text-white">
                        <?= htmlspecialchars($data['alamat']); ?>
                    </div>
                </div>

            </div>

            <div class="p-8 border-t border-gray-100 dark:border-[#1f1f1f]">

                <div class="flex gap-3">

                    <a href="edit.php?id=<?= (int)$data['no']; ?>"
                        class="px-5 h-12 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center">
                        Edit Data
                    </a>

                    <a href="hapus.php?id=<?= (int)$data['no']; ?>"
                        class="px-5 h-12 rounded-2xl bg-red-600 hover:bg-red-700 text-white flex items-center justify-center">
                        Hapus
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>
</html>
