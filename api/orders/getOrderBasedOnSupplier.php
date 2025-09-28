<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

//$supplierId = $data['supplierId'];
$address = $data['addressArray'];


$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->getOrderBasedOnSupplier($address);

//echo $address[0];
echo json_encode($result);




?>