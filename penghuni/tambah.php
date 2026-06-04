<?php

include '../config/koneksi.php';

if(isset($_POST['simpan'])){

    $no_kamar = $_POST['no_kamar'];
    $nik = $_POST['nik'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $status_kamar = $_POST['status_kamar'];
    $status_pembayaran = $_POST['status_pembayaran'];

    $query = mysqli_query($conn, "INSERT INTO penghuni (
        no_kamar,
        nik,
        nama_lengkap,
        alamat,
        no_hp,
        status_kamar,
        status_pembayaran
    ) VALUES (
        '$no_kamar',
        '$nik',
        '$nama_lengkap',
        '$alamat',
        '$no_hp',
        '$status_kamar',
        '$status_pembayaran'
    )");

    if($query){
        echo "<script>
            alert('Data penghuni berhasil ditambahkan');
            window.location='index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menambahkan data');
        </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Penghuni - SIMKOS</title>

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

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

<?php $active = 'penghuni'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

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

        <form method="POST" class="space-y-6">

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="nama_lengkap"
                    required
                    class="w-full h-12 px-5 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none">
            </div>

            <!-- NIK -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    NIK
                </label>

                <input
                    type="text"
                    name="nik"
                    maxlength="16"
                    required
                    class="w-full h-12 px-5 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none">
            </div>

            <!-- No Kamar -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    Nomor Kamar
                </label>

                <input
                    type="number"
                    name="no_kamar"
                    required
                    class="w-full h-12 px-5 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none">
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    rows="4"
                    required
                    class="w-full px-5 py-3 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none"></textarea>
            </div>

            <!-- No HP -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    No HP
                </label>

                <input
                    type="text"
                    name="no_hp"
                    required
                    class="w-full h-12 px-5 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none">
            </div>

            <!-- Status Kamar -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    Status Kamar
                </label>

                <select
                    name="status_kamar"
                    required
                    class="w-full h-12 px-5 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none">

                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>

                </select>
            </div>

            <!-- Status Pembayaran -->
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                    Status Pembayaran
                </label>

                <select
                    name="status_pembayaran"
                    required
                    class="w-full h-12 px-5 rounded-2xl
                    bg-gray-100 dark:bg-[#0d0d0d]
                    border border-gray-200 dark:border-[#222]
                    text-gray-800 dark:text-white outline-none">

                    <option value="Lunas">Lunas</option>
                    <option value="Menunggak">Menunggak</option>
                    <option value="Belum Lunas">Belum Lunas</option>

                </select>
            </div>

            <!-- BUTTON -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-[#1f1f1f]">

                <a href="index.php"
                    class="h-12 px-6 rounded-2xl
                    bg-gray-200 dark:bg-[#1a1a1a]
                    text-gray-700 dark:text-gray-300
                    flex items-center justify-center
                    hover:opacity-80 transition">
                    Batal
                </a>

                <button
                    type="submit"
                    name="simpan"
                    class="h-12 px-6 rounded-2xl
                    bg-blue-600 hover:bg-blue-700
                    text-white font-medium shadow-lg transition">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>