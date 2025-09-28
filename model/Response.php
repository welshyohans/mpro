<?php

class Response {

    public $conn;
    public function __construct($conn){
        $this->conn = $conn;

    }



	//this address id is for commission
    public function getAddressId($userId) {

        $query = "SELECT address.commission_address FROM customer LEFT JOIN address ON customer.address_id = address.id LEFT JOIN user ON user.customer_id = customer.id WHERE user.id ='$userId' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $addressId =  $result['commission_address'];
        return $addressId;
    }
	public function getColorImage($colorId,$goodsId){
		$query = "SELECT image_url FROM goods_color WHERE color_id = '$colorId' AND goods_id = '$goodsId' LIMIT 1";
	    $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $imageUrl =  $result['image_url'];
        return $imageUrl;
	}
    public function getAllSettings($userId,$addressId,$settings) {

        $deliver_query = "SELECT deliver_time.when_to_deliver FROM deliver_time LEFT JOIN customer ON customer.address_id = deliver_time.address_id LEFT JOIN user ON user.customer_id = customer.id WHERE user.id ='$userId' LIMIT 1";
        $deliver_stmt = $this->conn->prepare($deliver_query);
        $deliver_stmt->execute();
        $deliver_result = $deliver_stmt->fetch(PDO::FETCH_ASSOC);

        $deliverTime =  $deliver_result['when_to_deliver'];

        $settings->deliveryTime= $deliverTime;

        $settings_query = "SELECT * from settings WHERE setting_key = 'expire_time'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $expire_result = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        $expireTime =$expire_result['value'];

        $settings->expireTime= $expireTime;

        $settings_query1 = "SELECT * from settings WHERE setting_key = 'apk_update'";
        $settings_stmt1 = $this->conn->prepare($settings_query1); 
        $settings_stmt1->execute();
        $apk_result = $settings_stmt1->fetch(PDO::FETCH_ASSOC);

        $apkUpdate = $apk_result['value'];
        $settings->appUpdateInfo=$apkUpdate;

        $settings_query2 = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt2 = $this->conn->prepare($settings_query2); 
        $settings_stmt2->execute();
        $newCode_result = $settings_stmt2->fetch(PDO::FETCH_ASSOC);

        //TODO: latter i will change this to its normal value
        $lastUpdateCode = $newCode_result['value'];
        $settings->lastUpdateCode = $lastUpdateCode;
        //$settings->lastUpdateCode = 2;

        $query = "SELECT user.id,user.customer_id, customer.shop_name,customer.address_id,customer.specific_address,user.name,user.phone_number,user.profile_picture FROM user LEFT JOIN customer ON user.customer_id = customer.id WHERE user.id =".$userId." LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $settings->shopName = $result["shop_name"];
        $settings->userName= $result["name"];
        $settings->phoneNumber= $result["phone_number"];
        $settings->customerId= $result["customer_id"];
        $settings->userId= $result["id"];
        $settings->userImageUrl= $result["profile_picture"];
        $settings->address= $result["specific_address"];
        $settings->addressId= $result["address_id"];

        return $settings;
    }

