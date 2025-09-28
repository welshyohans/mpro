<?php

include_once '../../config/Database.php';
include_once '../../model/Goods.php';


$database = new Database();

$db = $database->connect();

$goods = new Goods($db);

echo json_encode($goods->getAllGoods());


?>