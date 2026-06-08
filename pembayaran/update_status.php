<?php
include '../config/koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$data        = json_decode(file_get_contents('php://input'), true);
$detail_id   = intval($data['detail_id']   ?? 0);
$penghuni_id = intval($data['penghuni_id'] ?? 0);
$status_baru = $data['status'] ?? '';

$statusValid = ['Lunas', 'Belum Bayar', 'Sebagian'];
if (!$detail_id || !$penghuni_id || !in_array($status_baru, $statusValid)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

mysqli_begin_transaction($conn);

try {
    // 1. Update status_bayar di detail_tagihan
    $stmt1 = mysqli_prepare($conn,
        "UPDATE detail_tagihan SET status_bayar = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt1, 'si', $status_baru, $detail_id);
    if (!mysqli_stmt_execute($stmt1)) {
        throw new Exception('Gagal update detail_tagihan: ' . mysqli_error($conn));
    }

    // 2. Sinkronkan status_pembayaran di penghuni
    //    Jika penghuni sudah Lunas di SEMUA tagihan → Lunas
    //    Jika ada yang Sebagian                    → Menunggak
    //    Jika semua Belum Bayar                    → Belum Lunas
    $qCek = mysqli_query($conn,
        "SELECT status_bayar FROM detail_tagihan WHERE penghuni_id = $penghuni_id"
    );

    $semuaLunas   = true;
    $adaSebagian  = false;

    while ($r = mysqli_fetch_assoc($qCek)) {
        if ($r['status_bayar'] !== 'Lunas')  $semuaLunas  = false;
        if ($r['status_bayar'] === 'Sebagian') $adaSebagian = true;
    }

    if ($semuaLunas) {
        $statusPenghuni = 'Lunas';
    } elseif ($adaSebagian) {
        $statusPenghuni = 'Menunggak';
    } else {
        $statusPenghuni = 'Belum Lunas';
    }

    $stmt2 = mysqli_prepare($conn,
        "UPDATE penghuni SET status_pembayaran = ? WHERE no = ?"
    );
    mysqli_stmt_bind_param($stmt2, 'si', $statusPenghuni, $penghuni_id);
    if (!mysqli_stmt_execute($stmt2)) {
        throw new Exception('Gagal update penghuni: ' . mysqli_error($conn));
    }

    mysqli_commit($conn);

    echo json_encode([
        'success'         => true,
        'status_baru'     => $status_baru,
        'status_penghuni' => $statusPenghuni,
        'message'         => 'Status berhasil diperbarui'
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
