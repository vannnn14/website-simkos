<?php
/**
 * SIMPAN TAGIHAN - API Endpoint
 * 
 * File ini menangani proses pembuatan tagihan bulanan dengan sistem bobot durasi tinggal
 * - Penghuni "Aktif" (full 1 bulan) = bobot 1.0
 * - Penghuni "Tidak Aktif" (setengah bulan) = bobot 0.5
 * 
 * Request: POST dengan JSON
 * Response: JSON dengan status dan data tagihan
 */

include '../config/koneksi.php';

header('Content-Type: application/json');

// ───────────────────────────────────────────────────────────────────────────────
// 1. VALIDASI METHOD REQUEST
// ───────────────────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak valid. Gunakan POST.'
    ]);
    exit;
}

// ───────────────────────────────────────────────────────────────────────────────
// 2. PARSE & VALIDASI INPUT DATA
// ───────────────────────────────────────────────────────────────────────────────

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Format JSON tidak valid'
    ]);
    exit;
}

// Parse dan validasi input biaya
$biaya_listrik  = floatval($data['listrik'] ?? 0);
$biaya_air      = floatval($data['air'] ?? 0);
$biaya_wifi     = floatval($data['wifi'] ?? 0);
$biaya_sampah   = floatval($data['sampah'] ?? 0);

// Validasi nilai tidak negatif
if ($biaya_listrik < 0 || $biaya_air < 0 || $biaya_wifi < 0 || $biaya_sampah < 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Nilai biaya tidak boleh negatif'
    ]);
    exit;
}

$total_tagihan = $biaya_listrik + $biaya_air + $biaya_wifi + $biaya_sampah;

if ($total_tagihan <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Total tagihan harus lebih dari 0'
    ]);
    exit;
}

// ───────────────────────────────────────────────────────────────────────────────
// 3. HITUNG BOBOT PENGHUNI (SISTEM DURASI TINGGAL)
// ───────────────────────────────────────────────────────────────────────────────

// Ambil jumlah penghuni aktif (full bulan)
$qAktif = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM penghuni 
    WHERE status_kamar = 'Aktif'
");

if (!$qAktif) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error query penghuni aktif: ' . mysqli_error($conn)
    ]);
    exit;
}

$jumlahAktif = mysqli_fetch_assoc($qAktif)['total'];

// Ambil jumlah penghuni tidak aktif (setengah bulan)
$qTidakAktif = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM penghuni 
    WHERE status_kamar = 'Tidak Aktif'
");

if (!$qTidakAktif) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error query penghuni tidak aktif: ' . mysqli_error($conn)
    ]);
    exit;
}

$jumlahTidakAktif = mysqli_fetch_assoc($qTidakAktif)['total'];

$totalPenghuni = $jumlahAktif + $jumlahTidakAktif;

// SISTEM BOBOT:
// Total Bobot = (Jumlah Aktif × 1.0) + (Jumlah Tidak Aktif × 0.5)
$totalBobot = ($jumlahAktif * 1.0) + ($jumlahTidakAktif * 0.5);

if ($totalBobot <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Tidak ada penghuni aktif. Tidak dapat membuat tagihan.'
    ]);
    exit;
}

// Hitung tarif per bobot
$tarifPerBobot = $total_tagihan / $totalBobot;

// ───────────────────────────────────────────────────────────────────────────────
// 4. PERSIAPAN DATA TAGIHAN
// ───────────────────────────────────────────────────────────────────────────────

$bulan_int   = intval(date('n'));        // 1-12
$bulan_text  = date('F');                // e.g. "June"
$tahun       = intval(date('Y'));
$tenggat     = date('Y-m-t');            // akhir bulan ini

// ───────────────────────────────────────────────────────────────────────────────
// 5. MULAI TRANSACTION (untuk konsistensi data)
// ───────────────────────────────────────────────────────────────────────────────

mysqli_begin_transaction($conn);

