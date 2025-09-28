
<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once '../../config/Database.php';
include_once '../../model/ORDER.php';
include_once '../../model/Customer.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$orderId = $data['orderId'];
$userId = $data['userId'];
$statusId = $data['statusId'];

$database = new Database();
$db = $database->connect();

$customer = new Customer($db);


//first update comment then return the list of customer

$customerId = $customer->getCustomerId($userId);

$order = new ORDER($db);

if($statusId == 9){
    $result = $order->mixOrder($orderId,$userId);
}else if($statusId == 7){
   $result = $order->cancelOrder($orderId); 
}else{
   $result = $order->updateToDeliver($orderId,$statusId,$customerId); 
}




//include_once 'getAllOrders.php';




?>