

<?php

include_once '../../config/Database.php';
include_once '../../model/Cover.php';


$database = new Database();

$db = $database->connect();

$cover = new Cover($db);

echo json_encode($cover->getAllSupplier());



?>