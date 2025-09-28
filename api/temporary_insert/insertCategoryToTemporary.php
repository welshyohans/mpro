<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$categoryId = $data['categoryId'];
$parentId = $data['parentId'];
$toSubCategory = $data['toSubCategory'];
$name = $data['name'];
$imageUrl = $data['imageUrl'];
$isAvailable = $data['isAvailable'];
$isUpdate = $data['isUpdate'];

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$temporary->insertCategoryToTemporary($categoryId,$parentId,$toSubCategory,$name,$imageUrl,$isAvailable,$isUpdate);
include_once '../settings/increaseNewUpdateCode.php';
include_once '../category/getCategoryForEdit.php';
?>