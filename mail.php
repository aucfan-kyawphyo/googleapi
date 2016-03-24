<?php
//$to      = 'memo@aucfan.com';
$to 	 = 'kyawphyo85@gmail.com';
$subject = date("Ymd") . mb_encode_mimeheader(" 開発G作業予定");
$message = file_get_contents('/deploy/qiita/google-api/message.txt', true);
$headers = 'From: kyawphyonaing@aucfan.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>
