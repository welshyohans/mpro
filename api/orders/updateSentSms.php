
<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

//$supplierId = $data['supplierId'];
$addresses = $data['addressArray'];
$suppliers = $data['supplierArray'];


$database = new Database();
$db = $database->connect();

$order = new ORDER($db);

$result = $order->updateSentSms($addresses,$suppliers);

echo 'success';
//echo $address[0];
//echo json_encode($result);




?>