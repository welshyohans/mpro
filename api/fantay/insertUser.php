
<?php
include_once '../../config/Database.php';
include_once '../../model/Fantay.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$name = $data['name'];
$phone = $data['phone'];
$address= $data['address'];

$database = new Database();
$db = $database->connect();




$user = new Fantay($db);

$result = $user->insertUser($name,$phone,$address);
$r = new R();
$r->id = $result;
$r->name = $name;
$r->phone=$phone;
$r->address= $address;

echo json_encode($r);


class R{
    public $id;
    public $name;
    public $phone;
    public $address;
}
?>