<?php

include_once '../../model/SMS.php';
include_once '../../config/Database.php';
include_once '../../model/Customer.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

//$userId=$data['userId'];	

$addressId = $data['addressId'];
$m = $data['message'];
$customerLevel = $data['customer_level'];
$database = new Database();

$db = $database->connect();

$customer = new Customer($db);


$customerList = $customer->getAddressBasedPhone($customerLevel,$addressId);
//echo json_encode($customerList);

$phones = array();



foreach($customerList as $c){
    $s = substr($c['phoneNumber'], 1);
$t= '+251'.$s;
array_push($phones, $t);

}
echo json_encode($phones);
$sms = new SMS();
$sms->addressBasedSms($phones,$m);
/*
$sms = new SMS();
foreach($customerList as $c){
    $s = substr($c['phoneNumber'], 1);
$t= '+251'.$s;
$name = $c['name'];
$m = 'ሰላም '.$name.' ሩጋስ ቤት ታላቅ ቅናሽ አርገዋል። ለትንሽ ግዜ የሚቆይ ሩጋስ ቻርጀር እና Socket ቅናሽ ተደርጎበታል። ከቻርጀር ከ 30 እስከ 50 ብር ቅናሽ የተደረገለት ሲሆን። ለ socket ደሞ 100 ብር ጀምሮ እስከ 360 ብር ቅናሽ ተደርጎለታል። የቅናሹ ተጠቃሚ ይሁኑ። በ merkatoPro ሲያዙን በፍጥነት እናደርስሎታለን። ለበለጠ መረጃ ወደ 0943090921 ወይም 0943080871 ይደውሉ። merkatopro.com';

   $sms->sendSms($t,$m); 
}*/




//echo 'test...';

?>