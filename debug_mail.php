<?php
// デバッグ用メール送信テスト
header('Content-Type: application/json; charset=UTF-8');

echo json_encode([
    'success' => true,
    'message' => 'PHP is working correctly',
    'php_version' => phpversion(),
    'mail_function' => function_exists('mail') ? 'Available' : 'Not available',
    'server_info' => [
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Unknown'
    ],
    'post_data' => $_POST,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
