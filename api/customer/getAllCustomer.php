<?php

include_once '../../config/Database.php';
include_once '../../model/Customer.php';


$database = new Database();

$db = $database->connect();

$customer = new Customer($db);

echo json_encode($customer->getaAllCustomer());

?>