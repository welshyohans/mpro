<?php
include_once '../../config/Database.php';
include_once '../../model/Register.php';
include_once '../../model/SMS.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);


$phone = $data['phoneNo'];



$database = new Database();
$db = $database->connect();

$response = new Register($db);
$r = new R();
$password = $response->forgetPassword($phone);
if($password !=0){

$sms = new SMS();
$s = substr($phone, 1);
$t= '+251'.$s;

$m= 'your password is :'.$password;
$sms->sendSms($t,$m);

$r->response = "success";
 echo json_encode($r);

}else{
    $r->response = "phone";
 echo json_encode($r);
}


//echo $result;
class R{
    public $response;
}





?>