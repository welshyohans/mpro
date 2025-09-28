<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$orderId = $data['orderId'];
$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

//this array comes from related to goods table
$r1 = $order->getOrderListBasedOnUser($orderId);

//this array comes from related to new_goods table
$r2 = $order->getOrderListFromNewGoods($orderId);

//this array comes from related to new_goods table
//$r3 = $order->getOrderListFromCover($orderId);

$result = array_merge($r1, $r2);

echo json_encode($result);




?>