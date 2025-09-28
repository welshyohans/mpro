
<?php

include_once '../../config/Database.php';
include_once '../../model/Customer.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$customerId = $data['userId'];

$database = new Database();

$db = $database->connect();

$customer = new Customer($db);

echo json_encode($customer->getCustomerPayment($customerId));

?>