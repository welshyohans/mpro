
<?php
include_once '../../config/Database.php';
include_once '../../model/Response.php';

$database = new Database();
$db = $database->connect();

$response = new Response($db);

$result = $response->getAllGoodsForUpdate();
echo json_encode($result);

?>