<?php
include '../config/auth.php';
require_once __DIR__ . '/../config/whatsapp.php';

$config  = waLoadConfig();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $apiUrl   = $_POST['api_url']   ?? 'https://api.fonnte.com/send';
        $apiToken = $_POST['api_token'] ?? '';
        if (waSaveConfig($apiUrl, $apiToken)) {
            $message     = 'Konfigurasi berhasil disimpan.';
            $messageType = 'success';
            $config      = waLoadConfig();
        } else {
            $message     = 'Gagal menyimpan konfigurasi.';
            $messageType = 'error';
        }
    }

    if (isset($_POST['test'])) {
        $apiToken = $_POST['api_token'] ?? $config['api_token'];
        $apiUrl   = $_POST['api_url']   ?? $config['api_url'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'target'      => 'test',
            'message'     => 'Test koneksi dari SIMKOS',
            'countryCode' => '62',
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiToken,
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
        } elseif ($httpCode >= 200 && $httpCode < 300) {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status']) {
                $message     = 'Koneksi berhasil! API Token valid.';
                $messageType = 'success';
            } else {
                $reason = $result['reason'] ?? 'Unknown error';
                $message     = 'Token tidak valid: ' . $reason;
                $messageType = 'error';
            }
        } else {
            $message     = "HTTP $httpCode — " . substr($response, 0, 200);
            $messageType = 'error';
        }
    }
}

$isConnected = !empty($config['api_token']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsApp API - SIMKOS</title>
  <?php include '../components/theme.php'; ?>
</head>
<body class="bg-gray-100 dark:bg-[#0f0f0f] text-gray-800 dark:text-white">

<?php $pageTitle = 'WhatsApp API'; ?>
<?php $active = 'whatsapp-api'; ?>
<?php include '../components/sidebar.php'; ?>

<div class="lg:ml-64 p-4 lg:p-8 pt-4">

  <?php include '../components/topbar.php'; ?>

  <!-- HEADER -->
  <div class="mb-8 flex justify-between items-center">
    <div>
      <h1 class="text-3xl font-bold">WhatsApp API</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2">
        Konfigurasi API Gateway untuk pengiriman reminder otomatis via Fonnte
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

        <h2 class="text-xl font-semibold mb-6">Konfigurasi Fonnte</h2>

        <form method="POST">
          <!-- API URL -->
          <div class="mb-6">
            <label class="block text-sm mb-2 text-gray-500 dark:text-gray-400">
              API Endpoint URL
            </label>
            <input type="text" name="api_url" value="<?= htmlspecialchars($config['api_url']) ?>"
              class="input" placeholder="https://api.fonnte.com/send">
          </div>

          <!-- API TOKEN -->
          <div class="mb-6">
            <label class="block text-sm mb-2 text-gray-500 dark:text-gray-400">
              API Token
            </label>
            <input type="password" name="api_token" value="<?= htmlspecialchars($config['api_token']) ?>"
              class="input" placeholder="Masukkan token dari Fonnte">
            <p class="text-xs text-gray-400 mt-2">
              Dapatkan token dari <a href="https://fonnte.com" target="_blank" class="text-blue-500 underline">fonnte.com</a>
              setelah daftar dan menghubungkan nomor WhatsApp.
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
            ? 'API siap digunakan untuk mengirim reminder WhatsApp.'
            : 'Masukkan API Token Fonnte untuk menghubungkan.' ?>
        </p>
      </div>

      <!-- CARA DAPATKAN TOKEN -->
      <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
        <h2 class="text-lg font-semibold mb-4">Cara Mendapatkan Token</h2>
        <ol class="space-y-3 text-sm text-gray-500 dark:text-gray-400 list-decimal list-inside">
          <li>Daftar akun di <a href="https://fonnte.com" target="_blank" class="text-blue-500 underline">fonnte.com</a></li>
          <li>Login dan hubungkan nomor WhatsApp kamu (scan QR)</li>
          <li>Salin API Token dari dashboard Fonnte</li>
          <li>Tempel token di kolom di samping</li>
          <li>Klik "Test Koneksi" untuk verifikasi</li>
          <li>Klik "Simpan Konfigurasi"</li>
        </ol>
      </div>

      <!-- INFORMASI -->
      <div class="bg-white dark:bg-[#111] p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-[#1f1f1f]">
        <h2 class="text-lg font-semibold mb-4">Informasi</h2>
        <ul class="space-y-3 text-sm text-gray-500 dark:text-gray-400">
          <li>• Pastikan nomor HP penghuni sudah diisi dengan benar di data penghuni</li>
          <li>• Format nomor: 0812xxx → otomatis 62812xxx</li>
          <li>• HP yang terhubung dengan Fonnte harus tetap online</li>
          <li>• Biaya mengikuti tarif Fonnte (gratis untuk 100 pesan pertama)</li>
        </ul>
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
