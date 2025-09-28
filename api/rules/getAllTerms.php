<?php

include_once '../../config/Database.php';
include_once '../../model/Rules.php';


$database = new Database();

$db = $database->connect();

$rules = new Rules($db);

echo json_encode($rules->getAllTerms());


?>