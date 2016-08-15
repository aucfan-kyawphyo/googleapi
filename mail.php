<?php
$to      = 'memo@aucfan.com';
//$to 	 = 'kyawphyonaing@aucfan.com';
mb_language("uni");
mb_internal_encoding("utf-8");
$subject = date("Ymd") . " DevOps作業予定";
$subject = mb_convert_encoding($subject,'utf-8',mb_detect_encoding($subject));
$subject = mb_encode_mimeheader($subject,'iso-2022-jp');
$message = file_get_contents('/deploy/qiita/googleapi/message.txt', true);
$headers = 'From: ohmi@aucfan.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>

