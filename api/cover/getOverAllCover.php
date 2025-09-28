
<?php

include_once '../../config/Database.php';
include_once '../../model/Cover.php';

$jsonData = file_get_contents('php://input');
$d = json_decode($jsonData, true);

$database = new Database();
$db = $database->connect();

$cover = new Cover($db);
$userId = $d['userId'];

$r = new R();
$r->coverList = $cover->getAllCover();
$r->supplierList = $cover->getAllSupplierForUser();
$r->coverSupplierList=$cover->getAllCoverSupplier();

    
   echo json_encode($r);
   
   
  class R{
      public $coverList;
      public $supplierList;
     public $coverSupplierList;
  }


?>