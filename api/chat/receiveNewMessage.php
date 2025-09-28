<?php

include_once '../../config/Database.php';
include_once '../../model/Chat.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);


$database = new Database();
$db = $database->connect();

$chat = new Chat($db);
$userId = $data['userId'];

echo json_encode($chat->getAllMessage($userId));
//later i return new message if not all the messages are i return empty array


?>