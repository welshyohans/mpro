<?php
// Your bot token
$botToken = '7360087292:AAEv1k_UEWDJ1f0kzQOphu1Bj8d8Rkh5-vo';

// Set the API URL
$apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

// Extract the parameter from the URL
$startParam = isset($_GET['start']) ? $_GET['start'] : 'No parameter found';

// Prepare the message
$message = [
    'chat_id' => $chatId, // Set the chat ID where you want to send the message
    'text' => "You passed: {$startParam}"
];

// Send the message
$response = file_get_contents($apiUrl . '?' . http_build_query($message));

// Output the response for debugging
echo $response;
echo 'hollo';
?>
