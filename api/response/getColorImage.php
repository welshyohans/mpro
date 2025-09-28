<?php
include_once '../../config/Database.php';
include_once '../../model/Response.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$colorId = $data['colorId'];
$goodsId = $data['goodsId'];

$database = new Database();
$db = $database->connect();

$response = new Response($db);

$result = $response->getColorImage($colorId,$goodsId);
$r = new R();
$r->response = $result;
echo json_encode($r);


class R{
    public $response;
}
?>