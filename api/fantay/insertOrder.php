<?php
include_once '../../config/Database.php';
include_once '../../model/Fantay.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$totalPrice = $data['totalPrice'];
$orderList = $data['orderList'];

$database = new Database();
$db = $database->connect();

$fantay = new Fantay($db);
$orderId = $fantay->insertOrder($userId,$totalPrice);


foreach($orderList as $or){
    $fantay->insertEachOrder($orderId,$or['goodsId'],$or['quantity'],$or['eachPrice']);
}

echo 'success';



?>