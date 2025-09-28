<?php

class Goods{

    private $conn;

    public function __construct($conn){

        $this->conn = $conn;
    }

    public function getAllGoods(){
        $query = "SELECT * FROM goods";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num>0){
            $allGoods = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
]                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"], 
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $row["color_status"],
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["pure_price"],
                    "image_url" => $row["image_url"],
                    "seo" =>$row["seo"],
                    "serverPriority" => $row["priority"]
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return "some thing error happened";
        }
    }

}









?>