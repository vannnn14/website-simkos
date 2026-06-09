<?php
require_once __DIR__ . '/../config/midtrans.php';

$rawBody = file_get_contents('php://input');
$notif   = json_decode($rawBody, true);

if (!$notif) {
    http_response_code(400);
    exit('Invalid payload');
}

$orderId          = $notif['order_id'] ?? '';
$transactionStatus = $notif['transaction_status'] ?? '';
$fraudStatus      = $notif['fraud_status'] ?? '';
$transactionId    = $notif['transaction_id'] ?? '';
$statusCode       = $notif['status_code'] ?? '';
$grossAmount      = $notif['gross_amount'] ?? '';

if (empty($orderId) || empty($transactionStatus)) {
    http_response_code(400);
    exit('Missing required fields');
}

include __DIR__ . '/../config/koneksi.php';

$q = mysqli_query($conn, "
    SELECT dt.id, dt.status_bayar, dt.penghuni_id
    FROM detail_tagihan dt
    WHERE dt.midtrans_order_id = '$orderId'
");
$row = mysqli_fetch_assoc($q);

if (!$row) {
    http_response_code(404);
    exit('Order not found');
}

$detailId    = (int) $row['id'];
$penghuniId  = (int) $row['penghuni_id'];

$statusBayar = 'Belum Bayar';

if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
    if ($fraudStatus === 'accept' || $fraudStatus === '') {
        $statusBayar = 'Lunas';
    }
} elseif ($transactionStatus === 'pending') {
    $statusBayar = 'Belum Bayar';
} elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
    $statusBayar = 'Belum Bayar';
}

if ($statusBayar === 'Lunas') {
    mysqli_begin_transaction($conn);

    try {
        $upd1 = mysqli_query($conn, "
            UPDATE detail_tagihan
            SET status_bayar = 'Lunas',
                midtrans_transaction_id = " . ($transactionId ? "'$transactionId'" : "midtrans_transaction_id") . "
            WHERE id = $detailId
        ");
        if (!$upd1) throw new Exception(mysqli_error($conn));

        $qCek = mysqli_query($conn, "
            SELECT status_bayar FROM detail_tagihan WHERE penghuni_id = $penghuniId
        ");
        $semuaLunas = true;
        while ($r = mysqli_fetch_assoc($qCek)) {
            if ($r['status_bayar'] !== 'Lunas') $semuaLunas = false;
        }

        $statusPenghuni = $semuaLunas ? 'Lunas' : 'Menunggak';

        $upd2 = mysqli_query($conn, "
            UPDATE penghuni SET status_pembayaran = '$statusPenghuni' WHERE no = $penghuniId
        ");
        if (!$upd2) throw new Exception(mysqli_error($conn));

        mysqli_commit($conn);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        http_response_code(500);
        exit('Update failed: ' . $e->getMessage());
    }
}

echo json_encode([
    'success'  => true,
    'order_id' => $orderId,
    'status'   => $transactionStatus,
    'payment_status' => $statusBayar,
]);
