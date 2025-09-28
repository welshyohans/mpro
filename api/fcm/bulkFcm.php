<?php
include_once '../../model/FCMService.php';
include_once '../../config/Database.php';
include_once '../../model/Notification.php';

//$jsonData = file_get_contents('php://input');
//$data = json_decode($jsonData, true);

$fcmCode = "dPo07lhMShS0CWao-2Hb6D:APA91bGVJ_1zMq-MwXSICqMq8uC5zCVlTILifwhHW4jH8p7z5uXJWLFxA4J_ndnK8eTGfWgsZiT7xPDgk8hE6BOVi-cu0SNYnAG6Ckv5aPEGWuwEwtJppIU";
$body = $_POST['body'];
$title = $_POST['title'];
//$isNotifable = $data['is_notifable'];
$goodsIdList =$_POST['goodsId'];
//$notificationDetail = $data['notificationDetail'];

$database = new Database();
$db = $database->connect();

$notification = new Notification($db);
$r= $notification->getAllUsersToken();
//echo json_encode($r);


$tokens = array("dPo07lhMShS0CWao-2Hb6D:APA91bGVJ_1zMq-MwXSICqMq8uC5zCVlTILifwhHW4jH8p7z5uXJWLFxA4J_ndnK8eTGfWgsZiT7xPDgk8hE6BOVi-cu0SNYnAG6Ckv5aPEGWuwEwtJppIU","dUFRRNm0Tayy9rE09HpFXg:APA91bGS7wrAJFk6lWYppyXvFiKworGwsCmaupBUwrO9esK5_MjVH3me3WlCzP5iyv2pw_UFB3kJew2r_QAcqsacTA5rfx2uN7u8k66qf-Pz9_Vh6Fyzd5O7ctzMK5_nKBP4PU6MaCaq");

$fcm = new FCMService('from-merkato', '../../model/serviceAccount.json');
$notificationDetail = '1$0&'.$title.'&'.$body.'&no|'.$goodsIdList;
$result = $fcm->sendMultipleFCM($r, $title, $body, 0, $notificationDetail);

//$result = $fcm->sendFCM('dPo07lhMShS0CWao-2Hb6D:APA91bGVJ_1zMq-MwXSICqMq8uC5zCVlTILifwhHW4jH8p7z5uXJWLFxA4J_ndnK8eTGfWgsZiT7xPDgk8hE6BOVi-cu0SNYnAG6Ckv5aPEGWuwEwtJppIU', 'Merkato is Just a Click Away!', 'Why make the trip? Get all the latest mobile accessories delivered directly to your shop from Merkato. Browse now and restock effortlessly!', 0, '1$20&Merkato is Just a Click Away!&Why make the trip? Get all the latest mobile accessories delivered directly to your shop from Merkato. Browse now and restock effortlessly!&no|0');
//echo $result;




?>