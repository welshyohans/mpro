
<?php

include_once '../../config/Database.php';
include_once '../../model/Category.php';

$database = new Database();

$db = $database->connect();

$category = new Category($db);

echo json_encode($category->getAllSimilarGlass());








?>