	    public function getAllGoodsForAdmin(){
        $query = "SELECT * FROM goods WHERE seo != 'covers' ORDER BY model";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num>0){
            $allGoods = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"],
                    "seo" =>$row["seo"], 
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "isAvailable" => $row["available_in_market"],
                    "purePrice" => $row["pure_price"],
                    "imageUrl" => $row["image_url"],
                    "isUpdate" => 1
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return "some thing error happened";
        }
    }
    
    public function getAllGoodsForUpdate(){
        
        $query = "SELECT goods.id,goods.shop_id,supplier.shop_name, goods.model,goods.category_id,goods.available_in_market,goods.pure_price,goods.priority,goods.discount_start,goods.image_url,goods.store, COALESCE(c1.commusion_value,0) AS aOC,COALESCE(c1.discount_commusion,0) AS aODC,COALESCE(c2.commusion_value,0) AS aTC, COALESCE(c2.discount_commusion,0) AS aTDC FROM goods LEFT JOIN supplier ON goods.shop_id = supplier.shop_id LEFT JOIN commussion c1 ON c1.goods_id = goods.id AND c1.address_id =1 LEFT JOIN commussion c2 ON c2.goods_id = goods.id AND c2.address_id=2 WHERE goods.last_delete_code =0 ORDER BY goods.available_in_market DESC, goods.model";
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
                    "discountStarts" => $row["discount_start"],
                    "isAvailable" => $row["available_in_market"],
                    "purePrice" => $row["pure_price"],
                    "imageUrl" => $row["image_url"],
					"serverPriority"=> $row["priority"],
					"shopId"=>$row["shop_id"],
					"shopName"=>$row["shop_name"],
					"store"=>$row["store"],
					"aOC"=>$row["aOC"],
					"aODC"=>$row["aODC"],
					"aTC"=>$row["aTC"],
					"aTDC"=>$row["aTDC"]
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return $allGoods;
        }
    
    }
       
       public function getAllGoodsBasedOnCategory($categoryId){
        $query = "SELECT goods.id, goods.model,goods.category_id,goods.available_in_market,goods.pure_price,goods.priority,goods.discount_start,goods.image_url,goods.store, COALESCE(c1.commusion_value,0) AS aOC,COALESCE(c1.discount_commusion,0) AS aODC,COALESCE(c2.commusion_value,0) AS aTC, COALESCE(c2.discount_commusion,0) AS aTDC FROM goods LEFT JOIN commussion c1 ON c1.goods_id = goods.id AND c1.address_id =1 LEFT JOIN commussion c2 ON c2.goods_id = goods.id AND c2.address_id=2 WHERE category_id ='$categoryId' ORDER BY goods.available_in_market DESC, goods.model";
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
                    "discountStarts" => $row["discount_start"],
                    "isAvailable" => $row["available_in_market"],
                    "purePrice" => $row["pure_price"],
                    "imageUrl" => $row["image_url"],
					"serverPriority"=> $row["priority"],
					"store"=>$row["store"],
					"aOC"=>$row["aOC"],
					"aODC"=>$row["aODC"],
					"aTC"=>$row["aTC"],
					"aTDC"=>$row["aTDC"]
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return $allGoods;
        }
    }
	
    public function getAllGoods($addressId) {

        $query = "SELECT goods.*,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':addressId',$addressId);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num>0){
            $allGoods = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"], 
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $this->getColor($row["id"]),
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["goods_price"],
                    "discountValue" => $row["discount_value"],
                    "image_url" => $row["image_url"],
                    "localPriority" => 0,
                    "seo" =>$row["seo"],
                    "serverPriority" => $row["priority"],
                    "isAdded" => 0
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return "some thing error happened";
        }

    }
    
        public function getAllGoods4($addressId) {

        $query = "SELECT goods.*,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':addressId',$addressId);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num>0){
            $allGoods = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"], 
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $this->getColor($row["id"]),
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["goods_price"],
                    "discountValue" => $row["discount_value"],
                    "image_url" => $row["image_url"],
                    "localPriority" => 0,
                    "seo" =>$row["seo"],
                    "parentId"=>8,
                    "serverPriority" => $row["priority"],
                    "isAdded" => 0
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return "some thing error happened";
        }

    }
	
	    public function getAllGoods3($addressId) {

        $query = "SELECT goods.*,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId WHERE goods.last_delete_code=0";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':addressId',$addressId);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num>0){
            $allGoods = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"], 
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $this->getColor($row["id"]),
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["goods_price"],
                    "discountValue" => $row["discount_value"],
                    "image_url" => $row["image_url"],
                    "localPriority" => rand(1, 100),
                    "seo" =>$row["seo"],
                    "serverPriority" => $row["priority"],
					"categoryTwo" => $row["category_two"],
                    "isAdded" => 0
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return "some thing error happened";
        }

    }
    
    	    public function getAllGoods6($addressId) {

        $query = "SELECT goods.*,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId WHERE goods.last_delete_code=0 ORDER BY goods.last_update DESC";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':addressId',$addressId);
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
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $this->getColor($row["id"]),
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["goods_price"],
                    "discountValue" => $row["discount_value"],
                    "image_url" => $row["image_url"],
                    "localPriority" => rand(1, 100),
                    "seo" =>$row["seo"],
                    "shopId"=>4,
                    "star"=>4,
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
    
    public function getEachShopGoods($addressId,$shopId) {

        $query = "SELECT goods.*,supplier_goods.price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM supplier_goods LEFT JOIN goods ON goods.id = supplier_goods.goods_id LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId WHERE supplier_goods.supplier_id =:shopId AND goods.last_delete_code=0";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':addressId',$addressId);
        $stmt->bindParam(':shopId',$shopId);
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
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $this->getColor($row["id"]),
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["goods_price"],
                    "discountValue" => $row["discount_value"],
                    "image_url" => $row["image_url"],
                    "localPriority" => rand(1, 100),
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

    public function getColor($goodsId){

        $query = "SELECT * FROM goods_color WHERE goods_id=:goods_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':goods_id',$goodsId);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        $result ="";
        
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                extract($row);
                $result .= $color_id."i".$quantity."-";
            }

        }else{
            $result = "0";
        }
        return rtrim($result,'-');
    }
    //for now we just return all categories but for latter we will decide who is phone seller
    //who is computer maintenance ....
    public function getAllCategories() {
        $query = "SELECT * FROM category";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        if($num>0){
            $allCategory = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCategory = array(
                    "categoryId" =>$row["id"],
                    "parentId" =>$row["parent_id"],
                    "name" =>$row["name"],
                    "priority" =>$row["priority"],
                    "imageUrl" => $row["image_url"],
                    "isAvailable" =>$row["is_available"], 
                    "toSubCategory" =>$row["to_sub_category"],
                    "brand"=>$row["brand"],
                    "showInfo"=>$row["show_info"]

                );
                array_push($allCategory,$eachCategory);
            }
            return $allCategory;

        }else{
           return  "something error happen";
        }
       
    }
    
        //who is computer maintenance ....
    public function getAllCategories6() {
        $query = "SELECT * FROM category";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        if($num>0){
            $allCategory = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCategory = array(
                    "categoryId" =>$row["id"],
                    "parentId" =>$row["parent_id"],
                    "name" =>$row["name"],
                    "priority" =>$row["priority"],
                    "imageUrl" => $row["image_url"],
                    "isAvailable" =>$row["is_available"], 
                    "toSubCategory" =>$row["to_sub_category"],
                    "brand"=>$row["brand"],
                    "info"=>"This is All about the category information... if you have do it bro...",
                    "showInfo"=>$row["show_info"]

                );
                array_push($allCategory,$eachCategory);
            }
            return $allCategory;

        }else{
           return  "something error happen";
        }
       
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
					"phone"=>$phone,
					"image"=>$image,
					"info"=>"this is info of supplier...",
					"priority"=>$priority,
					"isVisible"=>$isVisible
                );
                array_push($allSupplier,$eachSupplier);
            }
        }
        return $allSupplier;
    }

    public function getAllCodeGiver(){
        $query = "SELECT * FROM code_giver";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        if($num>0){
            $allCodes = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCodes = array(
                    "code" =>$code,
                    "codeValue" =>$code_value

                );
                array_push($allCodes,$eachCodes);
            }
            return $allCodes;

        }else{
           return  "something error happen";
        }
    }

    public function getResponseFromHolderTable($lastUpdateCode,$userId){
        $addressId = $this->getAddressId($userId);
        $query = "SELECT * FROM update_holder WHERE last_update_code = '$lastUpdateCode' AND address_id = '$addressId'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['actual_data'];
    }
	    public function getSpecificGoods($goodsId){
       
        $query = "SELECT * FROM goods WHERE id = '$goodsId'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
           $eachGoods = array(
                    "goodsId" => $row["id"],
                    "categoryId" => $row["category_id"], 	
                    "model" => $row["model"],
                    "description" => $row["description"], 
			        "seo" =>$row["seo"],
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "isAvailable" => $row["available_in_market"],
                    "purePrice" => $row["pure_price"],
                    "imageUrl" => $row["image_url"],
                    "serverPriority" => $row["priority"],
			        "shopId"=>$row["shop_id"],
                    "isUpdate" => 0
                );
        return $eachGoods;
    }
    
    	    public function webGetAllGoods($addressId) {

        $query = "SELECT goods.id,goods.category_id,model,goods.image_url,goods.show_in_home,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId WHERE goods.available_in_market =1 ORDER BY goods.priority DESC LIMIT 30";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':addressId',$addressId);
        $stmt->execute();
        
        $num = $stmt->rowCount();

        if($num>0){
            $allGoods = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //extract($row);  
                $eachGoods = array(
                    "id" => $row["id"],
                    "cid" => $row["category_id"], 	
                    "n" => $row["model"],
                    "sh" => $row["show_in_home"],
                    "p" => $row["goods_price"],
                    "iu" => $row["image_url"]
                );

                array_push($allGoods,$eachGoods);
            }
        
            return $allGoods;
        }else{

           return "some thing error happened";
        }

    }
    
        public function webGetAllCategories() {
        $query = "SELECT * FROM category WHERE is_available = 1 LIMIT 10";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        if($num>0){
            $allCategory = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCategory = array(
                    "id" =>$row["id"],
                    "pid" =>$row["parent_id"],
                    "n" =>$row["name"],
                    "iu" => $row["image_url"],
                    "sc" =>$row["to_sub_category"]

                );
                array_push($allCategory,$eachCategory);
            }
            return $allCategory;

        }else{
           return  "something error happen";
        }
       
    }


