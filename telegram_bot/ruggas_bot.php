<?php

$token = '7695176280:AAFh8CUSDY9JsdVB6GBth3so8Dg7nrK5Ok0';
$apiUrl = "https://api.telegram.org/bot$token/";

// Set your OpenAI API key and endpoint
$openaiApiKey = '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';
$openaiApiUrl = "https://api.openai.com/v1/chat/completions";

/*$openaiApiKey = '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';
$openaiApiUrl = "https://openrouter.ai/api/v1/chat/completions";*/

// Get the updates from Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Check if the update contains a message
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];

    // Check if the message is the "/start" command
    if ($text == "/start") {
        sendWelcomeMessage($chatId);
    } else {
        sendOpenAIMessage($chatId, $text);
    }
}

// Function to send the welcome message
function sendWelcomeMessage($chatId) {
    global $apiUrl;

    $logoUrl = "https://kulushey.com/kulushi/img/rrr.jpg"; // Replace with your logo URL
    $welcomeText = "Welcome to Ruggas Shop!\n\nYou can Explore and Order Mobile Accessories from the comfort of your home!!";

    // Define the Web App URL that should be opened inside Telegram
    $webAppUrl = "https://kulushey.com/kulushi/telegram_bot/ruggas.html"; // Replace with your mini-app URL

    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => 'Open App',
                    'web_app' => ['url' => $webAppUrl]
                ]
            ]
        ]
    ];

    $data = [
        'chat_id' => $chatId,
        'photo' => $logoUrl,
        'caption' => $welcomeText,
        'reply_markup' => json_encode($keyboard)
    ];

    file_get_contents($apiUrl . "sendPhoto?" . http_build_query($data));
}

// Function to send a text message via Telegram
function sendTelegramMessage($chatId, $message) {
    global $apiUrl;

    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    file_get_contents($apiUrl . "sendMessage?" . http_build_query($data));
}

// Function to send the user's message to OpenAI and return the response
function sendOpenAIMessage($chatId, $text) {
    global $openaiApiKey, $openaiApiUrl;

    // Prepare the data for the OpenAI API call
    $postData = [
        'model' => 'o3-mini',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a friendly and helpful customer support chatbot for an online electronics store. 
Your goal is to assist customers with their questions and issues regarding our products and services. 
Please be polite and professional in your responses. always response in amharic language and in geez characters'],
            ['role' => 'user', 'content' => $text]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $openaiApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openaiApiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        sendTelegramMessage($chatId, "Error contacting OpenAI: " . $error_msg);
        return;
    }
    curl_close($ch);

    $responseData = json_decode($result, true);

    if (isset($responseData['choices'][0]['message']['content'])) {
        $reply = $responseData['choices'][0]['message']['content'];
        sendTelegramMessage($chatId, $reply);
    } else {
        sendTelegramMessage($chatId, "Error: Unable to get a valid response from OpenAI.");
    }
}
?>
