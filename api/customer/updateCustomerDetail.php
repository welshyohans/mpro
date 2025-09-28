
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../../config/Database.php';
include_once '../../model/Customer.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$userName = $data['userName'];
$location = $data['location'];
$addressId = $data['addressId'];
$specificAddress = $data['specificAddress'];
$description = $data['description'];


$database = new Database();
$db = $database->connect();




$customer = new Customer($db);


//first update comment then return the list of customer

$customerId = $customer->getCustomerId($userId);

$customer->updateCustomerDetail($userId,$customerId,$userName,$location,$addressId,$specificAddress,$description);


?>