public function getUpdatedSetting($userId,$settings){
    

        $deliver_query = "SELECT deliver_time.when_to_deliver FROM deliver_time LEFT JOIN customer ON customer.address_id = deliver_time.address_id LEFT JOIN user ON user.customer_id = customer.id WHERE user.id ='$userId' LIMIT 1";
        $deliver_stmt = $this->conn->prepare($deliver_query);
        $deliver_stmt->execute();
        $deliver_result = $deliver_stmt->fetch(PDO::FETCH_ASSOC);

        $deliverTime =  $deliver_result['when_to_deliver'];

        $settings->deliveryTime= $deliverTime;

        $settings_query = "SELECT * from settings WHERE setting_key = 'expire_time'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $expire_result = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        $expireTime =$expire_result['value'];

        $settings->expireTime= $expireTime;

        $settings_query1 = "SELECT * from settings WHERE setting_key = 'apk_update'";
        $settings_stmt1 = $this->conn->prepare($settings_query1); 
        $settings_stmt1->execute();
        $apk_result = $settings_stmt1->fetch(PDO::FETCH_ASSOC);

        $apkUpdate = $apk_result['value'];
        $settings->appUpdateInfo=$apkUpdate;

        $settings_query2 = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt2 = $this->conn->prepare($settings_query2); 
        $settings_stmt2->execute();
        $newCode_result = $settings_stmt2->fetch(PDO::FETCH_ASSOC);

        //TODO: latter i will change this to its normal value
        $lastUpdateCode = $newCode_result['value'];
        $settings->lastUpdateCode = $lastUpdateCode; 
        $settings->phoneTime=0;
        
        return $settings;
}


 public function getUpdatedPrice($lastUpdateCode){

    
        $query = "SELECT goods.id,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id WHERE goods.last_update_price > '$lastUpdateCode'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        $allPrice = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachPrice = array(
                    "goodsId" =>$row["id"],
                    "price" =>$row["goods_price"]

                );
                array_push($allPrice,$eachPrice);
            }
        }
         return $allPrice;
    
}
public function getUpdatedGoods($lastUpdateCode,$addressId){

        $query = "SELECT goods.*,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id ='$addressId' WHERE goods.last_update_code > '$lastUpdateCode'";
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
                    "maxOrder" => $row["max_order"],
                    "minOrder" => $row["min_order"],
                    "discountStart" => $row["discount_start"],
                    "showInHome" => $row["show_in_home"],
                    "colourStatus" => $this->getColor($row["id"]),
                    "availableInMarket" => $row["available_in_market"],
                    "price" => $row["goods_price"],
                    "discountValue" => $row["discount_value"],
                    "image_url" => $row["image_url"],
                    "localPriority" => rand(1, 100),
                    "seo" =>$row["seo"],
                    "serverPriority" => $row["priority"],
					"categoryTwo" => $row["category_two"],
                    "isAdded" => 0
                );

                array_push($allGoods,$eachGoods);
            }
        
            
        }
        return $allGoods;

    }
