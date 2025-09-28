

<?php

include_once '../../config/Database.php';
include_once '../../model/Telegram.php';


$supplierId = $_POST['key'];
$telegramId = $_POST['telegram_id'];
$telegramName = $_POST['telegram_name'];
$userName = $_POST['user_name'];
$phoneNumber=$_POST['phone_number'];


$database = new Database();

$db = $database->connect();

$telegram = new Telegram($db);


$result = $telegram->checkPhoneForGames($telegramId,$phoneNumber);

 echo $result;

?>