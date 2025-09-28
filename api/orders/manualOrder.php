<?php

//this file is not working at this time b/ce it needs additional investigation 
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$orderId = $data['orderId'];
$price = $data['price'];
$name = $data['name'];
$quantity = $data['quantity'];
$profit = $data['profit'];

$database = new Database();
$db = $database->connect();

$order = new ORDER($db);


//first insert into new_goods table after that it inseted to orderList
$goodsId= $order->manualOrder($orderId,$name,$quantity,$price,$profit);


$order->insertEachOrder($orderId,$goodsId,$quantity,$price,0,'0','0','0',0);

//this array comes from related to goods table
$r1 = $order->getOrderListBasedOnUser($orderId);

//this array comes from related to new_goods table
$r2 = $order->getOrderListFromNewGoods($orderId);

//this array comes from related to new_goods table
$r3 = $order->getOrderListFromCover($orderId);

$result = array_merge($r1, $r2,$r3);
   
echo json_encode($result);

?>