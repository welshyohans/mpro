

<?php
include_once '../../config/Database.php';
include_once '../../model/Fantay.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$id = $data['id'];
$value = $data['value'];

$database = new Database();
$db = $database->connect();




$fantay = new Fantay($db);

$result = $fantay->updateGoodsAvailablity($id,$value);

include_once 'getAllGoodsForAdmin.php';


?>