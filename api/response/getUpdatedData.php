<?php

include_once '../../config/Database.php';
include_once '../../model/Response.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId=$data['userId'];	
$lastUpdateCode = $data['lastUpdateCode'];

$database = new Database();
$db = $database->connect();

$response = new Response($db);

$addressId = $response->getAddressId($userId);//this address is used for only commission not specific address

$r = new R();
$s = new Settings();

$r->setting =$response->getUpdatedSetting($userId,$s);
$r->updatePrice = $response->getUpdatedPrice($lastUpdateCode);
$r->updateGoods = $response->getUpdatedGoods($lastUpdateCode,$addressId);
$r->updateCategory = $response->getUpdatedCategory($lastUpdateCode);
$r->updateGoodsAvailable = $response->getUpdatedGoodsAvailable($lastUpdateCode);
$r->updateCategoryAvailable =$response->getUpdatedCategoryAvailable($lastUpdateCode);
$r->updatePriority=$response->getUpdatedPriority($lastUpdateCode);
$r->deletedGoods=$response->deletedGoods($lastUpdateCode);

echo json_encode($r);







class R{
    public $setting;
    public $updatePrice;
    public $updateGoods;
    public $updateCategory;
    public $updateGoodsAvailable;
    public $updateCategoryAvailable;
    public $updatePriority;
    public $deletedGoods;
}


class Settings{

    public $appUpdateInfo;
    public $phoneTime;
    public $expireTime;
    public $deliveryTime;
    public $lastUpdateCode;

}


?>





