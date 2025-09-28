<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';
include_once '../../model/Response.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$purePrice = $data['purePrice'];
$categoryId = $data['categoryId'];
$store = $data['store'];
$previousPrice = $data['previousPrice'];

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);
$temporary->updatePrice($goodsId,$purePrice,$previousPrice,$store);

//$temporary->insertPriceToTemporary($goodsId,$purePrice);


//include_once '../settings/increaseNewUpdateCode.php';

$response = new Response($db);

$result = $response->getAllGoodsBasedOnCategory($categoryId);
echo json_encode($result);





?>