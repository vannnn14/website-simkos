<?php
include '../config/auth.php';
require_once __DIR__ . '/../config/midtrans.php';

$config      = mtLoadConfig();
$message     = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $serverKey  = $_POST['server_key']  ?? '';
        $clientKey  = $_POST['client_key']  ?? '';
        $env        = $_POST['environment'] ?? 'sandbox';
        if (mtSaveConfig($serverKey, $clientKey, $env)) {
            $message     = 'Konfigurasi berhasil disimpan.';
            $messageType = 'success';
            $config      = mtLoadConfig();
        } else {
            $message     = 'Gagal menyimpan konfigurasi.';
            $messageType = 'error';
        }
    }

    if (isset($_POST['test'])) {
        $serverKey = $_POST['server_key'] ?? $config['server_key'];
        $clientKey = $_POST['client_key'] ?? $config['client_key'];
        $env       = $_POST['environment'] ?? $config['environment'];

        $url = ($env === 'production' ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2') . '/balance';
        $encoded = base64_encode($serverKey . ':');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $encoded,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            $message     = 'Koneksi gagal: ' . $curlErr;
            $messageType = 'error';
        } elseif ($httpCode === 200) {
            $message     = 'Koneksi berhasil! Server Key valid.';
            $messageType = 'success';
        } elseif ($httpCode === 401) {
            $message     = 'Server Key tidak valid (HTTP 401).';
            $messageType = 'error';
        } else {
            $message     = "Respon tidak terduga (HTTP $httpCode).";
            $messageType = 'error';
        }
    }
}

