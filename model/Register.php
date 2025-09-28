<?php


class Register {

    private $conn;
    public function __construct($conn){

        $this->conn = $conn;
    }

    public function isRegistered($sr,$phoneNo,$password,$phoneModel,$fcmCode){

        //todo latter i will check if the 
        $query = "SELECT u1.id, u1.name,u1.password, u1.phone_number,u1.is_registered, u2.phone_number AS registrar_phone FROM user u1 LEFT JOIN user u2 ON u1.register_by = u2.id WHERE u1.phone_number = :phone_number AND u1.customer_level > -9 LIMIT 1";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':phone_number',$phoneNo);
        $stmt->execute();

        $num = $stmt->rowCount();
        $result = "unregistered";
        if($num>0){
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            $p = $r['registrar_phone'];
            $pp = $r['password'];
            $ri = $r['is_registered'];
            $userId = $r['id'];
            if($p == $password || $pp == $password){
                if($ri){
                    //$result = "registered";	
                    $sr->response = "registered";
                    $sr->userId = $userId;
                }else{
                   // $result = "correct";
                   $sr->response = "correct";
                   $sr->userId = $userId;
                   //i will update fmc values
                   $q1= "UPDATE user SET firebase_code = '$fcmCode' WHERE user.id = '$userId'";
                   $s1 = $this->conn->exec($q1);

                }
            }else{
                $sr->response = "inCorrectPassword";
                $sr->userId = $userId;
                //$result = "incorectPassword";
            }

        }else{
       // $result = "unregistered";
       $sr->response = "unregistered";
       $sr->userId = 0;
        }
        //return $result;
        return $sr;
    }

    public function webIsRegistered($sr,$phoneNo,$password){

        //todo latter i will check if the 
        $query = "SELECT * FROM user WHERE phone_number = :phone_number LIMIT 1";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':phone_number',$phoneNo);
        $stmt->execute();

        $num = $stmt->rowCount();
        $result = "unregistered";
        if($num>0){
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            $p = $r['password'];
            $ri = $r['is_registered'];
            $userId = $r['id'];
            $name = $r['name'];
            $phoneNo = $r['phone_number'];
            if($p == $password){
                if($ri){
                    //$result = "registered";	
                    $sr->response = "registered";
                    $sr->userId = 0;
                    $sr->phoneNo = "00";
                    $sr->$name = "unknown";
                }else{
                   // $result = "correct";
                   $sr->response = "correct";
                   $sr->userId = $userId;
                   $sr->phoneNo = $phoneNo;
                   $sr->name = $name;
                   //i will update fmc values
                   //$q1= "UPDATE user SET firebase_code = '$fcmCode' WHERE user.id = '$userId'";
                   //$s1 = $this->conn->exec($q1);

                }
            }else{
                $sr->response = "inCorrectPassword";
                $sr->userId = 0;
                $sr->phoneNo = "00";
                $sr->name = "unknown";
                //$result = "incorectPassword";
            }

        }else{
       // $result = "unregistered";
       $sr->response = "unregistered";
       $sr->userId = 0;
       $sr->phoneNo = "00";
       $sr->name = "unknown";
        }
        //return $result;
        return $sr;
    }
    
     public function webLinkChecker($sr,$customerId){

        //todo latter i will check if the 
        $query = "SELECT customer.name,customer.phone,user.id AS userId FROM customer LEFT JOIN user ON customer.id = user.customer_id WHERE customer.id = '$customerId' LIMIT 1";
        $stmt = $this->conn->Prepare($query);
        //$stmt->bindParam(':customerId',$customerId);
        $stmt->execute();
    

        $num = $stmt->rowCount();
        $result = "unregistered";
        
        if($num>0){
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            //$p = $r['password'];
            $userId = $r['userId'];
            $name = $r['name'];
            $phoneNo = $r['phone'];

                   // $result = "correct";
                   $sr->response = "correct";
                   $sr->userId = $userId;
                   $sr->phoneNo = $phoneNo;
                   $sr->name = $name;


        }else{
       // $result = "unregistered";
       $sr->response = "unregistered";
       $sr->userId = 0;
       $sr->phoneNo = "00";
       $sr->name = "unknown";
        }
        //return $result;
        return $sr;
    }
    
    public function checkPhoneNumber($phone){
        $result = false;
        $query = "SELECT phone_number FROM user WHERE phone_number = '$phone' AND customer_level >-9";     
        
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();
        
        if($num>0){
           $result =  false;
        }else{
           $result = true;
        }
        return $result;
    }
    public function forgetPassword($phone){
                $result = 0;
        $query = "SELECT phone_number,password FROM user WHERE phone_number = '$phone' AND customer_level >-9 LIMIT 1";     
        
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();
        
        if($num>0){
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
           $result =  $r['password'];
        }else{
           $result = 0;
        }
        return $result;
    }
    public function registerCustomer($name,$phone,$shopName,$password,$registeredBy,$addressId,$specificAddress,$location,$locationDescription){

        $query = "INSERT INTO customer (id, name, phone, shop_name, profile_picture, password, registered_by, address_id, specific_address, location, location_description, shop_image, register_at) VALUES (NULL, '$name', '$phone', '$shopName', 'no', '$password', '$registeredBy', '$addressId', '$specificAddress', '$location', '$locationDescription', 'no', current_timestamp())";
        $stmt = $this->conn->exec($query);
        $customerId = $this->conn->lastInsertId();

        $query1 = "INSERT INTO user (id, customer_id, name, phone_number, password, register_by, profile_picture, is_registered, firebase_code, registered_date) VALUES (NULL, '$customerId', '$name', '$phone', '$password', '$registeredBy', 'no', '0', 'no', current_timestamp())";
        $stmt1 = $this->conn->exec($query1);
        $userId = $this->conn->lastInsertId();
        $this->userLastUPdate($userId);
        return $stmt1;
    }

    public function registerUser($customerId,$name,$phone,$password,$registeredBy){
        $query1 = "INSERT INTO user (id, customer_id, name, phone_number, password, register_by, profile_picture, is_registered, firebase_code, registered_date) VALUES (NULL, '$customerId', '$name', '$phone', '$password', '$registeredBy', 'no', '0', 'no', current_timestamp())";
        $stmt1 = $this->conn->exec($query1);
        $userId = $this->conn->lastInsertId();
        $this->userLastUPdate($userId);
    }

    function userLastUPdate($userId){
      $query = "INSERT INTO user_last_update (id, user_id, last_update_code, last_update_date, is_proved) VALUES (NULL,$userId, '0', current_timestamp(), '0')";
      $stmt = $this->conn->exec($query);
    }
}





?>