<?php
$botToken = '7360087292:AAEv1k_UEWDJ1f0kzQOphu1Bj8d8Rkh5-vo';
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['chat_id'])) {
    $chatId = $data['chat_id'];
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    $keyboard = [
        'keyboard' => [[
            ['text' => 'Share phone number', 'request_contact' => true]
        ]],
        'one_time_keyboard' => true,
        'resize_keyboard' => true
    ];

    $postData = [
        'chat_id' => $chatId,
        'text' => 'Please share your phone number:',
        'reply_markup' => json_encode($keyboard)
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($postData),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        echo json_encode(['success' => false]);
    } else {
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
