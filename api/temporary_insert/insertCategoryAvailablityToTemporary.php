<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$categoryId = $data['categoryId'];
$isAvailable = $data['isAvailable'];

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$temporary->insertCategoryAvailablityToTemporary($categoryId,$isAvailable);
include_once '../settings/increaseNewUpdateCode.php';
include_once '../category/getCategoryForEdit.php';
?>