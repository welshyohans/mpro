
<?php
include_once '../../config/Database.php';
include_once '../../model/SETTING.php';

$database = new Database();
$db = $database->connect();

$settings = new SETTING($db);
 $settings->deleteUpdateHolder();
echo 'success';
?>