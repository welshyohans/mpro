	 
	 <?php

class SpecialOffer {

    public $conn;
    public function __construct($conn){
        $this->conn = $conn;

    }
	 
	 
   public function getAllGoodsFromSpecial(){
        $query = "SELECT goods.*,special_offer.max_order AS new_max,special_offer.price AS new_price FROM special_offer LEFT JOIN goods ON special_offer.goods_id=goods.id WHERE special_offer.is_expired =0";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"], 
                    "maxOrder" => $row["new_max"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" =>"0",
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["new_price"],
                    "discountValue" => 0,
                    "image_url" => $row["image_url"],
                    "localPriority" => 0,
                    "seo" =>$row["seo"],
                    "serverPriority" => $row["priority"],
					"categoryTwo" => $row["category_two"],
                    "isAdded" => 0
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return $allGoods;
        }
    }
}

?>