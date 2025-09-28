<?php

include_once '../../config/Database.php';
include_once '../../model/SETTING.php';

$expireTime = $_POST['expireTime'];	

$database = new Database();
$db = $database->connect();

$settings = new SETTING($db);

if($settings->updateExpireTime($expireTime))
{
    echo 'you successfully updated';
}else{
    echo 'you failed to update';
}


?>