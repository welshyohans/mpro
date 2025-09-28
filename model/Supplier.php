<?php

class Supplier{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }
  
     public function getNewUpdateCode() {
        $settings_query = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $newUpdateCode = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        return $newUpdateCode['value'];

    }
    
    public function getAllSupplier(){
        $query = "SELECT * FROM supplier";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allSupplier = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachSupplier = array(
                    "shopId"=>$shop_id,
					"shopName"=>$shop_name,
					"shopDetail"=>$shop_detail,
					"shopType"=>$shop_type
                );
                array_push($allSupplier,$eachSupplier);
            }
        }
        return $allSupplier;
    }
	
public function getSupplier($goodsId){
    $query = "SELECT supplier.shop_detail FROM goods LEFT JOIN supplier ON goods.shop_id = supplier.shop_id WHERE goods.id = '$goodsId'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$result = array(
			"response"=>$row['shop_detail']
			);
		return $result;
    }
    
    public function checkSupplier($phone,$password){
        
        $query = "SELECT supplier.shop_name,supplier.shop_id,supplier.password FROM supplier WHERE supplier.phone = '$phone' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();
        $n = "";
        $id = 0;
			
		if($num>0){
		   	$row = $stmt->fetch(PDO::FETCH_ASSOC);
		   	if($row['password']==$password){
		   	    $n = $row['shop_name'];
		   	    $id = $row['shop_id'];
		   	}
		}
	

		$result = array(
			"userId"=>$id,
			"userName"=>$n,
			"password"=>"password",
			"phone"=>"phone"
			);

			return $result;
    
    }
    
  public function insertSupplier($name){
        $query = "INSERT INTO supplier (shop_id, shop_name, shop_detail, shop_type) VALUES (NULL, '$name', 'more info', 'accessory')";
        $stmt = $this->conn->exec($query);
    }
    
 public function getAllMyShopGoods($supplierId){
     
     
        $query = "SELECT goods.id,goods.model,goods.image_url,goods.description,goods.category_id,category.parent_id,COALESCE(supplier_goods.price,0) AS price,COALESCE(supplier_goods.discount_start,0) AS discount_start,COALESCE(supplier_goods.discount_price,0) AS discount_price,supplier_goods.is_credit,supplier_goods.min_order FROM supplier_goods LEFT JOIN goods  ON supplier_goods.goods_id = goods.id LEFT JOIN category ON goods.category_id = category.id WHERE goods.last_delete_code =0 AND supplier_goods.supplier_id =$supplierId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $cId = $category_id;
                if($parent_id !=0){
                    $cId = $parent_id;
                }
                $eachGoods = array(
                    "goodsId"=>$id,
					"name"=>$model,
					"image"=>$image_url,
					"description"=>$description,
					"price"=>$price,
					"discountStart"=>$discount_start,
					"discountPrice"=>$discount_price,
					"categoryId"=>$cId,
					"minOrder"=>$min_order,
					"isCredit"=>$is_credit
                );
                array_push($allGoods,$eachGoods);
            }
        }
        return $allGoods;
    
 
 }    
 
 public function getAllGoods($supplierId){
     //get all goods to the supplier to see and select to add to there shop  list we don't include cover in this part and make battery and glass change there category Id to there parent category Id
     
        $query = "SELECT goods.id,goods.model,goods.image_url,goods.description,goods.category_id,category.parent_id,COALESCE(supplier_goods.price,0) AS price,COALESCE(supplier_goods.discount_start,0) AS discount_start,COALESCE(supplier_goods.discount_price,0) AS discount_price,COALESCE(supplier_goods.is_credit,0),COALESCE(supplier_goods.min_order,0) FROM goods LEFT JOIN supplier_goods ON supplier_goods.goods_id = goods.id AND supplier_goods.supplier_id ='$supplierId' LEFT JOIN category ON goods.category_id = category.id WHERE goods.last_delete_code =0 ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $cId = $category_id;
                if($parent_id !=0){
                    $cId = $parent_id;
                }
                $eachGoods = array(
                    "goodsId"=>$id,
					"name"=>$model,
					"image"=>$image_url,
					"description"=>$description,
					"price"=>$price,
					"discountStart"=>$discount_start,
					"discountPrice"=>$discount_price,
					"categoryId"=>$cId,
					"minOrder"=>$min_order,
					"isCredit"=>$is_credit
                );
                array_push($allGoods,$eachGoods);
            }
        }
        return $allGoods;
    
 }
 
 public function deleteGoods($goodsId,$supplierId){
     //this deletes from there own and change shopId in the goods table if another goods is available but it make it off or unavailable if there is not another shop available to supply such goods.
     
         $query = "DELETE FROM supplier_goods WHERE supplier_goods.goods_id ='$goodsId' AND supplier_goods.supplier_id ='$supplierId'";
        $stmt = $this->conn->exec($query);
        
        //1.first check is the supplier holds the price if not nothing happened
        //2. if it holds the price. fetch all the supplier that have this goods. if no supplier exit to supply this goods it will change new_update_code from setting and make it unavailable the goods from the goods table
        //3.if there is supplier for that goods it will change shopId of the goods by comparing the price of the supplier and update new_update_code in setting and update lastPriceUpdate in goods table.
        
        $goods_query = "SELECT goods.pure_price,goods.shop_id,goods.available_in_market FROM goods WHERE goods.id = '$goodsId'";
        $goods_stmt = $this->conn->prepare($goods_query); 
        $goods_stmt->execute();
        $goods = $goods_stmt->fetch(PDO::FETCH_ASSOC);

        $oldShopId = $goods['shop_id'];
        
        if($oldShopId == $supplierId){
           $this->checkBestPrice($goodsId);
        }else{
            //echo 'no effect';
        }
        
 }
 
 public function addGoods($goodsId,$supplierId,$price,$discountStart,$discountPrice,$isUpdate){
     //first of all i will get the value of the goods isAvailable,shopId,and price of the shop it holds the goods.
     
             $goods_query = "SELECT goods.pure_price,goods.shop_id,goods.available_in_market FROM goods WHERE goods.id = '$goodsId'";
        $goods_stmt = $this->conn->prepare($goods_query); 
        $goods_stmt->execute();
        $goods = $goods_stmt->fetch(PDO::FETCH_ASSOC);

        $oldIsAvailable = $goods['available_in_market'];
        $oldShopId = $goods['shop_id'];
        $oldPrice = $goods['pure_price'];
        
       /* echo "is available:".$oldIsAvailable. " shopId:".$oldShopId." price:".$oldPrice;*/
        

        
        
        
     //based of this info we decide what happend to that goods...
     //if the goods is unavailable it updates its availablity and the price of the shop owner
     //if the goods is available 
     if($isUpdate == "update"){
         //in this case it updates the goods from supplier_goods
        $query = "UPDATE supplier_goods SET supplier_goods.price = '$price',supplier_goods.discount_start ='$discountStart',supplier_goods.discount_price='$discountPrice' WHERE supplier_goods.goods_id = '$goodsId' AND supplier_goods.supplier_id = '$supplierId'";
        $stmt = $this->conn->exec($query);
     }else{
         //in this case it is new and i add the goods to the supplier_goods
        $query1 = "INSERT INTO supplier_goods (id, supplier_id, goods_id, price, discount_start, discount_price) VALUES (NULL, '$supplierId', '$goodsId', '$price', '$discountStart', '$discountPrice')";
        $stmt1 = $this->conn->exec($query1);
     }
     
        if($oldIsAvailable==0){
            //in this case no matter what update availablity and price
            $this->updatePrice($goodsId,$price,$supplierId);
            $this->updateAvailablity($goodsId,1);
            //echo 'unavailable so it must be update price and availablity';
        }else{
            if($oldPrice >$price || $oldShopId==$supplierId ){
                //check the best price and update accoudinly 
                $this->checkBestPrice($goodsId);
                //echo 'compare and update price';
            }else{
                //do nothing
               // echo 'do nothing';
            }
        }
 }
 
 public function checkBestPrice($goodsId){
     
     //get all goods to the supplier to see and select to add to there shop  list we don't include cover in this part and make battery and glass change there category Id to there parent category Id
     
        $query = "SELECT supplier_goods.goods_id,supplier_goods.supplier_id,supplier_goods.price FROM supplier_goods WHERE supplier_goods.goods_id = '$goodsId'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        
        $lowerPrice = 100000;
        $bestSupplier = 0;
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                
                extract($row);

                if($lowerPrice>$price){
                    $lowerPrice = $price;
                    $bestSupplier = $supplier_id;
                }
            }
           $this->updatePrice($goodsId,$lowerPrice,$bestSupplier);
        }else{
            $this->updateAvailablity($goodsId,0);
        }
     
    
 
 }
 

 
    public function updateAvailablity($goodsId,$isAvailable){
                //first get lastUpdateCode
         $lastUpdateCode= $this->getNewUpdateCode() +1;
        
        
        //update lastUpdateCode
        $query1 = "UPDATE settings SET value = '$lastUpdateCode' WHERE settings.setting_key = 'new_update_code'";
        $stmt1 = $this->conn->exec($query1);
        
                //updatePrice in goods database and its lastAvailablecode
        $query2 = "UPDATE goods SET available_in_market = '$isAvailable',last_update_availability= '$lastUpdateCode' WHERE goods.id ='$goodsId'";
        $stmt2 = $this->conn->exec($query2);
    }
    
    
     public function updatePrice($goodsId,$price,$shopId){
        //first get lastUpdateCode
         $lastUpdateCode= $this->getNewUpdateCode() +1;
        
        
        //update lastUpdateCode
        $query1 = "UPDATE settings SET value = '$lastUpdateCode' WHERE settings.setting_key = 'new_update_code'";
        $stmt1 = $this->conn->exec($query1);
        
        //updatePrice in goods database and its lastAvailablecode
        $query2 = "UPDATE goods SET pure_price = '$price',last_update_price= '$lastUpdateCode',shop_id = '$shopId' WHERE goods.id ='$goodsId'";
        $stmt2 = $this->conn->exec($query2);
        
        

        
    }
    
    // Add these properties to your class
