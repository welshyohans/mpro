<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$adminId = $data['adminId'];
$eachPrice = $data['eachPrice'];
$previousPrice = $data['previousPrice'];
$quantity = $data['quantity'];

$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

//first insert the purchase
$order->purchaseGoods($goodsId,$adminId,$eachPrice,$previousPrice,$quantity);

//then get request of ordered goods 
$result = $order->getOrderListBasedOnGoods();
//$result = "success";

echo json_encode($result);

?>