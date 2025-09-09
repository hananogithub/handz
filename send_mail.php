<?php
// セキュリティヘッダーの設定
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// メール送信設定
$to = "info@handz-corporation.jp";
$subject = "Handz Corporation お問い合わせ";

// フォームデータの取得とサニタイズ
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$form_subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// サーバー側バリデーション（必須）
if (empty($name) || empty($email) || empty($form_subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'すべてのフィールドを入力してください。']);
    exit;
}

// 文字数制限チェック
if (strlen($name) > 100 || strlen($email) > 255 || strlen($form_subject) > 200 || strlen($message) > 2000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '入力内容が長すぎます。']);
    exit;
}

// メールアドレスの形式チェック
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '有効なメールアドレスを入力してください。']);
    exit;
}

// メールヘッダーインジェクション対策
if (strpos($email, "\r") !== false || strpos($email, "\n") !== false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '不正なメールアドレスです。']);
    exit;
}

// 複数宛先メール防止
if (strpos($email, ',') !== false || strpos($email, ';') !== false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '不正なメールアドレスです。']);
    exit;
}

// メール本文の作成（XSS対策済み）
$mail_body = "お問い合わせ内容\n\n";
$mail_body .= "お名前: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "\n";
$mail_body .= "メールアドレス: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n";
$mail_body .= "件名: " . htmlspecialchars($form_subject, ENT_QUOTES, 'UTF-8') . "\n\n";
$mail_body .= "メッセージ:\n" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "\n\n";
$mail_body .= "---\n";
$mail_body .= "送信日時: " . date('Y年m月d日 H:i:s') . "\n";
$mail_body .= "IPアドレス: " . $_SERVER['REMOTE_ADDR'] . "\n";

// メールヘッダーの設定（セキュリティ強化）
$headers = "From: noreply@handz-corporation.jp\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// メール送信
$mail_sent = mail($to, $subject, $mail_body, $headers);

if ($mail_sent) {
    // 送信成功
    echo json_encode(['success' => true, 'message' => 'お問い合わせありがとうございます。後日担当者よりご連絡いたします。']);
} else {
    // 送信失敗
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'メールの送信に失敗しました。しばらく時間をおいて再度お試しください。']);
}
?>
