<?php

include_once '../../config/Database.php';
include_once '../../model/A_Goods.php';


$database = new Database();

$db = $database->connect();

$category = new A_Goods($db);

echo json_encode($category->getAllCategory());


?>