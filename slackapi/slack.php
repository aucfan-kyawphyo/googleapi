<?php

function send_to_slack($message) {
  $webhook_url = 'https://hooks.slack.com/services/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';
  $options = array(
    'http' => array(
      'method' => 'POST',
      'header' => 'Content-Type: application/json',
      'content' => json_encode($message),
    )
  );
  $response = file_get_contents($webhook_url, false, stream_context_create($options));
  return $response === 'ok';
}

$text = file_get_contents('/var/www/html/googleapi/slackapi/message.txt', true);
$message = array(
  'username' => 'Testing',
  'text' => $text
);

send_to_slack($message);
