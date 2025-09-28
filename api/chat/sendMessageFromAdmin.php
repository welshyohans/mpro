<?php

include_once '../../config/Database.php';
include_once '../../model/Chat.php';
include_once '../../model/Notification.php';
include_once '../../model/FCM.php';


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

$notification = new Notification($db);
$fcm = new FCM();
$result = $notification->getSingleToken($userId);

$b = $userId.":1:".$content;
$body = "1$5&Kulushi Admin&".$b."&no|1";
$fcm->sendFcm($result,'Kulushi Admin',$body,0,"notification detail");
//echo $result;
echo json_encode($chat->getAllMessage($userId));
//later i return new message if not all the messages are i return empty array


?>