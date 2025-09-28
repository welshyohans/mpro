<?php


include_once '../../config/Database.php';
include_once '../../model/SETTING.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId=$data['userId'];
$lastUpdateCode =$data['lastUpdateCode'];
$fcmCode = $data['fcmCode'];
//later i get fcm code	

$database = new Database();
$db = $database->connect();

$setting = new SETTING($db);


$setting->updateFcm($userId,$fcmCode);
$r = new CheckResponse();

if($setting->getNewUpdateCode() == $lastUpdateCode){
    $r->response = "uptodate";
    echo json_encode($r);
}else if($setting->getOverAllUpdateCode()<$lastUpdateCode){
    $r->response = "fromholder";
    echo json_encode($r);
}else{
    $r->response = "fromnew";
    echo json_encode($r);
}

/*if($setting->getNewUpdateCode() == $lastUpdateCode){
    $r->response = "uptodate";
    echo json_encode($r);
}else if($setting->checkLastUpdateCodeInHolder($lastUpdateCode)>0){
    $r->response = "fromholder";
    echo json_encode($r);
}else{
    $r->response = "fromnew";
    echo json_encode($r);
}*/

class CheckResponse{
    public $response;
}









?>