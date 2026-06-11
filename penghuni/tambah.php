<?php

include '../config/koneksi.php';
include '../config/auth.php';

if (isset($_POST['simpan'])) {
    $nama_lengkap      = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nik               = mysqli_real_escape_string($conn, $_POST['nik']);
    $no_kamar          = intval($_POST['no_kamar']);
    $no_hp             = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $status_kamar = mysqli_real_escape_string($conn, $_POST['status_kamar']);
    $alamat      = mysqli_real_escape_string($conn, $_POST['alamat']);

    $query = mysqli_query($conn, "INSERT INTO penghuni (
        no_kamar, nik, nama_lengkap, alamat, no_hp,
        status_kamar, status_pembayaran
    ) VALUES (
        $no_kamar, '$nik', '$nama_lengkap', '$alamat', '$no_hp',
        '$status_kamar', 'Belum Lunas'
    )");

    if ($query) {
        header('Location: index.php?msg=' . urlencode('Data penghuni berhasil ditambahkan') . '&type=success');
        exit;
    } else {
        $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Penghuni - SIMKOS</title>

  <?php include '../components/theme.php'; ?>

</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

<?php $pageTitle = 'Tambah Penghuni'; ?>
<?php $active = 'penghuni'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="lg:ml-64 p-4 lg:p-8 pt-4">

    <?php include '../components/topbar.php'; ?>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            Tambah Penghuni
        </h1>

        <p class="text-gray-500 dark:text-gray-400 mt-2">
            Tambahkan data penghuni kos baru
        </p>
    </div>

    <div class="max-w-3xl bg-white dark:bg-[#111]
        rounded-3xl shadow-xl
        border border-gray-100 dark:border-[#1f1f1f]
        p-8">

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" name="nama_lengkap" required
                        class="w-full h-12 px-5 rounded-2xl bg-gray-100 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        NIK
                    </label>
                    <input type="text" name="nik" maxlength="16" required
                        class="w-full h-12 px-5 rounded-2xl bg-gray-100 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        Nomor HP
                    </label>
                    <input type="tel" name="no_hp" required
                        class="w-full h-12 px-5 rounded-2xl bg-gray-100 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        Nomor Kamar
                    </label>
                    <input type="number" name="no_kamar" required
                        class="w-full h-12 px-5 rounded-2xl bg-gray-100 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        Status Kamar
                    </label>
                    <select name="status_kamar" required
                        class="w-full h-12 px-5 rounded-2xl bg-gray-100 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        Alamat
                    </label>
                    <textarea name="alamat" rows="4" required
                        class="w-full px-5 py-3 rounded-2xl bg-gray-100 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222] text-gray-800 dark:text-white outline-none"></textarea>
                </div>

            </div>

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-[#1f1f1f]">

                <a href="index.php"
                    class="h-12 px-6 rounded-2xl bg-gray-200 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 flex items-center justify-center hover:opacity-80 transition">
                    Batal
                </a>

                <button type="submit" name="simpan"
                    class="h-12 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-lg transition">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>
