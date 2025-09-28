<?php

include_once '../../config/Database.php';
include_once '../../model/Chat.php';

$jsonData = file_get_contents('php://input');
$d = json_decode($jsonData, true);

$database = new Database();
$db = $database->connect();

$chat = new Chat($db);

    $idFromUser = $d['id'];
    $userId = $d['userId'];
    $adminId = $d['adminId']; // if it sends from user it is always one
    $content = $d['content'];
    $serverId = $d['serverId'];
    $isReceivedFromUser = $d['isSender'];
	
    $chat->sendMessage($idFromUser,$userId,$adminId,$content,$isReceivedFromUser);


//echo json_encode($chat->getAllMessage($userId));
//later i return new message if not all the messages are i return empty array


?>