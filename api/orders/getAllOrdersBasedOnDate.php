<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

// Check if a date is provided in the query string
// Use current date if not provided
$order_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get orders for the specified date
$result = $order->getAllOrdersByDate($order_date);

echo json_encode($result);
?>