<?php
/**
 * GET DETAIL PEMBAGIAN TAGIHAN - API Endpoint
 * 
 * Endpoint untuk mendapatkan detail pembagian tagihan per penghuni
 * dengan breakdown yang jelas dan mudah dipahami.
 * 
 * Request: GET /tagihan/get_pembagian.php?tagihan_id=X
 * Response: JSON dengan detail pembagian per penghuni
 */

include '../config/koneksi.php';

header('Content-Type: application/json');

// ───────────────────────────────────────────────────────────────────────────────
// 1. VALIDASI INPUT
// ───────────────────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak valid. Gunakan GET.'
    ]);
    exit;
}

if (!isset($_GET['tagihan_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Parameter tagihan_id diperlukan'
    ]);
    exit;
}

$tagihan_id = intval($_GET['tagihan_id']);

// ───────────────────────────────────────────────────────────────────────────────
// 2. AMBIL DATA TAGIHAN UTAMA
// ───────────────────────────────────────────────────────────────────────────────

$qTagihan = mysqli_query($conn, "
    SELECT * FROM tagihan_utilitas WHERE id = $tagihan_id
");

if (!$qTagihan || mysqli_num_rows($qTagihan) === 0) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Tagihan tidak ditemukan'
    ]);
    exit;
}

$tagihan = mysqli_fetch_assoc($qTagihan);

// ───────────────────────────────────────────────────────────────────────────────
// 3. AMBIL DETAIL PEMBAGIAN PER PENGHUNI
// ───────────────────────────────────────────────────────────────────────────────

$qDetail = mysqli_query($conn, "
    SELECT 
        dt.id,
        dt.penghuni_id,
        dt.bobot,
        dt.nominal_tagihan,
        dt.status_bayar,
        dt.tagihan_listrik,
        dt.tagihan_air,
        dt.tagihan_wifi,
        dt.tagihan_sampah,
        p.nama_lengkap,
        p.no_kamar,
        p.status_kamar
    FROM detail_tagihan dt
    JOIN penghuni p ON dt.penghuni_id = p.no
    WHERE dt.tagihan_id = $tagihan_id
    ORDER BY p.status_kamar DESC, p.nama_lengkap ASC
");

if (!$qDetail) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error query detail: ' . mysqli_error($conn)
    ]);
    exit;
}

$pembagian_detail = [];
$total_nominal_actual = 0;

while ($row = mysqli_fetch_assoc($qDetail)) {
    $pembagian_detail[] = $row;
    $total_nominal_actual += floatval($row['nominal_tagihan']);
}

// ───────────────────────────────────────────────────────────────────────────────
// 4. HITUNG STATISTIK PEMBAGIAN
// ───────────────────────────────────────────────────────────────────────────────

$jumlah_penghuni = count($pembagian_detail);
$penghuni_aktif = 0;
$penghuni_tidak_aktif = 0;
$total_bobot = 0;

foreach ($pembagian_detail as $detail) {
    $bobot = floatval($detail['bobot']);
    $total_bobot += $bobot;
    
    if ($detail['status_kamar'] === 'Aktif') {
        $penghuni_aktif++;
    } else {
        $penghuni_tidak_aktif++;
    }
}

// ───────────────────────────────────────────────────────────────────────────────
// 5. HITUNG RINGKASAN BIAYA
// ───────────────────────────────────────────────────────────────────────────────

$total_listrik = floatval($tagihan['biaya_listrik']);
$total_air = floatval($tagihan['biaya_air']);
$total_wifi = floatval($tagihan['biaya_wifi']);
$total_sampah = floatval($tagihan['biaya_sampah']);

$ringkasan_biaya_aktif = 0;
$ringkasan_biaya_tidak_aktif = 0;

foreach ($pembagian_detail as $detail) {
    if ($detail['status_kamar'] === 'Aktif') {
        $ringkasan_biaya_aktif += floatval($detail['nominal_tagihan']);
    } else {
        $ringkasan_biaya_tidak_aktif += floatval($detail['nominal_tagihan']);
    }
}

