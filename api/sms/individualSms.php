<?php

include_once '../../model/SMS.php';
include_once '../../config/Database.php';
include_once '../../model/FCMService.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

//$userId=$data['userId'];	

$phone = $data['phone'];
$body = $data['message'];
$isSms = $data['isSms'];
$fcmCode = $data['fcmCode'];

$title = $data['title'];
//$isNotifable = $data['is_notifable'];
$goodsIdList = $data['goodsIdList'];



$database = new Database();

$db = $database->connect();

$s = substr($phone, 1);
$t= '+251'.$s;

if($isSms==1){
    //send sms
    $sms = new SMS();
$sms->sendSms($t,$body); 
}else{
    //send notification
    
  $fcm = new FCMService('from-merkato', '../../model/serviceAccount.json');
  $notificationDetail = '1$0&'.$title.'&'.$body.'&no|'.$goodsIdList;
  $result = $fcm->sendFCM($fcmCode, $title, $body, 0, $notificationDetail);

echo $result;
}


?>