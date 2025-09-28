<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->getAllAddress();

echo json_encode($result);




?>