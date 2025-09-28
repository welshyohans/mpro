


<?php
include_once '../../config/Database.php';
include_once '../../model/A_Supplier.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$name = $data['name'];
$phone = $data['phone'];
$password= $data['password'];
$address = $data['address'];


$database = new Database();
$db = $database->connect();




$supplier = new A_Supplier($db);

$supplier->insertSupplier($name,$phone,$password,$address);

echo 'success';


?>