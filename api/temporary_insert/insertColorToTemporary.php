<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$colorId = $data['colorId'];
$quantity = $data['quantity'];

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$temporary->insertColorToTemporary($goodsId,$colorId,$quantity);

?>