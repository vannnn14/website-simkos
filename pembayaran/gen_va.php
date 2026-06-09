<?php
require_once __DIR__ . '/../config/midtrans.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$config = mtLoadConfig();
if (empty($config['server_key'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Midtrans belum dikonfigurasi']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$detailIds = $input['detail_ids'] ?? [];
$bank      = $input['bank'] ?? 'bca';

$validBanks = ['bca', 'bni', 'mandiri', 'bri', 'permata'];
if (!in_array($bank, $validBanks)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bank tidak valid. Pilihan: ' . implode(', ', $validBanks)]);
    exit;
}

if (empty($detailIds) || !is_array($detailIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tidak ada tagihan dipilih']);
    exit;
}

include __DIR__ . '/../config/koneksi.php';

$results = [];

foreach ($detailIds as $detailId) {
    $detailId = (int) $detailId;
    if ($detailId <= 0) continue;

    $q = mysqli_query($conn, "
        SELECT dt.*, p.nama_lengkap, p.no_hp
        FROM detail_tagihan dt
        JOIN penghuni p ON dt.penghuni_id = p.no
        WHERE dt.id = $detailId
    ");
    $row = mysqli_fetch_assoc($q);
    if (!$row) {
        $results[] = [
            'detail_id' => $detailId,
            'status'    => 'failed',
            'error'     => 'Data tidak ditemukan',
        ];
        continue;
    }

    if ($row['status_bayar'] === 'Lunas') {
        $results[] = [
            'detail_id' => $detailId,
            'nama'      => $row['nama_lengkap'],
            'status'    => 'failed',
            'error'     => 'Tagihan sudah lunas',
        ];
        continue;
    }

    if (!empty($row['midtrans_va_number'])) {
        $results[] = [
            'detail_id' => $detailId,
            'nama'      => $row['nama_lengkap'],
            'status'    => 'skipped',
            'va_number' => $row['midtrans_va_number'],
            'bank'       => $row['midtrans_va_bank'],
            'message'   => 'VA sudah ada sebelumnya',
        ];
        continue;
    }

    $phone = preg_replace('/[^0-9]/', '', $row['no_hp']);
    if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
    if (strlen($phone) < 10) $phone = '6281234567890';

    $orderId = 'SIMKOS-' . $detailId . '-' . time();

    $body = [
        'payment_type' => 'bank_transfer',
        'transaction_details' => [
            'order_id'    => $orderId,
            'gross_amount' => (int) $row['nominal_tagihan'],
        ],
        'bank_transfer' => [
            'bank' => $bank,
        ],
        'customer_details' => [
            'first_name' => $row['nama_lengkap'],
            'phone'      => $phone,
        ],
    ];

    $midResult = mtCallApi('/charge', $body);

    if (!isset($midResult['_success']) || !$midResult['_success']) {
        $errMsg = $midResult['error'] ?? ($midResult['status_message'] ?? 'Gagal generate VA');
        $results[] = [
            'detail_id' => $detailId,
            'nama'      => $row['nama_lengkap'],
            'status'    => 'failed',
            'error'     => $errMsg,
        ];
        continue;
    }

    $vaNumber = null;
    $vaBank   = null;

    if (isset($midResult['va_numbers'][0])) {
        $vaNumber = $midResult['va_numbers'][0]['va_number'];
        $vaBank   = $midResult['va_numbers'][0]['bank'];
    } elseif (isset($midResult['permata_va_number'])) {
        $vaNumber = $midResult['permata_va_number'];
        $vaBank   = 'permata';
    }

    $transactionId = $midResult['transaction_id'] ?? null;
    $expiry        = null;
    if (isset($midResult['expiry_time'])) {
        $expiry = date('Y-m-d H:i:s', strtotime($midResult['expiry_time']));
    }

    $upd = mysqli_query($conn, "
        UPDATE detail_tagihan SET
            midtrans_order_id       = '$orderId',
            midtrans_va_number      = " . ($vaNumber ? "'$vaNumber'" : "NULL") . ",
            midtrans_va_bank        = " . ($vaBank ? "'$vaBank'" : "NULL") . ",
            midtrans_transaction_id = " . ($transactionId ? "'$transactionId'" : "NULL") . ",
            midtrans_expiry         = " . ($expiry ? "'$expiry'" : "NULL") . "
        WHERE id = $detailId
    ");

    if (!$upd) {
        $results[] = [
            'detail_id' => $detailId,
            'nama'      => $row['nama_lengkap'],
            'status'    => 'failed',
            'error'     => 'Gagal update database',
        ];
        continue;
    }

    $results[] = [
        'detail_id' => $detailId,
        'nama'      => $row['nama_lengkap'],
        'status'    => 'success',
        'va_number' => $vaNumber,
        'bank'      => $vaBank,
        'order_id'  => $orderId,
        'nominal'   => $row['nominal_tagihan'],
        'expiry'    => $expiry,
    ];
}

$success = count(array_filter($results, fn($r) => $r['status'] === 'success'));
$failed  = count(array_filter($results, fn($r) => $r['status'] === 'failed'));
$skipped = count(array_filter($results, fn($r) => $r['status'] === 'skipped'));

echo json_encode([
    'success' => true,
    'generated' => $success,
    'failed'  => $failed,
    'skipped' => $skipped,
    'results' => $results,
]);
