<?php

include_once '../../config/Database.php';
include_once '../../model/SETTING.php';

$database = new Database();
$db = $database->connect();

$settings = new SETTING($db);
$s = new S();

$s->expireTime = $settings->getExpireTime();
$s->lastUpdateCode = $settings->getNewUpdateCode();

echo json_encode($s);


class S{
    public $expireTime;
    public $lastUpdateCode;
}
?>