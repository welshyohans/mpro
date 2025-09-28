
<?php
include_once '../../config/Database.php';
include_once '../../model/SpecialOffer.php';



// the user can come up with their userId and addressId 

$database = new Database();
$db = $database->connect();

$response = new SpecialOffer($db);
echo json_encode($response->getAllGoodsFromSpecial());

?>