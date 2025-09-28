<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$shopsId =$data['shopsId'];
$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->changeShop($goodsId,$shopsId);

//echo json_encode($result);

include_once 'getOrderedListBasedOnGoods.php';


?>