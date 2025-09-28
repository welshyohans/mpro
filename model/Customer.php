<?php

class Customer{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getaAllCustomer(){
      $query = "SELECT *,user.id AS userId FROM user LEFT JOIN customer ON customer.id = user.customer_id WHERE user.customer_level >-1 ORDER BY customer.unpaid DESC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allCustomer = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachCustomer = array(
                "customerId"=>$row["userId"],
                "name"=>$row["name"],
                "phone"=>$row["phone"],
                "shopName"=>$row["shop_name"],
                "password"=>$row["password"],
                "comment"=>$row["comment"],
                "specificAddress"=>$row["specific_address"],
                "addressId"=>$row['address_id'],
                "customerLevel"=>$row['order_number'],
                "fcmCode"=>$row['firebase_code'],
                "location"=>$row['location'],
                "description"=>$row['location_description'],
                "unpaid"=>$row['unpaid'],
                "totalCredit"=>$row['debit_you_have'],
                "permitCredit"=>$row['permitted_debit']
                
            );
            
            array_push($allCustomer,$eachCustomer);
        }
        return $allCustomer;
      }

    }
    
        public function getAllUsers(){
      $query = "SELECT customer.shop_name,customer.specific_address,user.password,user.phone_number,user.name,user.id FROM customer LEFT JOIN user ON customer.id = user.customer_id WHERE user.customer_level >-1 ORDER BY customer.register_at DESC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allCustomer = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachCustomer = array(
                "userId"=>$row["id"],
                "userName"=>$row["name"],
                "phone"=>$row["phone_number"],
                "shopName"=>$row["shop_name"],
                "password"=>$row["password"],
                "specificAddress"=>$row["specific_address"]
            );
            
            array_push($allCustomer,$eachCustomer);
        }
        return $allCustomer;
      }

    }
	
	    public function getaAllUserForNotification(){
      $query = "SELECT user.id,user.name,user.phone_number,user.firebase_code,user.customer_level,customer.shop_name,customer.address_id,customer.specific_address FROM user LEFT JOIN customer ON user.customer_id = customer.id WHERE user.customer_level > -1 ORDER BY user.customer_level DESC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allCustomer = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachCustomer = array(
                "userId"=>$id,
                "name"=>$name,
                "phone"=>$phone_number,
                "shopName"=>$shop_name,
				"firebaseCode" =>$firebase_code,
                "customerLevel"=>$customer_level,
				"addressId"=>$address_id,
                "specificAddress"=>$row["specific_address"]
            );
            
            array_push($allCustomer,$eachCustomer);
        }
        return $allCustomer;
      }

    }
    
    
    public function getCustomerPayment($customerId){
        $cId = $this->getCustomerId($customerId);
        $query = "SELECT * FROM payments WHERE customer_id = '$cId'";
        
       $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allPayments = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachPayments = array( 
                "amount"=>$row["amount"],
                "through"=>$row["through"],
                "paidDate"=>$row["paid_date"],
                "additionalInfo"=>$row["additional_info"]
            );
            
            array_push($allPayments,$eachPayments);
        }
        return $allPayments;
      }
        
    }
    public function getCustomerId($userId){
      $query = "SELECT customer_id FROM user WHERE user.id = '$userId' LIMIT 1";
       $stmt = $this->conn->prepare($query);
       $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              return $row["customer_id"];
            } else {
            return 0; // or return '' if you prefer an empty string
          }
    }
    
     public function updateCustomerDetail($userId,$customerId,$name,$location,$addressId,$specificAddress,$description){
      $query = "UPDATE customer SET name = '$name',location = '$location',address_id = '$addressId',specific_address = '$specificAddress',location_description = '$description' WHERE customer.id = '$customerId'";
        $stmt = $this->conn->prepare($query);
         $stmt->execute();
        
        
            $query2 = "UPDATE user SET name = '$name' WHERE user.id = '$userId'";
        $stmt2 = $this->conn->prepare($query2);
        return $stmt2->execute();
    }
    
    
    public function getUserPhone(){
        $query = "SELECT DISTINCT phone_number,name FROM user";
     $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allUser = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachUser = array(
                "name"=>$row["name"],
                "phoneNumber"=>$row["phone_number"]
            );
            
            array_push($allUser,$eachUser);
        }
        return $allUser;
      }
        
    }
    public function getAddressBasedPhone($customerLevel,$addressId){ // i get phone number based on address only latter i will add customer level

        $query;
             //this also need customer level and address
          if($customerLevel==0){
              //this message is for all level of customer
                 $query = "SELECT DISTINCT phone FROM customer LEFT JOIN user ON user.customer_id = customer.id WHERE customer.address_id = '$addressId' AND user.customer_level>-9";
        }else if($customerLevel==1){
            //this message is for customer who does not order from us
                $query = "SELECT DISTINCT phone FROM customer LEFT JOIN user ON user.customer_id = customer.id WHERE customer.address_id = '$addressId' AND user.customer_level =0";
    
        }else if($customerLevel ==2){
               //this message is middle level customer means customer below 5
                   $query = "SELECT DISTINCT phone FROM customer LEFT JOIN user ON user.customer_id = customer.id WHERE customer.address_id = '$addressId' AND user.customer_level <5 AND user.customer_level>0";
        }else{
          //this is customer level greater than 5
              $query = "SELECT DISTINCT phone FROM customer LEFT JOIN user ON user.customer_id = customer.id WHERE customer.address_id = '$addressId' AND user.customer_level>4";
         }

    //$query = "SELECT DISTINCT phone_number FROM user WHERE customer_level=10";
     $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allUser = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachUser = array(
                "phoneNumber"=>$row["phone"]
            );
            
            array_push($allUser,$eachUser);
        }
        return $allUser;
      }
        
    
    }
    
    public function updateComment($customerId,$comment){
                $query = "UPDATE customer SET comment = '$comment' WHERE customer.id = '$customerId'";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
	 public function getAllUserForChat(){
      $query = "SELECT user.id, user.name,user.phone_number,customer.shop_name, customer.specific_address ,COALESCE(chat.content,'no') AS content, COALESCE(chat.sendAt,0) AS chat_time FROM user LEFT JOIN ( SELECT user_id, MAX(sendAt) AS last_chat_time FROM chat GROUP BY user_id ) last_chat ON user.id = last_chat.user_id LEFT JOIN chat ON user.id= chat.user_id AND last_chat.last_chat_time = chat.sendAt LEFT JOIN customer ON user.customer_id = customer.id WHERE user.customer_level >-1 ORDER BY chat.sendAt DESC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allUser = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachUser = array(
                "userId"=>$row["id"],
                "name"=>$row["name"],
                "phoneNumber"=>$row["phone_number"],
                "shopName"=>$row["shop_name"],
                "specificAddress"=>$row["specific_address"],
                "content"=>$row["content"]
            );
            
            array_push($allUser,$eachUser);
        }
        return $allUser;
      }

    }
    
    public function insertPayment($customerId,$amount,$method,$additional){
            $query = "INSERT INTO payments (id, customer_id, paid_date, amount, through, additional_info) VALUES (NULL, '$customerId', NULL, '$amount', '$method', '$additional')";
        $stmt = $this->conn->prepare($query);
         $stmt->execute();
        
        
     $query2 = "UPDATE customer SET unpaid = unpaid - '$amount' WHERE customer.id = '$customerId'";
        $stmt2 = $this->conn->prepare($query2);
        return $stmt2->execute();
    }
  
}


    ?>