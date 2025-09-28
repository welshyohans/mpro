<?php

include_once '../config/Database.php';

$token = '7417302388:AAGPEbUpddHJjnD-Zbl__FyrQ9-sWA1bUm0';
$apiUrl = "https://api.telegram.org/bot$token/";

// Get the updates from Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Check if the update contains a message
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    
    
    $database = new Database();
    $db = $database->connect();

    insertChat($db,$chatId,$text);


    // Check if the message is the "/start" command
    if ($text == "/start") {
       // sendWelcomeMessage($chatId);
        sendTelegramMessage($chatId, "this is merkato pro you can order what ever you want");
       
    }else{
        sendWelcomeMessage($chatId); 
    }
}



// Function to send the welcome message
function sendWelcomeMessage($chatId) {
    global $apiUrl;

    $logoUrl = "https://merkatopro.com/img/mm.png"; // Replace with your logo URL
    $welcomeText = "Welcome to Merkato Pro!\n\nYou can Order Mobile and Mobile Accessories from confort of your shop!!";

    // Define the Web App URL that should be opened inside Telegram
    $webAppUrl = "https://kulushey.com/kulushi/telegram_bot/category.html"; // Replace with your mini-app URL

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

 function insertChat($db,$chatId,$message){

        
        $query = "INSERT INTO telegram_chat (id, telegram_id, message) VALUES (NULL, '$chatId', '$message')";
        $stmt = $db->exec($query);
       // $last_id = $db->lastInsertId();
        //return $last_id;
    }

?>
