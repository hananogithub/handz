<?php
// メール送信設定
$to = "info@handz-corporation.jp";
$subject = "Handz Corporation お問い合わせ";

// フォームデータの取得
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$form_subject = isset($_POST['subject']) ? $_POST['subject'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// バリデーション
if (empty($name) || empty($email) || empty($form_subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'すべてのフィールドを入力してください。']);
    exit;
}

// メールアドレスの形式チェック
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '有効なメールアドレスを入力してください。']);
    exit;
}

// メール本文の作成
$mail_body = "お問い合わせ内容\n\n";
$mail_body .= "お名前: " . $name . "\n";
$mail_body .= "メールアドレス: " . $email . "\n";
$mail_body .= "件名: " . $form_subject . "\n\n";
$mail_body .= "メッセージ:\n" . $message . "\n\n";
$mail_body .= "---\n";
$mail_body .= "送信日時: " . date('Y年m月d日 H:i:s') . "\n";

// メールヘッダーの設定
$headers = "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

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
