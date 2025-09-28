<?php

include_once '../../config/Database.php';
include_once '../../model/Color.php';


$database = new Database();

$db = $database->connect();

$color = new Color($db);

echo json_encode($color->getAllColors());













?>