
<?php

include_once '../../config/Database.php';
include_once '../../model/Fantay.php';


$database = new Database();

$db = $database->connect();

$goods = new Fantay($db);

echo json_encode($goods->getAllGoodsForAdmin());


?>