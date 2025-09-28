<?php

include_once '../../config/Database.php';
include_once '../../model/Telegram.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$totalPrice = $data['totalPrice'];
$orderList = $data['orderList'];
$comment = $data['comment'];


$database = new Database();

$db = $database->connect();

$telegram = new Telegram($db);


$orderId = $telegram->insertWebOrder($userId,$totalPrice,$comment);


foreach($orderList as $or){
    $telegram->insertWebsEachOrder($orderId,$or['id'],$or['quantity'],$or['price']);
    //echo "GoodsId: " . $or['goodsId']. "...Quantity:" . $or['quantity'] . "...EachPrice: " . $or['eachPrice'] . "...UseDiscount:" . $or['useDiscount'] . "\n ";
}

echo 'success';



?>