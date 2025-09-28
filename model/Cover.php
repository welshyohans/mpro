<?php

class Cover{
    
    private $conn;
    
    public function __construct($conn){
        $this->conn = $conn;
    }
    
    public function getAllSupplier(){
        
      $query = "SELECT * FROM cover_supplier";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allSupplier = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachSupplier = array(
                "supplierId"=>$row["id"],
                "name"=>$row["supplier_name"],
                "address"=>$row["address"],
                "isExpire"=>$row["is_expire"],
                "additionalInfo"=>$row["additional_info"]
            );
            
            array_push($allSupplier,$eachSupplier);
        }
        return $allSupplier;
      }

    
    }
    
    public function insertSupplier($name,$address,$additionalInfo){
                $query = "INSERT INTO cover_supplier (id, supplier_name, address, is_expire, additional_info) VALUES (NULL, '$name', '$address', '1', '$additionalInfo')";
        $stmt = $this->conn->exec($query);
    }
    
    public function updateExpire($id,$isExpire){
        $query = "UPDATE cover_supplier SET is_expire = '$isExpire' WHERE cover_supplier.id = '$id'";
        $stmt = $this->conn->exec($query);
    }
    
    public function updateCoverExistence($coverId,$supplierId,$price){
        //if price is zero it means we must delete that from the table else we must insert to the table
        if($price == 0){
        $query = "DELETE FROM cover_supplier_model WHERE model_id = '$coverId' AND supplier_id ='$supplierId'";
        $stmt = $this->conn->exec($query); 
        }else{
      $q = "INSERT INTO cover_supplier_model (id, model_id, supplier_id, price, is_available) VALUES (NULL, '$coverId', '$supplierId', '$price', '1');";
        $s = $this->conn->exec($q);
        }

    }
    public function getAllCoverBasedOnSupplier($supplierId){
     
    $query = "SELECT cover_model.id, cover_model.model, cover_model.category, COALESCE(TB.supplier_id, 0) AS supplierId, COALESCE(TB.price,0) AS price FROM cover_model LEFT JOIN (SELECT id, supplier_id, model_id, price FROM cover_supplier_model WHERE supplier_id = 3 ) TB ON cover_model.id = TB.model_id GROUP BY cover_model.id ORDER BY supplier_id DESC,cover_model.model";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allCovers = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachCover = array(
                "id"=>$row["id"],
                "name"=>$row["model"],
                "category"=>$row['category'],
                "price"=>$row['price'],
                "isAvailable"=>$row["supplierId"]
            );
            
            array_push($allCovers,$eachCover);
        }
        return $allCovers;
      }

    }
    
    public function getImageBasedOnSupplier($supplierId){
        
        
      $query = "SELECT * FROM cover_image WHERE supplier_id='$supplierId'";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allImages = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachImages = array(
                "id"=>$row["id"],
                "url"=>$row["url"],
                "supplierId"=>$row["supplier_id"]
            );
            
            array_push($allImages,$eachImages);
        }
        return $allImages;
      }

    
    
    }
    
    public function getAllSupplierForUser(){
        
      $query = "SELECT 
    s.*,
    JSON_ARRAYAGG(si.url) AS imageList
FROM 
    cover_supplier s
LEFT JOIN 
    cover_image si ON s.id = si.supplier_id
GROUP BY 
    s.id, s.supplier_name";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allSuppliers = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

           $defaultImageItem = "default.jpg";
           $images = json_decode($imageList);
           //$images = $images?$images:[$defaultImageItem];
           
               // Filter out any null values from the imageList array
    $images = array_filter($images, function($value) {
        return !is_null($value);
    });
     
           
          // $images = ['cc','dd'];
           
            $eachSupplier = array(
                "id"=>$row["id"],
                "name"=>$row["supplier_name"],
                "address"=>$row["address"],
                "additionalInfo" =>$row["additional_info"],
                "imageList" => $images
            );
            
            array_push($allSuppliers,$eachSupplier);
        }
        //$allSuppliers["imageList"] = array_filter($allSuppliers["imageList"]);
        
        
        return $allSuppliers;
      }

    
    
    
        
    }
    public function getAllCover(){
        
      $query = "SELECT * FROM cover_model";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allCovers = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachCover = array(
                "id"=>$row["id"],
                "name"=>$row["model"]
            );
            
            array_push($allCovers,$eachCover);
        }
        return $allCovers;
      }

        
    }
    public function getAllCoverSupplier(){
        
      $query = "SELECT *,COALESCE((SELECT cover_image.url FROM cover_image WHERE cover_image.supplier_id = cover_supplier_model.supplier_id AND cover_image.is_comman =1 LIMIT 1),'no') AS sampleImage FROM cover_supplier_model";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();

      $allCoverSuppliers = array();
      if($num > 0){

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $eachCoverSupplier = array(
                "id"=>$row["id"],
                "coverId"=>$row["model_id"],
                "supplierId"=>$row["supplier_id"],
                "price"=>$row["price"],
                "sampleImage"=>$row["sampleImage"]
            );
            
            array_push($allCoverSuppliers,$eachCoverSupplier);
        }
        return $allCoverSuppliers;
      }

    
    
    
        
    } 
    
    
}

?>