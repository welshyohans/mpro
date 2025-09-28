
<?php
include_once '../../config/Database.php';
include_once '../../model/Supplier.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$name = $data['shopName'];

$database = new Database();
$db = $database->connect();




$supplier = new Supplier($db);

$supplier->insertSupplier($name);

include_once '../orders/getAllShops.php';


?>