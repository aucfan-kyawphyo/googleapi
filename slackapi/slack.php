<?php

function send_to_slack($message) {
    //at_tech_dept channel
    //$webhook_url = 'https://hooks.slack.com/services/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';
    //testing to @kyawphyo
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

$text = file_get_contents('/deploy/googleapi/slackapi/message.txt', true);
$message = array(
  'username' => 'kyawphyo',
  'text' => $text
);

send_to_slack($message);