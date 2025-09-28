
<?php
include_once '../../config/Database.php';
include_once '../../model/Response.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$categoryId=$data['categoryId'];

$database = new Database();
$db = $database->connect();


$response = new Response($db);

$result = $response->getAllGoodsBasedOnCategory($categoryId);
echo json_encode($result);

?>