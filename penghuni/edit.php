<?php

include '../config/koneksi.php';

$id = $_GET['id'];

$data = mysqli_query($conn, "SELECT * FROM penghuni WHERE no='$id'");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $nama_lengkap = $_POST['nama_lengkap'];
    $nik = $_POST['nik'];
    $no_kamar = $_POST['no_kamar'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $status_kamar = $_POST['status_kamar'];
    $status_pembayaran = $_POST['status_pembayaran'];

    mysqli_query($conn, "
        UPDATE penghuni SET
        nama_lengkap='$nama_lengkap',
        nik='$nik',
        no_kamar='$no_kamar',
        alamat='$alamat',
        no_hp='$no_hp',
        status_kamar='$status_kamar',
        status_pembayaran='$status_pembayaran'
        WHERE no='$id'
    ");

    echo "
    <script>
        alert('Data berhasil diperbarui');
        window.location='index.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penghuni - SIMKOS</title>

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

                <!-- Nama Lengkap -->
                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama_lengkap"
                        value="<?= $row['nama_lengkap']; ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none">
                </div>

                <!-- NIK -->
                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        NIK
                    </label>

                    <input
                        type="text"
                        name="nik"
                        value="<?= $row['nik']; ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none">
                </div>

                <!-- No Kamar -->
                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        No Kamar
                    </label>

                    <input
                        type="number"
                        name="no_kamar"
                        value="<?= $row['no_kamar']; ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none">
                </div>

                <!-- No HP -->
                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        value="<?= $row['no_hp']; ?>"
                        required
                        class="w-full h-12 px-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none">
                </div>

                <!-- Status Kamar -->
                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Status Kamar
                    </label>

                    <select
                        name="status_kamar"
                        class="w-full h-12 px-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none">

                        <option value="Aktif" <?= ($row['status_kamar']=='Aktif') ? 'selected' : ''; ?>>
                            Aktif
                        </option>

                        <option value="Tidak Aktif" <?= ($row['status_kamar']=='Tidak Aktif') ? 'selected' : ''; ?>>
                            Tidak Aktif
                        </option>

                    </select>
                </div>

                <!-- Status Pembayaran -->
                <div>
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Status Pembayaran
                    </label>

                    <select
                        name="status_pembayaran"
                        class="w-full h-12 px-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none">

                        <option value="Lunas" <?= ($row['status_pembayaran']=='Lunas') ? 'selected' : ''; ?>>
                            Lunas
                        </option>

                        <option value="Menunggak" <?= ($row['status_pembayaran']=='Menunggak') ? 'selected' : ''; ?>>
                            Menunggak
                        </option>

                        <option value="Belum Lunas" <?= ($row['status_pembayaran']=='Belum Lunas') ? 'selected' : ''; ?>>
                            Belum Lunas
                        </option>

                    </select>
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        rows="4"
                        class="w-full p-4 rounded-2xl
                        bg-gray-50 dark:bg-[#0d0d0d]
                        border border-gray-200 dark:border-[#222]
                        text-gray-800 dark:text-white
                        outline-none"><?= $row['alamat']; ?></textarea>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-8">

                <a href="index.php"
                    class="px-5 h-12 rounded-2xl
                    bg-gray-200 dark:bg-[#1b1b1b]
                    text-gray-700 dark:text-white
                    flex items-center">
                    Batal
                </a>

                <button
                    type="submit"
                    name="update"
                    class="px-6 h-12 rounded-2xl
                    bg-blue-600 hover:bg-blue-700
                    text-white font-medium">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>