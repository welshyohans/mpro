<?php

include_once '../../config/Database.php';
include_once '../../model/Response.php';

//$userId = $_POST['user_id'];
//$addressId = $_POST['address_id'];	

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId=$data['userId'];	
//$addressId = $data['addressId'];
//$ordered = $data['ordered'];
//$ordeder1 = $order[1]


// the user can come up with their userId and addressId 

$database = new Database();
$db = $database->connect();

$response = new Response($db);
$addressId = $response->getAddressId($userId);//this address is used for only commission not specific address

$r = new R();
$s = new Settings();
$r->settings = $response->getAllSettings($userId,$addressId,$s);
$r->goodsList = $response->getAllGoods3($addressId);
$r->categoryList = $response->getAllCategories();
$r->codesList = $response->getAllCodeGiver();

echo json_encode($r);







class R{
    public $settings;
    public $goodsList;
    public $categoryList;
    public $codesList;
}


class Settings{

    public $appUpdateInfo;

    public $shopName;
    public $userName;
    public $phoneNumber;
    public $customerId;
    public $userId;
    public $userImageUrl;
    public $address;
    public $addressId;

    public $expireTime;
    public $deliveryTime;
    public $lastUpdateCode;

}


?>





