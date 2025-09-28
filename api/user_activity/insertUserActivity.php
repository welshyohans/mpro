<?php
include_once '../../config/Database.php';
include_once '../../model/UserActivity.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$lastUpdateCode = $data['lastUpdateCode'];
$userPhoneTime = $data['phoneTime'];
$userActivity = $data['userActivity'];


$database = new Database();
$db = $database->connect();



$uA= new UserActivity($db);
$uA->updateUserLastCode($userId,$lastUpdateCode);

//echo json_encode($userActivity);
//echo "userId : $userId";
//echo "lastUpdateCode : $lastUpdateCode";
//echo "userPhoneTime : $userPhoneTime \n";

foreach($userActivity as $userA){
    $uA->insertUserActivity($userId,$userPhoneTime,$userA['activityType'],$userA['insertedTime'],$userA['additionalInfo']);
  //  echo  $userA['activityType'];
    //echo $userA['additionalInfo'];
    //echo $userA['insertTime'];
}

//echo "success";
$r = new R();
$r->response = "success";
echo json_encode($r);

class R{
    public $response;
}
?>