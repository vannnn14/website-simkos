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
    // 0. Ambil nominal tagihan dan data detail
    $qNominal = mysqli_query($conn,
        "SELECT nominal_tagihan FROM detail_tagihan WHERE id = $detail_id"
    );
    $rNominal = mysqli_fetch_assoc($qNominal);
    $nominal  = $rNominal['nominal_tagihan'] ?? 0;

    // 1. Update status_bayar di detail_tagihan
    $stmt1 = mysqli_prepare($conn,
        "UPDATE detail_tagihan SET status_bayar = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt1, 'si', $status_baru, $detail_id);
    if (!mysqli_stmt_execute($stmt1)) {
        throw new Exception('Gagal update detail_tagihan: ' . mysqli_error($conn));
    }

    // 1b. Catat/void pembayaran
    if ($status_baru === 'Lunas') {
        $stmtPay = mysqli_prepare($conn,
            "INSERT INTO pembayaran (penghuni_id, detail_tagihan_id, jumlah_bayar, metode_pembayaran, status, keterangan)
             VALUES (?, ?, ?, 'Tunai', 'Diterima', 'Pembayaran manual via admin')"
        );
        mysqli_stmt_bind_param($stmtPay, 'iid', $penghuni_id, $detail_id, $nominal);
        if (!mysqli_stmt_execute($stmtPay)) {
            throw new Exception('Gagal insert pembayaran: ' . mysqli_error($conn));
        }
    } else {
        $stmtVoid = mysqli_prepare($conn,
            "UPDATE pembayaran SET status = 'Ditolak', keterangan = 'Dibatalkan admin' WHERE detail_tagihan_id = ? AND status = 'Diterima'"
        );
        mysqli_stmt_bind_param($stmtVoid, 'i', $detail_id);
        mysqli_stmt_execute($stmtVoid);
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
