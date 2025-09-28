<?php

class Notification{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getAllUsersToken(){
        $query = "SELECT user.id,user.firebase_code FROM user WHERE firebase_code != 'no' AND firebase_code !='no fcm'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allFcm = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachFcm = array(
                    "fcmCode" =>$firebase_code
                );
                array_push($allFcm,$firebase_code);
            }
        }
        return $allFcm;
    }
	
	 public function getSingleToken($userId){
        $query = "SELECT * FROM user WHERE id = '$userId'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['firebase_code'];
    }
	
		
	
	public function insertNotification($title,$body,$goodsList){

        //$presentLastUpdateCode = $this->getNewUpdateCode();
        $query = "INSERT INTO `notification` (`id`, `title`, `body`, `goodsList`) VALUES (NULL, '$title', '$body', '$goodsList')";
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }
    
}


?>