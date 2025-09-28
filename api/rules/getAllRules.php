<?php

include_once '../../config/Database.php';
include_once '../../model/Rules.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];

$database = new Database();

$db = $database->connect();

$rules = new Rules($db);

echo json_encode($rules->getAllRules($userId));


?>