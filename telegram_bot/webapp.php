<?php

$token = "7360087292:AAEv1k_UEWDJ1f0kzQOphu1Bj8d8Rkh5-vo";
$ngrokUrl = "https://kulushey.com/kulushi/telegram_bot/index.html"; // Replace with your Ngrok URL and path

$apiUrl = "https://api.telegram.org/bot$token/setChatMenuButton";

$data = [
    'menu_button' => [
        'type' => 'web_app',
        'text' => 'Start',
        'web_app' => [
            'url' => $ngrokUrl
        ]
    ]
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);

echo $response;
?>
