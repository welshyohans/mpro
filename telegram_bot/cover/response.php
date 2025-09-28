<?php

include_once '../../config/Database.php';
include_once '../../model/Telegram.php';


$supplierId = $_POST['key'];
$telegramId = $_POST['telegram_id'];
$telegramName = $_POST['telegram_name'];
$userName = $_POST['user_name'];


$database = new Database();

$db = $database->connect();

$telegram = new Telegram($db);


$result = $telegram->checkUser($telegramId,$userName,$telegramName,$supplierId);

$s = "ok";
$r = new R();
if($result < 5){
    $r->cover =array();
    $r->userId = $result;

    echo json_encode($r);
}else{


    $c = $telegram->getAllCover($supplierId);
    //$r->status = $s;
    $r->cover =$c;
    $r->userId = $result;
    
    echo json_encode($r);
    //echo 'your registered successfully:'.$result;
}

class R{
    //public $status;
    public $userId;
    public $cover;
}

//echo 'Key: '.$key.' and TelegramId :'.$telegramId.' Telegram Name:'.$userName;

/*if($result == 0){
    echo 'result: to register phone number';
}else if($result ==1){
    echo 'you are not connect with user table';
}else if($result ==2){
    echo 'you did not permit to use this';
}else if($result ==3){
    echo 'the link is expired';
}*/

?>