$isConnected = !empty($config['server_key']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Gateway - SIMKOS</title>
  <?php include '../components/theme.php'; ?>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $active = 'payment-gateway'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="ml-64 p-8">

  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8 flex justify-between items-center">
    <div>
      <h1 class="text-3xl font-bold">Payment Gateway</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Konfigurasi Midtrans untuk pembayaran Virtual Account penghuni kos
      </p>
    </div>
    <?php if ($isConnected): ?>
    <div class="flex items-center gap-3 bg-green-100 dark:bg-green-900/20 px-4 py-2 rounded-2xl">
      <div class="w-3 h-3 rounded-full bg-green-500"></div>
      <span class="text-sm font-medium text-green-600">Terhubung</span>
    </div>
    <?php else: ?>
    <div class="flex items-center gap-3 bg-red-100 dark:bg-red-900/20 px-4 py-2 rounded-2xl">
      <div class="w-3 h-3 rounded-full bg-red-500"></div>
      <span class="text-sm font-medium text-red-600">Belum Terhubung</span>
    </div>
    <?php endif; ?>
  </div>

  <!-- NOTIF -->
  <?php if ($message): ?>
  <div class="mb-6 px-5 py-4 rounded-2xl text-sm font-medium
    <?= $messageType === 'success'
      ? 'bg-green-100 text-green-700'
      : 'bg-red-100 text-red-700' ?>">
    <?= htmlspecialchars($message) ?>
  </div>
  <?php endif; ?>

  <!-- GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- LEFT: FORM -->
    <div class="lg:col-span-2">
      <div class="bg-white dark:bg-[#111] p-8 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">

        <h2 class="text-xl font-semibold mb-6">Konfigurasi Midtrans</h2>

        <form method="POST">
          <!-- SERVER KEY -->
          <div class="mb-6">
            <label class="block text-sm mb-2 text-gray-500 dark:text-gray-400">
              Midtrans Server Key
            </label>
            <input type="password" name="server_key"
              value="<?= htmlspecialchars($config['server_key']) ?>"
              class="input" placeholder="Masukkan Server Key dari Midtrans">
          </div>

          <!-- CLIENT KEY -->
          <div class="mb-6">
            <label class="block text-sm mb-2 text-gray-500 dark:text-gray-400">
              Midtrans Client Key
            </label>
            <input type="text" name="client_key"
              value="<?= htmlspecialchars($config['client_key']) ?>"
              class="input" placeholder="Masukkan Client Key dari Midtrans">
          </div>

          <!-- ENVIRONMENT -->
          <div class="mb-8">
            <label class="block text-sm mb-2 text-gray-500 dark:text-gray-400">
              Environment
            </label>
            <select name="environment" class="input">
              <option value="sandbox" <?= $config['environment'] === 'sandbox' ? 'selected' : '' ?>>
                Sandbox (Testing)
              </option>
              <option value="production" <?= $config['environment'] === 'production' ? 'selected' : '' ?>>
                Production (Live)
              </option>
            </select>
          </div>

          <!-- CALLBACK URL INFO -->
          <div class="mb-8 p-4 rounded-2xl bg-gray-50 dark:bg-[#0d0d0d] border border-gray-200 dark:border-[#222]">
            <p class="text-sm text-gray-500 dark:text-gray-400">
              <strong>Callback URL:</strong> Atur di dashboard Midtrans →
              Settings → Notification URL →
              <code class="text-blue-500">http://localhost/simkos-web/payment-gateway/callback.php</code>
            </p>
            <p class="text-xs text-gray-400 mt-2">
              Untuk testing local, gunakan <strong>ngrok</strong>: jalankan
              <code class="text-blue-500">ngrok http 80</code>
              lalu copy URL ngrok + /simkos-web/payment-gateway/callback.php
            </p>
          </div>

          <!-- BUTTONS -->
          <div class="flex justify-end gap-3">
            <button type="submit" name="test" value="1"
              class="px-5 py-3 rounded-2xl bg-gray-200 dark:bg-[#1a1a1a] hover:opacity-80 transition">
              Test Koneksi
            </button>
            <button type="submit" name="save" value="1"
              class="px-5 py-3 rounded-2xl bg-blue-600 text-white hover:bg-blue-700 transition">
              Simpan Konfigurasi
            </button>
          </div>
        </form>

      </div>
    </div>

    <!-- RIGHT: INFO -->
    <div class="space-y-6">

      <!-- STATUS -->
      <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
        <p class="text-sm text-gray-500 dark:text-gray-400">Status Koneksi</p>
        <div class="flex items-center gap-3 mt-4">
          <div class="w-4 h-4 rounded-full <?= $isConnected ? 'bg-green-500' : 'bg-red-500' ?>"></div>
          <h2 class="text-xl font-bold <?= $isConnected ? 'text-green-600' : 'text-red-600' ?>">
            <?= $isConnected ? 'Terhubung' : 'Belum Terhubung' ?>
          </h2>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
          <?= $isConnected
            ? 'Midtrans siap digunakan. Anda bisa mulai generate VA dari halaman Pembayaran.'
            : 'Masukkan Server Key Midtrans untuk menghubungkan.' ?>
        </p>
      </div>

      <!-- ENVIRONMENT -->
      <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
        <p class="text-sm text-gray-500 dark:text-gray-400">Environment</p>
        <h2 class="text-2xl font-bold mt-3 <?= $config['environment'] === 'production' ? 'text-green-500' : 'text-yellow-500' ?>">
          <?= $config['environment'] === 'production' ? 'Production' : 'Sandbox' ?>
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
          <?= $config['environment'] === 'production'
            ? 'Mode live — transaksi riil'
            : 'Mode testing — gunakan kartu uji Midtrans' ?>
        </p>
      </div>

      <!-- CARA DAPATKAN KEY -->
      <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
        <h2 class="text-lg font-semibold mb-4">Cara Mendapatkan API Key</h2>
        <ol class="space-y-3 text-sm text-gray-500 dark:text-gray-400 list-decimal list-inside">
          <li>Daftar akun di <a href="https://midtrans.com" target="_blank" class="text-blue-500 underline">midtrans.com</a></li>
          <li>Login ke dashboard Midtrans</li>
          <li>Menu <strong>Settings</strong> → <strong>Access Keys</strong></li>
          <li>Salin <strong>Server Key</strong> dan <strong>Client Key</strong></li>
          <li>Pilih Environment: Sandbox (testing) atau Production</li>
          <li>Klik "Test Koneksi" untuk verifikasi</li>
        </ol>
      </div>

    </div>

  </div>

</div>

<style>
.input {
  width: 100%; height: 52px; padding: 0 16px;
  border-radius: 16px; background: #f3f4f6;
  border: 1px solid #e5e7eb; outline: none;
}
.dark .input {
  background: #0d0d0d; border: 1px solid #222; color: white;
}
</style>

</body>
</html>
