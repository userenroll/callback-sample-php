<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Aws\Sns\Exception\InvalidSnsMessageException;

// Make sure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die;
}
 
try {
    // Create a message from the post data and validate its signature
    $message = Message::fromRawPostData();
    $validator = new MessageValidator();
    $validator->validate($message);
} catch (InvalidSnsMessageException $e) {
   // Invalid message
   http_response_code(400);
   error_log('SNS Message Validation Error: ' . $e->getMessage);
   die();
}
 
if ($message['Type'] === 'SubscriptionConfirmation') {
   // Confirm the subscription by sending a GET request to the SubscribeURL
   file_get_contents($message['SubscribeURL']);
}

// Data sent from callback
$callbackData = json_decode($message['Message']);
