<?php

$waConfigFile = __DIR__ . '/whatsapp.json';

function waLoadConfig() {
    global $waConfigFile;
    $default = ['api_url' => 'https://api.fonnte.com/send', 'api_token' => ''];
    if (!file_exists($waConfigFile)) {
        file_put_contents($waConfigFile, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }
    $data = json_decode(file_get_contents($waConfigFile), true);
    return is_array($data) ? array_merge($default, $data) : $default;
}

function waSaveConfig($apiUrl, $apiToken) {
    global $waConfigFile;
    $data = json_encode([
        'api_url'   => rtrim($apiUrl, '/'),
        'api_token' => trim($apiToken),
    ], JSON_PRETTY_PRINT);
    return file_put_contents($waConfigFile, $data) !== false;
}

function waFormatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 2) === '62') {
        return $phone;
    }
    if (substr($phone, 0, 1) === '0') {
        return '62' . substr($phone, 1);
    }
    return '62' . $phone;
}
