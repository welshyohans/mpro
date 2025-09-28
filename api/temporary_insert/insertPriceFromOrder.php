<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';
include_once '../../model/Response.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$purePrice = $data['purePrice'];
//$categoryId = $data['categoryId'];
$store = $data['store']; 
$previousPrice = $data['previousPrice'];
$priorityValue = $data['priority'];
$AOC = $data['aOC'];
$ATC = $data['aTC'];


$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);

//in this part i will update commission as well as priority 

$temporary->insertCommustionToTemporary($goodsId,0,$AOC,$AOC,$ATC,$ATC); //latter i will correct for update 
$temporary->insertPriorityToTemporary($goodsId,$priorityValue);
$temporary->updatePrice($goodsId,$purePrice,$previousPrice,$store);

include_once '../settings/increaseNewUpdateCode.php';


$response = new Response($db);

$result = $response->getAllGoodsForUpdate();
echo json_encode($result);





?>