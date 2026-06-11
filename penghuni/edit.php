<?php

include '../config/koneksi.php';
include '../config/auth.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php?msg=' . urlencode('ID tidak valid') . '&type=error');
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM penghuni WHERE no = $id");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    header('Location: index.php?msg=' . urlencode('Data tidak ditemukan') . '&type=error');
    exit;
}

if (isset($_POST['update'])) {
    $nama_lengkap      = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nik               = mysqli_real_escape_string($conn, $_POST['nik']);
    $no_kamar          = intval($_POST['no_kamar']);
    $no_hp             = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $status_kamar = mysqli_real_escape_string($conn, $_POST['status_kamar']);
    $alamat       = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn, "
        UPDATE penghuni SET
            nama_lengkap = '$nama_lengkap',
            nik          = '$nik',
            no_kamar     = $no_kamar,
            no_hp        = '$no_hp',
            status_kamar = '$status_kamar',
            alamat       = '$alamat'
        WHERE no = $id
    ");

    header('Location: index.php?msg=' . urlencode('Data berhasil diperbarui') . '&type=success');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penghuni - SIMKOS</title>

    <?php include '../components/theme.php'; ?>

</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

<?php $pageTitle = 'Edit Penghuni'; ?>
<?php $active = 'penghuni'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="lg:ml-64 p-4 lg:p-8 pt-4">

    <?php include '../components/topbar.php'; ?>

    <div class="flex items-center justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                Edit Penghuni
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mt-2">
                Ubah informasi data penghuni kos
            </p>
        </div>

        <a href="index.php"
            class="h-12 px-5 rounded-2xl
            bg-gray-200 dark:bg-[#1b1b1b]
            text-gray-700 dark:text-white
            flex items-center justify-center">
            ← Kembali
        </a>

    </div>

    <div class="bg-white dark:bg-[#111]
    rounded-3xl shadow-xl
    border border-gray-100 dark:border-[#1f1f1f]
    p-8">

        <form method="POST">

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Nama Lengkap
                    </label>
                    <input type="text" name="nama_lengkap"
                        value="<?= htmlspecialchars($row['nama_lengkap']); ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        NIK
                    </label>
                    <input type="text" name="nik" maxlength="16"
                        value="<?= htmlspecialchars($row['nik']); ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Nomor HP
                    </label>
                    <input type="tel" name="no_hp"
                        value="<?= htmlspecialchars($row['no_hp']); ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        No Kamar
                    </label>
                    <input type="number" name="no_kamar"
                        value="<?= htmlspecialchars($row['no_kamar']); ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Status Kamar
                    </label>
                    <select name="status_kamar"
                        class="w-full h-12 px-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                        <option value="Aktif" <?= $row['status_kamar']=='Aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Tidak Aktif" <?= $row['status_kamar']=='Tidak Aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Status Pembayaran
                    </label>
                    <div class="h-12 px-4 rounded-2xl bg-gray-100 dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222] flex items-center text-gray-800 dark:text-white">
                        <?php if ($row['status_pembayaran'] == 'Lunas') : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">Lunas</span>
                        <?php elseif ($row['status_pembayaran'] == 'Menunggak') : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-600">Menunggak</span>
                        <?php else : ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Belum Lunas</span>
                        <?php endif; ?>
                        <span class="ml-2 text-xs text-gray-400">(otomatis dari tagihan)</span>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Alamat
                    </label>
                    <textarea name="alamat" rows="4"
                        class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none"><?= htmlspecialchars($row['alamat']); ?></textarea>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-8">

                <a href="index.php"
                    class="px-5 h-12 rounded-2xl bg-gray-200 dark:bg-[#1b1b1b] text-gray-700 dark:text-white flex items-center">
                    Batal
                </a>

                <button type="submit" name="update"
                    class="px-6 h-12 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-medium">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>
