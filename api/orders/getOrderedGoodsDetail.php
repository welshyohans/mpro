<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->getOrderedGoodsDetail($goodsId);

echo json_encode($result);




?>