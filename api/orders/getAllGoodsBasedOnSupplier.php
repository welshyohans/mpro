
<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';


$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->getAllGoodsBasedOnSupplier();

echo json_encode($result);

?>