/*public $shop_id;
public $shop_name;
public $shop_detail;
public $isVisible;*/

// Add this function to get all suppliers
public function getAllSuppliers(){
    $query = "SELECT shop_id, shop_name, shop_detail, image, isVisible FROM supplier ORDER BY priority DESC, shop_name ASC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $suppliers_arr = array();
    if($stmt->rowCount() > 0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $supplier_item = array(
                "shopId" => $shop_id,
                "shopName" => $shop_name,
                "shopDetail" => $shop_detail,
                "imageUrl" => $image, // Make sure your image column has the base URL or just the path
                "isVisible" => (bool)$isVisible // Cast to boolean for proper JSON
            );
            array_push($suppliers_arr, $supplier_item);
        }
    }
    return $suppliers_arr; 
}


// Add this function to update a supplier
public function updateSupplier($shopId,$shopName,$shopDetail,$isVisible){ 
    // Use prepared statements to prevent SQL injection
    $query = "UPDATE supplier SET shop_name = '$shopName', shop_detail = '$shopDetail', isVisible = '$isVisible' WHERE shop_id = '$shopId'";

    $stmt2 = $this->conn->exec($query);
    
}

//latter it will be based on customerId
public function getAllCredits($customerId){
    // This query joins the credit table with a customers table to get the name.
    // Make sure you have a 'customers' table with 'id' and 'name' fields.
    $query = "SELECT 
                cr.id, 
                cr.due_date, 
                cr.total, 
                cr.paid
              FROM 
                credit cr WHERE cr.customer_id = '$customerId'
              ORDER BY 
                cr.due_date DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $credits_arr = array();
    if($stmt->rowCount() > 0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $credit_item = array(
                "creditId" => $id,
                "dueDate" => $due_date, // e.g., "2023-10-27"
                "totalAmount" => (float)$total,
                "paidAmount" => (float)$paid
            );
            array_push($credits_arr, $credit_item);
        }
    }
    return $credits_arr;
}

}