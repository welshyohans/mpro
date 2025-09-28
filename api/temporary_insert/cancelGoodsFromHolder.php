<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';
include_once '../../model/Response.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$purePrice = $data['purePrice'];


$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);

$temporary->insertGoodsAvailablityToTemporary($goodsId,0);


include_once '../settings/increaseNewUpdateCode.php';

$order = new ORDER($db);

$order->updateStoreToZero($goods_id);
//then get request of ordered goods 
$result = $order->getOrderListBasedOnGoods();
//$result = "success";

echo json_encode($result);





?>