// ───────────────────────────────────────────────────────────────────────────────
// 6. BUILD RESPONSE
// ───────────────────────────────────────────────────────────────────────────────

$response = [
    'success' => true,
    'message' => 'Detail pembagian tagihan',
    'tagihan_id' => intval($tagihan['id']),
    'bulan' => $tagihan['bulan'],
    'tahun' => intval($tagihan['tahun']),
    'ringkasan' => [
        'total_tagihan' => floatval($tagihan['total_tagihan']),
        'total_bobot' => floatval($tagihan['total_bobot']),
        'tarif_per_bobot' => floatval($tagihan['tarif_per_bobot']),
        'tenggat_pembayaran' => $tagihan['tenggat_pembayaran']
    ],
    'penghuni_summary' => [
        'total_penghuni' => intval($jumlah_penghuni),
        'penghuni_aktif' => intval($penghuni_aktif),
        'penghuni_tidak_aktif' => intval($penghuni_tidak_aktif),
        'total_bobot_aktif' => floatval($penghuni_aktif * 1.0),
        'total_bobot_tidak_aktif' => floatval($penghuni_tidak_aktif * 0.5)
    ],
    'ringkasan_biaya' => [
        'listrik' => floatval($total_listrik),
        'air' => floatval($total_air),
        'wifi' => floatval($total_wifi),
        'sampah' => floatval($total_sampah),
        'total' => floatval($tagihan['total_tagihan']),
        'pembagian' => [
            'penghuni_aktif' => floatval($ringkasan_biaya_aktif),
            'penghuni_tidak_aktif' => floatval($ringkasan_biaya_tidak_aktif)
        ]
    ],
    'perhitungan_detail' => [
        'sistem' => 'Bobot Durasi Tinggal',
        'penjelasan' => [
            'Penghuni Aktif (1 bulan) = Bobot 1.0',
            'Penghuni Tidak Aktif (setengah bulan) = Bobot 0.5',
            'Tarif per Bobot = Total Tagihan ÷ Total Bobot',
            'Tagihan Penghuni = Tarif per Bobot × Bobot Penghuni'
        ],
        'rumus' => 'Tagihan Penghuni = ' . floatval($tagihan['tarif_per_bobot']) . ' × Bobot'
    ],
    'pembagian_per_penghuni' => []
];

// ───────────────────────────────────────────────────────────────────────────────
// 7. BUILD DETAIL PER PENGHUNI
// ───────────────────────────────────────────────────────────────────────────────

foreach ($pembagian_detail as $detail) {
    $nominal = floatval($detail['nominal_tagihan']);
    $bobot = floatval($detail['bobot']);
    
    $response['pembagian_per_penghuni'][] = [
        'penghuni_id' => intval($detail['penghuni_id']),
        'nama' => $detail['nama_lengkap'],
        'kamar' => $detail['no_kamar'],
        'status_kamar' => $detail['status_kamar'],
        'bobot' => $bobot,
        'status' => $detail['status_kamar'] === 'Aktif' ? 'FULL (1 Bulan)' : 'SETENGAH (0.5 Bulan)',
        'tagihan' => [
            'total' => $nominal,
            'listrik' => floatval($detail['tagihan_listrik']),
            'air' => floatval($detail['tagihan_air']),
            'wifi' => floatval($detail['tagihan_wifi']),
            'sampah' => floatval($detail['tagihan_sampah'])
        ],
        'status_pembayaran' => $detail['status_bayar'],
        'keterangan' => [
            'rumus' => floatval($tagihan['tarif_per_bobot']) . ' × ' . $bobot . ' = ' . number_format($nominal, 2),
            'penjelasan' => $detail['status_kamar'] === 'Aktif' 
                ? 'Bayar FULL untuk 1 bulan penuh tinggal' 
                : 'Bayar SETENGAH karena hanya tinggal setengah bulan'
        ]
    ];
}

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
