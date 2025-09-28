<?php
include_once '../../model/FCM.php';
include_once '../../config/Database.php';
include_once '../../model/Notification.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);


$body = $data['body'];
$title = $data['title'];
$isNotifable = $data['is_notifable'];
$notificationDetail = $data['notificationDetail'];

$database = new Database();
$db = $database->connect();

$notification = new Notification($db);
$result = $notification->getAllUsersToken();
//echo json_encode();
$fcm = new FCM();

foreach($result as $r){
   // echo $r['fcmCode'];
   $fcm->sendFcm($r,$title,$body,$isNotifable,$notificationDetail);
}
//$fcm = new FCM();


?>