<?php
include_once '../../config/Database.php';
include_once '../../model/Register.php';


//$jsonData = file_get_contents('php://input');
//$data = json_decode($jsonData, true);

$customer = $_POST['customer'];



$database = new Database();
$db = $database->connect();

$response = new Register($db);
$sr = new ServerResponse(); //this is to get response
$registrationInfo =$response->webLinkChecker($sr,$customer);



echo json_encode($registrationInfo);
//echo "customerId:".$customer;

class ServerResponse{
    public $response;
    public $userId;
    public $name;
    public $phoneNo;
}



?>