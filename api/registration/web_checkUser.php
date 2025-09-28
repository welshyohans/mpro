<?php
include_once '../../config/Database.php';
include_once '../../model/Register.php';


//$jsonData = file_get_contents('php://input');
//$data = json_decode($jsonData, true);

$phoneNo = $_POST['phone'];
$password = $_POST['password'];


$database = new Database();
$db = $database->connect();

$response = new Register($db);
$sr = new ServerResponse(); //this is to get response
$registrationInfo =$response->webIsRegistered($sr,$phoneNo,$password);

echo json_encode($registrationInfo);
//echo "you did it bro";

class ServerResponse{
    public $response;
    public $userId;
    public $name;
    public $phoneNo;
}



?>