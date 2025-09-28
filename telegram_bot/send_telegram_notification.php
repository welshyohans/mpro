<?php
include_once '../config/Database.php';
$token = '7417302388:AAGPEbUpddHJjnD-Zbl__FyrQ9-sWA1bUm0';
$apiUrl = "https://api.telegram.org/bot$token/";
$chatId = '6410628620';  // Ensure chat ID is correct and in quotes

//first get the list of userName with their Name is not available make it null
    $database = new Database();
    $db = $database->connect();
    //get the list of user name and 
    getAllUser($db);
    
    
//sendTelegramMessage($chatId, "hello from merkato pro");
//sendWelcomeMessage($chatId,"welsh");

function sendTelegramMessage($chatId, $message) {
    global $apiUrl;

    $data = [
        'chat_id' => $chatId,
        'text'    => $message
    ];

    // Build URL
    $url = $apiUrl . "sendMessage?" . http_build_query($data);

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Optional: disable SSL verification (not recommended for production)
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
    } else {
        // Decode the response to see what Telegram says
        $responseData = json_decode($response, true);
        if (!$responseData['ok']) {
            echo 'Telegram error: ' . $responseData['description'];
        } else {
            echo 'Message sent successfully!';
        }
    }
    curl_close($ch);
}

function getAllUser($db){
    
            
            $q = "SELECT telegram.telegram_name,telegram.telegram_id,telegram.user_id,user.id,user.name FROM telegram LEFT JOIN user ON telegram.user_id = user.id WHERE telegram.user_id !=0";
            
    $stmt = $db->prepare($q);
    $stmt->execute();

    $num = $stmt->rowCount();

   // $quantityList = array();

    if($num>0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            sendWelcomeMessage($telegram_id,$name);
            echo 'selam: '.$name.' from now on you can order using telegram. telegramId: ' .$telegram_id;
            echo '<br>';
/*            $eachQuantity = array(
                "userName" =>$name,
                "address" =>$specific_address,
                "quantity" =>$quantity,
                "orderListId" =>$id
            );*/
           // array_push($quantityList,$eachQuantity);
        }
    }
    //return $quantityList;
        
}

// Function to send the welcome message
function sendWelcomeMessage($chatId,$name) {
    global $apiUrl;

    $logoUrl = "https://merkatopro.com/img/mm.png"; // Replace with your logo URL
    /*$welcomeText = "Welcome to Merkato Pro!\n\nYou can Order Mobile and Mobile Accessories from confort of your shop!!";*/
    $welcomeText = "ሰላም " .$name. " ከዛሬ ጀምሮ ቀጥታ በዚ ቴሌግራም ከመርካቶ Pro ማዘዝ ይችላሉ!! ለበለጠ መረጃ ወደ 0943090921 ይደውሉ!!";

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


    
?>
