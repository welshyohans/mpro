<?php

include_once '../../config/Database.php';
include_once '../../model/Temporary.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$categoryId = $data['categoryId'];
$brandId = 10; // for now it is not necessary to use brand
$model = $data['model'];
$description = $data['description'];
$seo = $data['seo'];
$minOrder = $data['minOrder'];
$maxOrder = $data['maxOrder'];
$discountStarts = $data['discountStarts'];
$showInHome = $data['showInHome'];
$isAvailable = $data['isAvailable'];
$purePrice = $data['purePrice'];
$isUpdate = $data['isUpdate'];
$imageUrl= $data['imageUrl'];
$shopId = $data['shopId'];


$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);

$temporary->insertGoodsToTemporary($goodsId,$categoryId,$brandId,$model,$description,$seo,$purePrice,$minOrder,$maxOrder,0,$showInHome,$imageUrl,$discountStarts,$isAvailable,0,$isUpdate,$shopId);

include_once '../settings/increaseNewUpdateCode.php';


?>