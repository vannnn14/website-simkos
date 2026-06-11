<?php
include '../config/auth.php';
require_once __DIR__ . '/../config/whatsapp.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$config = waLoadConfig();
if (empty($config['api_token'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'API token belum dikonfigurasi']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$ids   = $input['detail_ids'] ?? [];

if (empty($ids) || !is_array($ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tidak ada penghuni dipilih']);
    exit;
}

include __DIR__ . '/../config/koneksi.php';

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types        = str_repeat('i', count($ids));

    $stmt = mysqli_prepare($conn, "
    SELECT
        dt.id              AS detail_id,
        dt.nominal_tagihan,
        dt.status_bayar,
        dt.tagihan_listrik,
        dt.tagihan_air,
        dt.tagihan_wifi,
        dt.tagihan_sampah,
        dt.midtrans_va_number,
        dt.midtrans_va_bank,
        p.nama_lengkap,
        p.no_kamar,
        p.no_hp,
        tu.bulan,
        tu.tahun,
        tu.tenggat_pembayaran
    FROM detail_tagihan dt
    JOIN penghuni p         ON dt.penghuni_id = p.no
    JOIN tagihan_utilitas tu ON dt.tagihan_id  = tu.id
    WHERE dt.id IN ($placeholders)
");

mysqli_stmt_bind_param($stmt, $types, ...$ids);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$results = [];

while ($row = mysqli_fetch_assoc($result)) {
    $phone = waFormatPhone($row['no_hp']);

    if (strlen($phone) < 10) {
        $results[] = [
            'nama'   => $row['nama_lengkap'],
            'status' => 'failed',
            'error'  => 'Nomor HP tidak valid',
        ];
        continue;
    }

    $tenggat = $row['tenggat_pembayaran']
        ? date('d/m/Y', strtotime($row['tenggat_pembayaran']))
        : '-';

    $message = "*🏠 PENGINGAT TAGIHAN KOS*";
    $message .= "\n\nYth. *{$row['nama_lengkap']}* (Kamar {$row['no_kamar']})";
    $message .= "\n\nTagihan kos bulan *{$row['bulan']} {$row['tahun']}*:";
    $message .= "\n• Listrik: Rp " . number_format($row['tagihan_listrik'], 0, ',', '.');
    $message .= "\n• Air: Rp " . number_format($row['tagihan_air'], 0, ',', '.');
    $message .= "\n• WiFi: Rp " . number_format($row['tagihan_wifi'], 0, ',', '.');
    $message .= "\n• Sampah: Rp " . number_format($row['tagihan_sampah'], 0, ',', '.');
    $message .= "\n━━━━━━━━━━━━━━━━━━━";
    $message .= "\n*Total: Rp " . number_format($row['nominal_tagihan'], 0, ',', '.') . "*";
    $message .= "\n\nStatus: *{$row['status_bayar']}*";
    $message .= "\nTenggat: {$tenggat}";

    if (!empty($row['midtrans_va_number'])) {
        $message .= "\n\n💳 *BAYAR VIA VIRTUAL ACCOUNT*";
        $message .= "\n" . strtoupper($row['midtrans_va_bank']) . ": {$row['midtrans_va_number']}";
        $message .= "\na.n. SIMKOS MANAGEMENT";
    } else {
        $message .= "\n\n💳 Bayar tunai ke admin kos.";
    }

    $message .= "\n\nMohon segera melakukan pembayaran.";
    $message .= "\nTerima kasih 🙏";
    $message .= "\n- *Manajemen Kos*";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['api_url']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'target'      => $phone,
        'message'     => $message,
        'countryCode' => '62',
    ]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $config['api_token'],
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response   = curl_exec($ch);
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError  = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        $results[] = [
            'nama'   => $row['nama_lengkap'],
            'status' => 'failed',
            'error'  => 'Curl error: ' . $curlError,
        ];
        continue;
    }

    $apiResult = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300 && isset($apiResult['status']) && $apiResult['status']) {
        $results[] = [
            'nama'   => $row['nama_lengkap'],
            'status' => 'sent',
            'wa'     => $phone,
        ];
    } else {
        $reason = $apiResult['reason'] ?? $apiResult['message'] ?? 'Unknown error';
        $results[] = [
            'nama'   => $row['nama_lengkap'],
            'status' => 'failed',
            'error'  => $reason,
        ];
    }
}

$sent   = count(array_filter($results, fn($r) => $r['status'] === 'sent'));
$failed = count(array_filter($results, fn($r) => $r['status'] === 'failed'));

echo json_encode([
    'success' => true,
    'sent'    => $sent,
    'failed'  => $failed,
    'results' => $results,
]);
