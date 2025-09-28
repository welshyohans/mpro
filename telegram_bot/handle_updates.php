<?php
$botToken = '7360087292:AAEv1k_UEWDJ1f0kzQOphu1Bj8d8Rkh5-vo';

function handleUpdates($updates) {
    foreach ($updates as $update) {
        if (isset($update['message']['contact'])) {
            $chatId = $update['message']['chat']['id'];
            $phoneNumber = $update['message']['contact']['phone_number'];

            // Process the phone number (e.g., store it in a database)
            file_put_contents('phone_numbers.txt', "Chat ID: $chatId, Phone Number: $phoneNumber\n", FILE_APPEND);

            // Send a confirmation message
            sendMessage($chatId, "Thank you for sharing your phone number welsh: $phoneNumber");
        }
    }
}

function getUpdates($botToken) {
    $url = "https://api.telegram.org/bot$botToken/getUpdates";
    $updates = json_decode(file_get_contents($url), true);
    return $updates['result'];
}

function sendMessage($chatId, $message) {
    global $botToken;
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($postData),
        ],
    ];

    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}

$updates = getUpdates($botToken);
handleUpdates($updates);
?>
