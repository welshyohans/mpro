<?php
include_once '../../config/Database.php';
include_once '../../model/Register.php';
include_once '../../model/SMS.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$name = $data['name'];
$phone = $data['phone'];
$shopName = $data['shopName'];
$password = $data['password'];
$registeredBy = $data['registeredBy'];
$addressId = $data['addressId'];
$specificAddress = $data['specificAddress'];
$location = $data['location'];
$locationDescription = $data['locationDescription'];

$database = new Database();
$db = $database->connect();

$response = new Register($db);
$r = new R();
if($response->checkPhoneNumber($phone)){
    //you can register else we send its password using sms
    $result = $response->registerCustomer($name,$phone,$shopName,$password,$registeredBy,$addressId,$specificAddress,$location,$locationDescription);

$sms = new SMS();
$s = substr($phone, 1);
$t= '+251'.$s;
//$t= '+251943080871';
$m= 'your password is :'.$password;
$sms->sendSms($t,$m);

$r->response = "success";
 echo json_encode($r);
//echo 'success';
}else{
    $r->response = "phone";
 echo json_encode($r);
}


//echo $result;
class R{
    public $response;
}





?>