<?php
include_once '../../config/Database.php';
include_once '../../model/Register.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$name = $data['name'];
$phone = $data['phone'];
$password = $data['password'];
$registeredBy = $data['registeredBy'];
$customerId = $data['customerId'];

$database = new Database();
$db = $database->connect();

$response = new Register($db);
$result = $response->registerUser($customerId,$name,$phone,$password,$registeredBy);
echo $result;


?>