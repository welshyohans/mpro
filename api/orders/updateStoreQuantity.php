<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$store =$data['store'];
$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->updateStoreQuantity($goodsId,$store);

//echo json_encode($result);

include_once 'getOrderedListBasedOnGoods.php';


?>