<?php

include_once '../../config/Database.php';
include_once '../../model/Supplier.php';

// Get the raw posted data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$customerId = $data['goodsId'];

$database = new Database();

$db = $database->connect();

$supplier = new Supplier($db);

echo json_encode($supplier->getAllCredits($customerId));


?>