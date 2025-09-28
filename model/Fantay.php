<?php

class Fantay{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }
  
    public function getAllAddress(){
        $query = "SELECT * FROM address";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allAddress = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachAddress = array(
                    "addressId" =>$id,
                    "addressName" =>$sub_city
                );
                array_push($allAddress,$eachAddress);
            }
        }
        return $allAddress;
    }
    
    public function insertAddress($subCity){
        $query = "INSERT INTO address (id, city, sub_city, commission_address) VALUES (NULL, 'Addis Abeba', '$subCity', '2')";
        
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        
        
        $q = "INSERT INTO deliver_time (id, address_id, when_to_deliver, deliver_at, active_deliver) VALUES (NULL, '$last_id', 'ሁሌም ከ ሰኞ እስከ ቀዳሜ ከ 3 ሰአት ጀምሮ እናደርሳለን!!', current_timestamp(), 'በሰአታችን እናደርሳለን!!');";
        
        $s = $this->conn->exec($q);
       
    }
    public function insertUser($name,$phone,$address){
                $query = "INSERT INTO fantay_user (id, name, phone, address) VALUES (NULL, '$name', '$phone', '$address')";
        
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        
        return $last_id;
    }
    public function insertGoods($id,$name,$price,$description,$isAvailable,$categoryId,$image){
        if($id == 0){
         $query = "INSERT INTO fantay_goods (id, name, image, price, category_id, description, available) VALUES (NULL, '$name', '$image', '$price', '$categoryId', '$description', '$isAvailable')";
        
        $stmt = $this->conn->exec($query);
        }else{
            //in this case it needs update
        $q = "INSERT INTO fantay_user (id, name, phone, address) VALUES (NULL, '$name', '$phone', '$address')";
        
        $s = $this->conn->exec($q);
        }
    }
    
    public function updateGoodsAvailablity($id,$value){
        $q = "UPDATE fantay_goods SET available = '$value' WHERE fantay_goods.id = '$id'";
        
        $s = $this->conn->exec($q);
    }
    public function updateGoodsPrice($id,$price){
    $q = "UPDATE fantay_goods SET price = '$price' WHERE fantay_goods.id = '$id'";
        
        $s = $this->conn->exec($q);
    }
    public function getAllGoods(){
        
        $query = "SELECT * FROM fantay_goods WHERE available !=0 ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachGoods = array(
                    "id" =>$id,
                    "name"=>$name,
                    "price"=>$price,
                    "isAvailable"=>$available,
                    "image"=>$image,
                    "description"=>$description
                );
                array_push($allGoods,$eachGoods);
            }
        }
        return $allGoods;
    
    
        
    }
    
    
        public function getAllUsers(){
        
        $query = "SELECT * FROM fantay_user";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachGoods = array(
                    "id" =>$id,
                    "name"=>$name,
                    "phone"=>$phone,
                    "address"=>$address
                );
                array_push($allGoods,$eachGoods);
            }
        }
        return $allGoods;
    
    }
    
    public function getAllOrders(){
        
        $query = "SELECT fantay_order.*,fantay_user.name AS userName,fantay_user.phone FROM fantay_order LEFT JOIN fantay_user ON fantay_user.id = fantay_order.user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachGoods = array(
                    "orderId" =>$id,
                    "userName"=>$userName,
                    "totalPrice"=>$total_price,
                    "phone"=>$phone,
                    "isChecked"=>$is_checked
                );
                array_push($allGoods,$eachGoods);
            }
        }
        return $allGoods;
    
    }
        public function getAllGoodsForAdmin(){
        
        $query = "SELECT * FROM fantay_goods";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachGoods = array(
                    "id" =>$id,
                    "name"=>$name,
                    "price"=>$price,
                    "isAvailable"=>$available,
                    "image"=>$image,
                    "description"=>$description
                );
                array_push($allGoods,$eachGoods);
            }
        }
        return $allGoods;
    
    }
    
  public function insertOrder($userId,$totalPrice){

        $query = "INSERT INTO fantay_order (id, user_id, total_price, is_checked, date) VALUES (NULL, '$userId', '$totalPrice', '0', current_timestamp())";
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }



    public function insertEachOrder($orderId,$goodsId,$quantity,$eachPrice){
        
        $query = "INSERT INTO fantay_order_list (id, order_id, price, quantity, goods_id) VALUES (NULL, '$orderId', '$eachPrice', '$quantity', '$goodsId')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
 
    }

}