<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//header('Content-Type: application/json');
include_once '../../config/Database.php';
include_once '../../model/Supplier.php';

$database = new Database();
$db = $database->connect();

$response = new Supplier($db);

// Get the raw posted data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);


    $shopId= $data['shopId'];
    $shopName = $data['shopName'];
    $shopDetail = $data['shopDetail'];
    $isVisible = $data['isVisible'];

$response->updateSupplier($shopId,$shopName,$shopDetail,$isVisible);

 echo json_encode(array('message' => 'Supplier Updated Successfully.'));


?>