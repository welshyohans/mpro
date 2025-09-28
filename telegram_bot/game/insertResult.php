<?php

include_once '../../config/Database.php';
include_once '../../model/Telegram.php';


$telegramId = $_POST['telegram_id'];
$userId = $_POST['user_id'];
$drawNumber = $_POST['draw_number'];


$database = new Database();

$db = $database->connect();

$telegram = new Telegram($db);


$result = $telegram->insertPlayerResult($userId,$telegramId,$drawNumber);

echo $result;

?>