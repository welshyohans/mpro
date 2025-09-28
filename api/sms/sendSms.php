<?php

include_once '../../model/SMS.php';


$today = date('j');
$today_c = (int)$today -8;

$customerNumber = $today_c *2;

//echo "today is:-".$today_c;
$t= '+251943090921';
$m= 'Today you must give service  to '.$customerNumber.' customers';
$sms = new SMS();
//$sms->sendSms($t,$m); 


?>