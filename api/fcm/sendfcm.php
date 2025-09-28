<?php
include_once '../../model/FCMService.php';
include_once '../../config/Database.php';
include_once '../../model/Notification.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$fcmCode = $data['fcmCode'];
$body = $data['body'];
$title = $data['title'];
//$isNotifable = $data['is_notifable'];
$goodsIdList = $data['goodsIdList'];
//$notificationDetail = $data['notificationDetail'];

$database = new Database();
$db = $database->connect();

/*$notification = new Notification($db);
$r= $notification->getAllUsersToken();*/
//echo json_encode($result);


/*$tokens = array("f59hMGFERAeNoM5r7mu9-L:APA91bEW2eRSrJPG-XIsWFTk0keu3xAFao8IBDaw3NM2V_2gBrTWofVuK46vXvjwOXv0K8_iAJRL0VydzUUcOlzSkUl0u-_mPEN0LEQYs9R2vDAvhHwqF9RTQfjBDYt3EGdeW20Hk0MW","dUFRRNm0Tayy9rE09HpFXg:APA91bGS7wrAJFk6lWYppyXvFiKworGwsCmaupBUwrO9esK5_MjVH3me3WlCzP5iyv2pw_UFB3kJew2r_QAcqsacTA5rfx2uN7u8k66qf-Pz9_Vh6Fyzd5O7ctzMK5_nKBP4PU6MaCaq");
*/
$fcm = new FCMService('from-merkato', '../../model/serviceAccount.json');
$notificationDetail = '1$0&'.$title.'&'.$body.'&no|'.$goodsIdList;
$result = $fcm->sendFCM($fcmCode, $title, $body, 0, $notificationDetail);

//$result = $fcm->sendFCM('fm_CEvK2SkChnDJ0L5Nlpo:APA91bGvZ3zU6Wp5hjTUbSAsVwWuZIrmHW1O0hVwRGRJAHjv2qgDPI4waP7P8m46ZaJLswgqRzA_yRLPFdwEEJHrgM6Rsrnzn0oRsxnjnqQa-Lu2CYdXvyPj8jXeZKFXcEzCLa9mjYln', 'Merkato is Just a Click Away!', 'Why make the trip? Get all the latest mobile accessories delivered directly to your shop from Merkato. Browse now and restock effortlessly!', 0, '1$20&Merkato is Just a Click Away!&Why make the trip? Get all the latest mobile accessories delivered directly to your shop from Merkato. Browse now and restock effortlessly!&no|0');
echo $result;




?>