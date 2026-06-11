<?php include '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Data Penghuni - SIMKOS</title>

  <?php include '../components/theme.php'; ?>

</head>

<body class="bg-gray-100 dark:bg-[#0f0f0f]">

  <?php $pageTitle = 'Data Penghuni'; ?>
  <?php $active = 'penghuni'; ?>
  <?php include '../components/sidebar.php'; ?>

  <div class="lg:ml-64 p-4 lg:p-8 pt-4">

    <?php include '../components/topbar.php'; ?>

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">

      <!-- LEFT -->
      <div>

        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
          Data Penghuni
        </h1>

        <p class="text-gray-500 dark:text-gray-400 mt-2">
          Kelola seluruh data penghuni kos
        </p>

      </div>

      <a href="tambah.php"
   class="h-12 px-5 rounded-2xl bg-blue-600 hover:bg-blue-700
   transition text-white font-medium shadow-lg flex items-center justify-center">
  + Tambah Penghuni
</a>

    </div>

    <!-- TABLE CARD -->
    <div class="bg-white dark:bg-[#111]
    rounded-3xl shadow-xl
    border border-gray-100 dark:border-[#1f1f1f]
    overflow-hidden">

      <!-- TOP ACTION -->
      <div class="p-6 border-b border-gray-100 dark:border-[#1f1f1f]">

        <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">

          <!-- SEARCH -->
          <div class="relative w-full md:w-80">

               <input
      type="text"
      id="searchInput"
      placeholder="Cari penghuni..."
      class="w-full h-12 rounded-2xl
      bg-gray-100 dark:bg-[#0d0d0d]
      border border-gray-200 dark:border-[#222]
      px-5 outline-none
      text-gray-800 dark:text-white"
    >

          </div>

          <!-- FILTER -->
                    <select
              id="statusFilter"
              class="h-12 px-4 rounded-2xl
              bg-gray-100 dark:bg-[#0d0d0d]
              border border-gray-200 dark:border-[#222]
              text-gray-700 dark:text-gray-300
              outline-none">

              <option value="">Semua Status</option>
              <option value="aktif">Aktif</option>
              <option value="tidak aktif">Tidak Aktif</option>

          </select>

        </div>

      </div>

      <!-- TABLE -->
      <div class="overflow-x-auto relative">

        <table class="w-full">

      <!-- HEAD -->
<thead class="bg-gray-50 dark:bg-[#0d0d0d] sticky top-0 z-10">

    <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">

        <th class="px-6 py-5">No</th>

        <th class="px-6 py-5">Nama Lengkap</th>

        <th class="px-6 py-5">NIK</th>

        <th class="px-6 py-5">No Kamar</th>

        <th class="px-6 py-5">No HP</th>

        <th class="px-6 py-5">Alamat</th>

        <th class="px-6 py-5">Status Kamar</th>

        <th class="px-6 py-5">Status Pembayaran</th>

        <th class="px-6 py-5 text-center">Aksi</th>

    </tr>

</thead>
<tbody class="text-sm text-gray-700 dark:text-gray-300">

<?php
include '../config/koneksi.php';

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

if($cari != ''){
    $query = mysqli_query($conn,"
        SELECT * FROM penghuni
        WHERE nama_lengkap LIKE '%$cari%'
        OR nik LIKE '%$cari%'
        OR no_kamar LIKE '%$cari%'
        OR no_hp LIKE '%$cari%'
        OR alamat LIKE '%$cari%'
    ");
}else{
    $query = mysqli_query($conn,"SELECT * FROM penghuni");
}

while($row = mysqli_fetch_assoc($query)) :
?>

<tr
class="data-row even:bg-gray-50/50 dark:even:bg-[#0a0a0a] border-t border-gray-100 dark:border-[#1f1f1f] hover:bg-gray-50 dark:hover:bg-[#151515] transition"
data-status="<?= htmlspecialchars(strtolower(trim($row['status_kamar']))); ?>">

    <td class="px-6 py-5">
        <?= (int)$row['no']; ?>
    </td>

    <td class="px-6 py-5">
        <?= htmlspecialchars($row['nama_lengkap']); ?>
    </td>

    <td class="px-6 py-5">
        <?= htmlspecialchars($row['nik']); ?>
    </td>

    <td class="px-6 py-5">
        <?= (int)$row['no_kamar']; ?>
    </td>

    <td class="px-6 py-5">
        <?= htmlspecialchars($row['no_hp']); ?>
    </td>

    <td class="px-6 py-5">
    <?= htmlspecialchars($row['alamat']); ?>
</td>

    <td class="px-6 py-5">

        <?php if($row['status_kamar'] == 'Aktif') : ?>

            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                Aktif
            </span>

        <?php else : ?>

            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                Tidak Aktif
            </span>

        <?php endif; ?>

    </td>

    <td class="px-6 py-5">

        <?php if($row['status_pembayaran'] == 'Lunas') : ?>

            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                Lunas
            </span>

        <?php elseif($row['status_pembayaran'] == 'Menunggak') : ?>

            <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-600">
                Menunggak
            </span>

        <?php else : ?>

            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                Belum Lunas
            </span>

        <?php endif; ?>

    </td>

    <td class="px-6 py-5">

    <div class="flex items-center justify-center gap-2">

        <a href="detail.php?id=<?= (int)$row['no']; ?>"
           class="px-4 py-2 rounded-xl bg-green-100 text-green-600 text-sm font-medium hover:opacity-80">
            Detail
        </a>

        <a href="edit.php?id=<?= (int)$row['no']; ?>"
           class="px-4 py-2 rounded-xl bg-blue-100 text-blue-600 text-sm font-medium hover:opacity-80">
            Edit
        </a>

        <a href="hapus.php?id=<?= (int)$row['no']; ?>"
           onclick="return confirm('Yakin ingin menghapus data ini?')"
           class="px-4 py-2 rounded-xl bg-red-100 text-red-500 text-sm font-medium hover:opacity-80">
            Hapus
        </a>

    </div>

</td>

</tr>

<?php endwhile; ?>

</tbody>

        </table>

      </div>

    </div>

  </div>



          <script>

const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');

function filterData() {

    let keyword = searchInput.value.toLowerCase();
    let status = statusFilter.value.toLowerCase();

    let rows = document.querySelectorAll('.data-row');

    rows.forEach(function(row) {

        let text = row.textContent.toLowerCase();
        let rowStatus = row.dataset.status;

        let cocokKeyword = text.includes(keyword);

        let cocokStatus =
            status === '' ||
            rowStatus === status;

        if (cocokKeyword && cocokStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }

    });

}

searchInput.addEventListener('keyup', filterData);
statusFilter.addEventListener('change', filterData);

// ── Flash message from redirect ──────────────────────────────────────────
const urlParams = new URLSearchParams(window.location.search);
const msg = urlParams.get('msg');
const type = urlParams.get('type');
if (msg) {
  showToast(decodeURIComponent(msg), type || 'success');
  // Clean URL
  const url = new URL(window.location);
  url.searchParams.delete('msg');
  url.searchParams.delete('type');
  window.history.replaceState({}, '', url);
}
</script>

</body>
</html>