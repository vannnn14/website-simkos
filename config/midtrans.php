<?php

$mtConfigFile = __DIR__ . '/midtrans.json';

function mtLoadConfig() {
    global $mtConfigFile;
    $default = [
        'server_key'  => '',
        'client_key'  => '',
        'environment' => 'sandbox',
    ];
    if (!file_exists($mtConfigFile)) {
        file_put_contents($mtConfigFile, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }
    $data = json_decode(file_get_contents($mtConfigFile), true);
    return is_array($data) ? array_merge($default, $data) : $default;
}

function mtSaveConfig($serverKey, $clientKey, $environment) {
    global $mtConfigFile;
    $data = json_encode([
        'server_key'  => trim($serverKey),
        'client_key'  => trim($clientKey),
        'environment' => $environment === 'production' ? 'production' : 'sandbox',
    ], JSON_PRETTY_PRINT);
    return file_put_contents($mtConfigFile, $data) !== false;
}

function mtApiUrl() {
    $config = mtLoadConfig();
    return $config['environment'] === 'production'
        ? 'https://api.midtrans.com/v2'
        : 'https://api.sandbox.midtrans.com/v2';
}

function mtAuthHeader() {
    $config = mtLoadConfig();
    $encoded = base64_encode($config['server_key'] . ':');
    return 'Basic ' . $encoded;
}

function mtCallApi($endpoint, $body) {
    $url = mtApiUrl() . '/' . ltrim($endpoint, '/');
    $jsonBody = json_encode($body);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: ' . mtAuthHeader(),
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response   = curl_exec($ch);
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError  = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return [
            'success' => false,
            'error'   => 'Curl: ' . $curlError,
        ];
    }

    $parsed = json_decode($response, true);
    if (!$parsed) {
        return [
            'success' => false,
            'error'   => 'Invalid JSON response (HTTP ' . $httpCode . ')',
            'raw'     => $response,
        ];
    }

    $parsed['_http_code'] = $httpCode;
    $parsed['_success']   = ($httpCode >= 200 && $httpCode < 300);

    return $parsed;
}

function mtGenerateVa($detailId, $bank) {
    include __DIR__ . '/koneksi.php';

    $q = mysqli_query($conn, "
        SELECT dt.*, p.nama_lengkap, p.no_hp, tu.bulan, tu.tahun
        FROM detail_tagihan dt
        JOIN penghuni p ON dt.penghuni_id = p.no
        JOIN tagihan_utilitas tu ON dt.tagihan_id = tu.id
        WHERE dt.id = $detailId
    ");
    $row = mysqli_fetch_assoc($q);
    if (!$row) {
        return ['success' => false, 'error' => 'Detail tagihan tidak ditemukan'];
    }

    $phone = preg_replace('/[^0-9]/', '', $row['no_hp']);
    if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);

    $orderId = 'SIMKOS-' . $detailId . '-' . time();

    $body = [
        'payment_type' => 'bank_transfer',
        'transaction_details' => [
            'order_id' => $orderId,
            'gross_amount' => (int) $row['nominal_tagihan'],
        ],
        'bank_transfer' => [
            'bank' => $bank,
        ],
        'customer_details' => [
            'first_name' => $row['nama_lengkap'],
            'phone' => $phone,
        ],
    ];

    $result = mtCallApi('/charge', $body);

    if (!isset($result['_success']) || !$result['_success']) {
        $errMsg = $result['error'] ?? ($result['status_message'] ?? 'Unknown error');
        return ['success' => false, 'error' => $errMsg];
    }

    $vaNumber = null;
    $vaBank   = null;
    if (isset($result['va_numbers'][0])) {
        $vaNumber = $result['va_numbers'][0]['va_number'];
        $vaBank   = $result['va_numbers'][0]['bank'];
    } elseif (isset($result['permata_va_number'])) {
        $vaNumber = $result['permata_va_number'];
        $vaBank   = 'permata';
    }

    $transactionId = $result['transaction_id'] ?? null;
    $expiry        = null;
    if (isset($result['expiry_time'])) {
        $expiry = date('Y-m-d H:i:s', strtotime($result['expiry_time']));
    }

    $upd = mysqli_query($conn, "
        UPDATE detail_tagihan SET
            midtrans_order_id = '$orderId',
            midtrans_va_number = '$vaNumber',
            midtrans_va_bank = '$vaBank',
            midtrans_transaction_id = '$transactionId',
            midtrans_expiry = " . ($expiry ? "'$expiry'" : "NULL") . "
        WHERE id = $detailId
    ");
    if (!$upd) {
        return ['success' => false, 'error' => 'Gagal update database: ' . mysqli_error($conn)];
    }

    return [
        'success'   => true,
        'order_id'  => $orderId,
        'va_number' => $vaNumber,
        'bank'      => $vaBank,
        'transaction_id' => $transactionId,
        'expiry'    => $expiry,
        'nama'      => $row['nama_lengkap'],
        'nominal'   => $row['nominal_tagihan'],
    ];
}
