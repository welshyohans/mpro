
<?php

include_once '../../config/Database.php';
include_once '../../model/Cover.php';

$jsonData = file_get_contents('php://input');
$d = json_decode($jsonData, true);

$database = new Database();
$db = $database->connect();

$cover = new Cover($db);

     $supplierId = $d['supplierId'];
     $coverId = $d['coverId'];
     $price = $d['price'];
	
    $result = $cover->updateCoverExistence($coverId,$supplierId,$price);
    
   echo json_encode($result);

?>