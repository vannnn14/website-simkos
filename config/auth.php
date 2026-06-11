<?php
session_start();

if (!isset($_SESSION['user'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Sesi habis, silakan login ulang']);
        exit;
    }
    header('Location: /simkos-web/auth/login.php');
    exit;
}
