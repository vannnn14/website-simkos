<?php

include '../config/koneksi.php';
include '../config/auth.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo "<script>alert('ID tidak valid');window.location='index.php';</script>";
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM penghuni WHERE no = $id");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan');window.location='index.php';</script>";
    exit;
}

if (isset($_POST['hapus'])) {
    mysqli_query($conn, "DELETE FROM penghuni WHERE no = $id");

    echo "
    <script>
        alert('Data berhasil dihapus');
        window.location='index.php';
    </script>
    ";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Penghuni - SIMKOS</title>

    <?php include '../components/theme.php'; ?>

</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

<?php $active='penghuni'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

    <?php include '../components/topbar.php'; ?>

    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            Hapus Penghuni
        </h1>

        <p class="text-gray-500 dark:text-gray-400 mt-2">
            Konfirmasi penghapusan data penghuni
        </p>

    </div>

    <div class="max-w-2xl mx-auto">

        <div class="bg-white dark:bg-[#111]
        border border-gray-100 dark:border-[#1f1f1f]
        rounded-3xl shadow-xl p-8">

            <div class="flex justify-center mb-6">

                <div class="w-24 h-24 rounded-full
                bg-red-100 flex items-center justify-center">

                    <span class="text-5xl">⚠️</span>

                </div>

            </div>

            <div class="text-center">

                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Yakin ingin menghapus?
                </h2>

                <p class="text-gray-500 dark:text-gray-400 mt-3">
                    Data penghuni berikut akan dihapus secara permanen.
                </p>

            </div>

            <div class="mt-8 p-6 rounded-2xl
            bg-gray-50 dark:bg-[#181818]">

                <div class="space-y-4">

                    <div>
                        <span class="text-gray-500 text-sm">
                            Nama Penghuni
                        </span>

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            <?= htmlspecialchars($row['nama_lengkap']); ?>
                        </h3>
                    </div>

                    <div>
                        <span class="text-gray-500 text-sm">
                            NIK
                        </span>

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            <?= htmlspecialchars($row['nik']); ?>
                        </h3>
                    </div>

                    <div>
                        <span class="text-gray-500 text-sm">
                            No Kamar
                        </span>

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            <?= htmlspecialchars($row['no_kamar']); ?>
                        </h3>
                    </div>

                </div>

            </div>

            <div class="mt-6 p-4 rounded-2xl
            bg-red-50 border border-red-200">

                <p class="text-red-600 text-sm">
                    Data yang sudah dihapus tidak dapat dikembalikan lagi.
                </p>

            </div>

            <div class="flex justify-center gap-3 mt-8">

                <a href="index.php"
                    class="px-6 h-12 rounded-2xl
                    bg-gray-200 dark:bg-[#1b1b1b]
                    text-gray-700 dark:text-white
                    flex items-center items-center">
                    Batal
                </a>

                <form method="POST">

                    <button
                        type="submit"
                        name="hapus"
                        class="px-6 h-12 rounded-2xl
                        bg-red-600 hover:bg-red-700
                        text-white font-medium">
                        Ya, Hapus Data
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>
