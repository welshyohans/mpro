<?php
include_once '../../model/FCM.php';
include_once '../../config/Database.php';
include_once '../../model/Notification.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$body = $data['body'];
$title = $data['title'];
$goodsList =$data['goodsList'];
$fcmList = $data['fcmList'];

$database = new Database();
$db = $database->connect();
$notification = new Notification($db);
$notificationId = $notification->insertNotification($title,$body,$goodsList);
$b = "1$".$notificationId."&".$title."&".$body."&no|".$goodsList;
$fcm = new FCM();
//first insert to notification table than get the last insered id
foreach($fcmList as $r){
    //echo $r."\n";
	//echo $b;
   $fcm->sendFcm($r,$title,$b,0,"notification detail");
}
//$fcm = new FCM();

?>