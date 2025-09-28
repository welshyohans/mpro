<?php
// Your bot token
$botToken = '7360087292:AAEv1k_UEWDJ1f0kzQOphu1Bj8d8Rkh5-vo';

// Set the Telegram API URL
$apiUrl = "https://api.telegram.org/bot{$botToken}";

// Function to handle incoming requests
function handleTelegramRequest() {
    $input = file_get_contents('php://input');
    $update = json_decode($input, true);

    if (isset($update['message'])) {
        $message = $update['message'];
        $chatId = $message['chat']['id'];

        // Check if the contact is shared
        if (isset($message['contact'])) {
            $phoneNumber = $message['contact']['phone_number'];
            sendMessage($chatId, "Thank you for sharing your phone number: $phoneNumber");

            // Optionally, redirect to the mini app with the phone number
            $miniAppUrl = "https://kulushey.com/kulushi/telegram_bot/index.html?phone=" . urlencode($phoneNumber);
            sendMessage($chatId, "Open the mini app: $miniAppUrl");
        } else {
            // Request contact
            requestContact($chatId);
        }
    }
}

// Function to send a message via Telegram API
function sendMessage($chatId, $text) {
    global $apiUrl;
    $params = [
        'chat_id' => $chatId,
        'text' => $text
    ];
    file_get_contents($apiUrl . '/sendMessage?' . http_build_query($params));
}

// Function to request contact
function requestContact($chatId) {
    global $apiUrl;
    $keyboard = [
        'keyboard' => [[[
            'text' => 'Share Contact',
            'request_contact' => true
        ]]],
        'one_time_keyboard' => true,
        'resize_keyboard' => true
    ];
    $params = [
        'chat_id' => $chatId,
        'text' => 'Please share your contact information.',
        'reply_markup' => json_encode($keyboard)
    ];
    file_get_contents($apiUrl . '/sendMessage?' . http_build_query($params));
}

// Handle the incoming request
handleTelegramRequest();
?>
