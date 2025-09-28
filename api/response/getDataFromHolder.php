<?php

include_once '../../config/Database.php';
include_once '../../model/Response.php';


$database = new Database();
$db = $database->connect();

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId=$data['userId'];
$lastUpdateCode =$data['lastUpdateCode'];

//later i get fcm code	

$response = new Response($db);

$result = $response->getResponseFromHolderTable($lastUpdateCode,$userId);

$r = new R();
$r->data = $result;
//echo json_encode($r);

//echo "\n....".strlen($result);

class R{
    public $data;
}

?>