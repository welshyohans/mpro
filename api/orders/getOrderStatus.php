
<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->getOrderStatus($userId);

echo json_encode($result);




?>