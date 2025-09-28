<?php

class A_Supplier{

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
    
    public function insertSupplier($name,$phone,$password,$address){
        $query = "INSERT INTO a_supplier (id, name, address, phone, priority, password) VALUES (NULL, '$name', '$address', '$phone', '0', '$password')";
        
        $stmt = $this->conn->exec($query);
       
    }

}