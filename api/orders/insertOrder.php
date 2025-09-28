<?php
include_once '../../config/Database.php';
include_once '../../model/ORDER.php';
include_once '../../model/SMS.php';


$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$userId = $data['userId'];
$customerId = $data['customerId'];
$totalPrice = $data['totalPrice'];
$comment = $data['comment'];
$lastUpdateCode = $data['lastUpdateCode'];
$orderList = $data['orderList'];

$database = new Database();
$db = $database->connect();

$order = new ORDER($db);
$orderId = $order->insertOrder($userId,$customerId,$totalPrice,$lastUpdateCode,$comment);


foreach($orderList as $or){
    $order->insertEachOrder($orderId,$or['goodsId'],$or['quantity'],$or['eachPrice'],$or['useDiscount'],$or['colorOne'],$or['colorTwo'],$or['colorThree'],$or['isColorTouched']);
    //echo "GoodsId: " . $or['goodsId']. "...Quantity:" . $or['quantity'] . "...EachPrice: " . $or['eachPrice'] . "...UseDiscount:" . $or['useDiscount'] . "\n ";
}

//get the customer phone number who ordered
$p = $order->getPhoneNumber($userId);
//include_once 'updateFinishedGoods.php'; //todo for now we don't control finished and unfinished goods

$r = new R();

 $r->orderId = $orderId;
 $r->orderedDate = date('l, F j');
 $r->deliveryTime = $order->getDeliveryTime($customerId);
 
 $t= '+251943090921';
/*    //send sms
    $sms = new SMS();
    
    
    $s = substr($p, 1); //remove zero
$tt= '+251'.$s;
$sms->sendSms($tt,"በትክክል ታዘዋል። እናመሰግናለን 
Phone : 0943090921
Telegram: t.me/merkato_pro
"); 
$sms->sendSms($t,"New Order total price:-".$totalPrice); 
 
 echo json_encode($r);*/
 
 
 
 $sms = new SMS();

$s = substr($p, 1); // Remove zero
$tt = '+251' . $s;

// Prepare and escape arguments for the first SMS
$tt_escaped = escapeshellarg($tt);
$message1 = escapeshellarg("በትክክል ታዘዋል። እናመሰግናለን\nPhone : 0943090921\nTelegram: t.me/merkato_pro");
// Run the first SMS sending in the background
exec("php send_sms.php $tt_escaped $message1 > /dev/null 2>&1 &");

// Prepare and escape arguments for the second SMS
$t_escaped = escapeshellarg($t);
$message2 = escapeshellarg("New Order total price:-" . $totalPrice);
// Run the second SMS sending in the background
exec("php send_sms.php $t_escaped $message2 > /dev/null 2>&1 &");

// Send the response immediately
echo json_encode($r);



class R{
    public $orderId;
    public $orderedDate;
    public $deliveryTime;
}





?>