

<?php

include_once '../../config/Database.php';
include_once '../../model/Cover.php';

$jsonData = file_get_contents('php://input');
$d = json_decode($jsonData, true);

$database = new Database();
$db = $database->connect();

$cover = new Cover($db);

     $supplierId = $d['supplierId'];
	
    $result = $cover->getAllCoverBasedOnSupplier($supplierId);
    
   echo json_encode($result);

?>