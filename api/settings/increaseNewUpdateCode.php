<?php

include_once '../../config/Database.php';
include_once '../../model/SETTING.php';
include_once '../../model/Temporary.php';
include_once '../../model/Holder.php';

//$password = $_POST['password']; // this code must run if we pass the correct password

$database = new Database();
$db = $database->connect();

$settings = new Setting($db);
$newUpdatedCode = getNewUpdateCode($settings);

$temporary = new Temporary($db,$newUpdatedCode);
$holder = new Holder($db);

addNewUpdateCodeToHolder($settings,$newUpdatedCode);
updateAllDataFromTemporaryTable($temporary);

updateHolderTable($holder,$newUpdatedCode);

insertIncrementedNewUpdateCode($settings,$newUpdatedCode);


//when we press increase UpdateCode in our home Screen 
 //1. get the newUpdateCode from setting table and increment by 1
 //2. get all address ids and insert empty data with different address and the new updatecode in to holder table 
 //3. update all data from temporary tables(goods,category,price....)
 //4. update all rows in holder table based on there lastUpdateCode and address compare to newUpdateAddress
 //5. finally return with inserted new updatecode


 function getNewUpdateCode($settings){
    return $settings->getNewUpdateCode() + 1;
 }

 function addNewUpdateCodeToHolder($settings,$newUpdatedCode){

    // in this part we insert empty data to holder table based on addressId and newUpdateCode
    $settings->insertNewUpdateCodeToHolder($newUpdatedCode);
}

 function updateAllDataFromTemporaryTable($temporary){
    $temporary->insertUpdatedPriceFromTemporary();
    $temporary->insertUpdatedGoodsFromTemporary();
    $temporary->insertUpdatedColorFromTemporary();
    $temporary->insertUpdatedGoodsAvailabilityFromTemporary();
    $temporary->insertUpdatedCategoryAvailabilityFromTemporary();
    $temporary->insertUpdatedPriorityFromTemporary();
    $temporary->insertUpdatedCodeGiverFromTemporary();
    $temporary->insertUpdatedCommissionsFromTemporary();
    $temporary->insertUpdatedCategoryFromTemporary();
 }

 function updateHolderTable($holder,$newUpdatedCode){
    
    $holder->insertDataToHolder($newUpdatedCode);

 }

 function insertIncrementedNewUpdateCode($settings,$newUpdatedCode){
    $settings->insertIncrementedUpdateCode($newUpdatedCode);
 }


//echo json_encode($settings->getArrayOfAddressIds());
//lastly we retrun setting table


$s = new S();

$s->expireTime = $settings->getExpireTime();
$s->lastUpdateCode = $settings->getNewUpdateCode();

//echo json_encode($s);


class S{
    public $expireTime;
    public $lastUpdateCode;
}


?>