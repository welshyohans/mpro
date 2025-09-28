




<?php
include_once '../../config/Database.php';
include_once '../../model/Supplier.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$supplierId = $data['categoryId'];
$isFromShop = $data['image'];


$database = new Database();
$db = $database->connect();




$supplier = new Supplier($db);

$supplier->deleteGoods($goodsId,$supplierId);

if($isFromShop == "shop"){
    echo json_encode($supplier->getAllMyShopGoods($supplierId));
}else{
 echo json_encode($supplier->getAllGoods($supplierId));   
}


?>