
<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once '../../config/Database.php';
include_once '../../model/Customer.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$orderId = $data['orderId'];
$amount = $data['amount'];
$paymentMethod = $data['paymentMethod'];
$additionalInfo = $data['additionalInfo'];



$database = new Database();
$db = $database->connect();




$customer = new Customer($db);


//first update comment then return the list of customer

$customerId = $customer->getCustomerId($userId);

//insert to payment 
// substruct from unpaid
$customer->insertPayment($customerId,$amount,$paymentMethod,$additionalInfo);

?>