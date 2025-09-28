


<?php
include_once '../../config/Database.php';
include_once '../../model/Supplier.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$goodsId = $data['goodsId'];
$supplierId = $data['categoryId'];

$price = $data['price'];
$discountStart = $data['discountStart'];

$discountPrice = $data['discountPrice'];
$isUpdate = $data['name'];
$isFromShop = $data['image']; //is used to know from where the request come

$database = new Database();
$db = $database->connect();




$supplier = new Supplier($db);

$supplier->addGoods($goodsId,$supplierId,$price,$discountStart,$discountPrice,$isUpdate);

if($isFromShop == "shop"){
    echo json_encode($supplier->getAllMyShopGoods($supplierId));
}else{
 echo json_encode($supplier->getAllGoods($supplierId));   
}



?>