
<?php
include_once '../../config/Database.php';
include_once '../../model/Customer.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$customerId = $data['customerId'];
$comment = $data['comment'];

$database = new Database();
$db = $database->connect();




$customer = new Customer($db);

$customer->updateComment($customerId,$comment);

//first update comment then return the list of customer

echo json_encode($customer->getaAllCustomer());


?>