
<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$orderId = $data['orderId'];
$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->cancelOrder($orderId);

include_once 'getAllOrders.php';




?>