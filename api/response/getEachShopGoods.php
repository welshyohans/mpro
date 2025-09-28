<?php

include_once '../../config/Database.php';
include_once '../../model/Response.php';

//$userId = $_POST['user_id'];
//$addressId = $_POST['address_id'];	

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$shopId=$data['addressId'];
$userId = $data['userId'];
//$addressId = $data['addressId'];
//$ordered = $data['ordered'];
//$ordeder1 = $order[1]


// the user can come up with their userId and addressId 

$database = new Database();
$db = $database->connect();

$response = new Response($db);
$addressId = $response->getAddressId($userId);//this address is used for only commission not specific address

$r= $response->getEachShopGoods($addressId,$shopId);
echo  json_encode($r);



?>