public function getUpdatedCategory($lastUpdateCode){
        $query = "SELECT * FROM category WHERE last_update_code >'$lastUpdateCode'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount(); 
         $allCategory = array();
        if($num>0){
           
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCategory = array(
                    "categoryId" =>$row["id"],
                    "parentId" =>$row["parent_id"],
                    "name" =>$row["name"],
                    "priority" =>0,
                    "imageUrl" => $row["image_url"],
                    "isAvailable" =>$row["is_available"], 
                    "toSubCategory" =>$row["to_sub_category"],
                    "brand"=>$row["brand"],
                    "showInfo"=>$row["show_info"]

                );
                array_push($allCategory,$eachCategory);
            }
           

        }
         return $allCategory;
       
    }
function getUpdatedCategoryAvailable($lastUpdateCode){

        $query = "SELECT * FROM category WHERE last_update_availability > '$lastUpdateCode'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        $allCategory = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachCategory = array(
                    "id" =>$row["id"],
                    "isAvailable" =>$row["is_available"]

                );
                array_push($allCategory,$eachCategory);
            }
        }
         return $allCategory;
    
}
function getUpdatedGoodsAvailable($lastUpdateCode){

    
        $query = "SELECT id,available_in_market FROM goods WHERE last_update_availability > '$lastUpdateCode'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachGoods = array(
                    "id" =>$row["id"],
                    "isAvailable" =>$row["available_in_market"]

                );
                array_push($allGoods,$eachGoods);
            }
        }
         return $allGoods;
    
}
function getUpdatedPriority($lastUpdateCode){

    
        $query = "SELECT id,priority FROM goods WHERE last_update_priority > '$lastUpdateCode'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachGoods = array(
                    "goodsId" =>$row["id"],
                    "priority" =>$row["priority"]

                );
                array_push($allGoods,$eachGoods);
            }
        }
         return $allGoods;
    
}

public function deletedGoods($lastUpdateCode){
        
        $query = "SELECT id FROM goods WHERE last_delete_code  > '$lastUpdateCode'";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();  
        $allGoods = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                array_push($allGoods,$row['id']);
            }
        }
         return $allGoods;
    //return "hellow world";
}


}

?>