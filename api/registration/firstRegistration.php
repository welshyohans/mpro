<?php
include_once '../../config/Database.php';
include_once '../../model/Register.php';
include_once '../../model/SMS.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$phoneNo = $data['phoneNo'];
$password = $data['password'];
$phoneModel = $data['phoneModel'];
$fcmCode = $data['fcmCode'];


$database = new Database();
$db = $database->connect();

//in this case we will retrun with user's id and response if the use is not allowed to use 
// the user id will be zero or something known to express
$response = new Register($db);
$sr = new ServerResponse(); //this is to get response
$registrationInfo =$response->isRegistered($sr,$phoneNo,$password,$phoneModel,$fcmCode);


$sms = new SMS();
$s = substr($phoneNo, 1);
$t= '+251'.$s;
//$t= '+251943080871';
$m= 'የ MerkatoPro.com ተጠቃሚ ስለሆኑ እናመሰግናለን! ያዘዙትን እቃ በፍጥነት እና በተማኝንነት እናደርሳለን ።  ለበለጠ መረጃ  ወደ 0943090921 ወይም 0943080871 ይደውሉ' ;

if($registrationInfo->response == "correct"){
$sms->sendSms($t,$m);    
}


echo json_encode($registrationInfo);



class ServerResponse{
    public $response;
    public $userId;
}



?>