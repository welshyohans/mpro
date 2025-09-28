<?php


header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

include_once '../../config/Database.php';
include_once '../../model/Response.php';


//$jsonData = file_get_contents('php://input');
//$data = json_decode($jsonData, true);

$userId=$_POST['userId'];	


$database = new Database();
$db = $database->connect();

$response = new Response($db);
$addressId = $response->getAddressId($userId);//this address is used for only commission not specific address

$r = new R();

$r->product = $response->webGetAllGoods($addressId);
$r->category = $response->webGetAllCategories();

echo json_encode($r);

class R{
    public $product;
    public $category;
}
?>





