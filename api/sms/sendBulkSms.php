<?php

include_once '../../model/SMS.php';

//$db = $database->connect();
$t= '+251943090921';
$m= 'what an amazing thing happened';
$sms = new SMS();


   $sms->sendBulkSms($t,$m); 



//echo 'test...';

?>