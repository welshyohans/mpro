<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$temporary->updateFinishedGoods();

include_once '../settings/increaseNewUpdateCode.php';

//echo json_encode();

?>