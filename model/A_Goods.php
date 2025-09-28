<?php

class A_Goods{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }
  
    public function getAllSupplier(){
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
    
    public function insertGoods($name,$categoryId,$image,$description,$insertedBy){
        $query = "INSERT INTO a_goods (id, name, category_id, image, description, priority, show_in_home, is_available, category_two, min_order, discount_min_order, last_update_code, edited_by, inserted_by) VALUES (NULL, '$name', '$categoryId', '$image', '$description', '0', '1', '1', '0', '1', '0', '0', '0', '$insertedBy')";
        
        $stmt = $this->conn->exec($query);
       
    }
    
    public function getAllCategory(){
        
        $query = "SELECT * FROM category WHERE parent_id = 0 AND is_available !=0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allCategory = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCategory = array(
                    "categoryId" =>$id,
                    "name" =>$name
                );
                array_push($allCategory,$eachCategory);
            }
        }
        return $allCategory;
    
    }
    

}