try {
    // ─────────────────────────────────────────────────────────────────────────
    // 5A. INSERT DATA TAGIHAN UTAMA KE TABLE 'tagihan_utilitas'
    // ─────────────────────────────────────────────────────────────────────────
    
    $stmtTagihan = mysqli_prepare($conn, "
        INSERT INTO tagihan_utilitas 
            (bulan, tahun, biaya_listrik, biaya_air, biaya_wifi, biaya_sampah,
             total_penghuni, total_tagihan, total_bobot, tarif_per_bobot, tenggat_pembayaran)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmtTagihan) {
        throw new Exception('Prepare statement tagihan gagal: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmtTagihan,
        'siddddiddds',
        $bulan_text,
        $tahun,
        $biaya_listrik,
        $biaya_air,
        $biaya_wifi,
        $biaya_sampah,
        $totalPenghuni,
        $total_tagihan,
        $totalBobot,
        $tarifPerBobot,
        $tenggat
    );

    if (!mysqli_stmt_execute($stmtTagihan)) {
        throw new Exception('Execute insert tagihan gagal: ' . mysqli_error($conn));
    }

    $tagihan_id = mysqli_insert_id($conn);

    // ─────────────────────────────────────────────────────────────────────────
    // 5B. INSERT DETAIL TAGIHAN PER PENGHUNI
    // ─────────────────────────────────────────────────────────────────────────

    $qPenghuni = mysqli_query($conn, "
        SELECT no, nama_lengkap, status_kamar 
        FROM penghuni 
        ORDER BY nama_lengkap ASC
    ");

    if (!$qPenghuni) {
        throw new Exception('Query penghuni gagal: ' . mysqli_error($conn));
    }

    $stmtDetail = mysqli_prepare($conn, "
        INSERT INTO detail_tagihan 
            (tagihan_id, penghuni_id, bobot, nominal_tagihan, status_bayar,
             tagihan_listrik, tagihan_wifi, tagihan_air, tagihan_sampah)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmtDetail) {
        throw new Exception('Prepare statement detail gagal: ' . mysqli_error($conn));
    }

    $detail_count = 0;
    $total_nominal_check = 0;

    while ($p = mysqli_fetch_assoc($qPenghuni)) {
        $penghuni_id = $p['no'];

        // SISTEM BOBOT:
        // Penghuni Aktif = bobot 1.0 (full 1 bulan)
        // Penghuni Tidak Aktif = bobot 0.5 (setengah bulan)
        $bobot = ($p['status_kamar'] === 'Aktif') ? 1.0 : 0.5;

        // PERHITUNGAN BIAYA PER PENGHUNI:
        // Listrik dibagi proporsional berdasarkan bobot (untuk semua penghuni)
        $tListrik = ($biaya_listrik / $totalBobot) * $bobot;

        // Air, Wifi, Sampah hanya untuk penghuni Aktif (full 1 bulan)
        if ($p['status_kamar'] === 'Aktif') {
            $tAir    = $jumlahAktif > 0 ? $biaya_air    / $jumlahAktif : 0;
            $tWifi   = $jumlahAktif > 0 ? $biaya_wifi   / $jumlahAktif : 0;
            $tSampah = $jumlahAktif > 0 ? $biaya_sampah / $jumlahAktif : 0;
        } else {
            $tAir    = 0;
            $tWifi   = 0;
            $tSampah = 0;
        }

        // Total nominal tagihan penghuni
        $nominal_penghuni = $tListrik + $tAir + $tWifi + $tSampah;
        $total_nominal_check += $nominal_penghuni;

        $status_bayar = 'Belum Bayar';

        mysqli_stmt_bind_param(
            $stmtDetail,
            'iiddsdddd',
            $tagihan_id,
            $penghuni_id,
            $bobot,
            $nominal_penghuni,
            $status_bayar,
            $tListrik,
            $tWifi,
            $tAir,
            $tSampah
        );

        if (!mysqli_stmt_execute($stmtDetail)) {
            throw new Exception(
                'Insert detail tagihan untuk penghuni ' . $p['nama_lengkap'] . ' gagal: ' . 
                mysqli_error($conn)
            );
        }

        $detail_count++;
    }

    // Validasi: total nominal detail harus mendekati total tagihan (dengan tolerance untuk pembulatan)
    $difference = abs($total_nominal_check - $total_tagihan);
    if ($difference > 1) { // tolerance 1 rupiah untuk pembulatan
        throw new Exception(
            'Validasi jumlah tagihan gagal. Selisih: Rp' . number_format($difference, 2)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5C. COMMIT TRANSACTION
    // ─────────────────────────────────────────────────────────────────────────

    mysqli_commit($conn);

    // ─────────────────────────────────────────────────────────────────────────
    // 6. RETURN SUCCESS RESPONSE
    // ─────────────────────────────────────────────────────────────────────────

    http_response_code(201);
    echo json_encode([
        'success'         => true,
        'message'         => 'Tagihan berhasil disimpan',
        'tagihan_id'      => $tagihan_id,
        'bulan'           => $bulan_text,
        'tahun'           => $tahun,
        'total_tagihan'   => floatval($total_tagihan),
        'total_bobot'     => floatval($totalBobot),
        'tarif_per_bobot' => floatval($tarifPerBobot),
        'penghuni_aktif'  => intval($jumlahAktif),
        'penghuni_tidak_aktif' => intval($jumlahTidakAktif),
        'detail_count'    => intval($detail_count),
        'biaya' => [
            'listrik' => floatval($biaya_listrik),
            'air'     => floatval($biaya_air),
            'wifi'    => floatval($biaya_wifi),
            'sampah'  => floatval($biaya_sampah)
        ]
    ]);

} catch (Exception $e) {
    // ─────────────────────────────────────────────────────────────────────────
    // ROLLBACK Jika Ada Error
    // ─────────────────────────────────────────────────────────────────────────

    mysqli_rollback($conn);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
