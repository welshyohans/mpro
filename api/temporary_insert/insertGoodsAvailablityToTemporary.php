<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';
include_once '../../model/Response.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$isAvailable = $data['isAvailable'];
$categoryId = $data['categoryId'];

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$temporary->insertGoodsAvailablityToTemporary($goodsId,$isAvailable);

include_once '../settings/increaseNewUpdateCode.php';

$response = new Response($db);

$result = $response->getAllGoodsBasedOnCategory($categoryId);
echo json_encode($result);

?>