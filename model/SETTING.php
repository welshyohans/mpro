<?php

class SETTING{

    private $conn;

    public function __construct($conn){

        $this->conn = $conn;
    }

    public function updateExpireTime($hour){
        $query = "UPDATE settings SET value = '$hour' WHERE setting_key = 'expire_time'";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

	    public function getExpireTime(){
        $settings_query = "SELECT * from settings WHERE setting_key = 'expire_time'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $expireTime = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        return $expireTime['value'];
    }
	
    public function getNewUpdateCode() {
        $settings_query = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $newUpdateCode = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        return $newUpdateCode['value'];

    }
   public function getOverAllUpdateCode() {
        $settings_query = "SELECT * from settings WHERE setting_key = 'over_all_update'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $newUpdateCode = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        return $newUpdateCode['value'];

    }

    public function getArrayOfAddressIds(){
        $query = "SELECT * FROM address";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        $addressList = array();
        if($num > 0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
              array_push($addressList,$row['id']);   
            }
        }
       return $addressList;

    }

    public function insertNewUpdateCodeToHolder($newUpdateCode){
        //first get all address id
       /* $addressIds = $this->getArrayOfAddressIds();
        foreach($addressIds as $addressId){
            $query = "INSERT INTO update_holder (id, last_update_code, address_id,data_size,actual_data) VALUES (NULL,'$newUpdateCode','$addressId',0,'updated')";
            $stmt = $this->conn->exec($query);
            //echo $addressId.": ".$newUpdatedCode;
        }*/
		
		//for now we have only two addresses for commisssion
 $query = "INSERT INTO update_holder (id, last_update_code, address_id,data_size,actual_data) VALUES (NULL,'$newUpdateCode',1,0,'updated')";
            $stmt = $this->conn->exec($query);
		
$q = "INSERT INTO update_holder (id, last_update_code, address_id,data_size,actual_data) VALUES (NULL,'$newUpdateCode',2,0,'updated')";
            $stmt1 = $this->conn->exec($q);
    }

    public function insertIncrementedUpdateCode($newUpdateCode){
        $query = "UPDATE settings SET value = '$newUpdateCode' WHERE setting_key = 'new_update_code'";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    public function checkLastUpdateCodeInHolder($lastUpdateCode){
        $query = "SELECT id FROM update_holder WHERE last_update_code = '$lastUpdateCode'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->rowCount();

    }

    public function updateFcm($userId,$fcmCode){
        $q1= "UPDATE user SET firebase_code = '$fcmCode' WHERE user.id = '$userId'";
        $s1 = $this->conn->exec($q1);
    }
    public function deleteUpdateHolder(){
        $q1= "DELETE FROM update_holder";
        $s1 = $this->conn->exec($q1);
    }
}



?>