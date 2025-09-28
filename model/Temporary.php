<?php 

class Temporary{

    
    private $conn;
    private $newUpdateCode;
    public function __construct($conn,$newUpdateCode){

        $this->conn = $conn;
        $this->newUpdateCode = $newUpdateCode;
    }

	public function getLastId(){
		$query = "SELECT id FROM goods ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $id =  $result['id'];
        return $id;
	}
    public function insertUpdatedGoodsFromTemporary(){

        // when we insert a goods to the normal goods table  from temporary goods table
        // we decide to insert or to update based on the isUpdate column

        $query1 = "SELECT * FROM temp_goods_update";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                if($is_update == 1){
                    $query2 = "UPDATE goods SET category_id = '$category_id', brand_id = '$brand_id', model = '$model', description = '$description', seo = '$seo', pure_price = '$pure_price', min_order = '$min_order', max_order = '$max_order', priority = '$priority', show_in_home = '$show_in_home', image_url = '$image_url', discount_start = '$discount_start', last_update_code = '$this->newUpdateCode', available_in_market = '$available_in_market',shop_id = '$shop_id' WHERE goods.id = '$goods_id'";
                    $stmt2 = $this->conn->exec($query2);
                }else{
                    $query4 = "INSERT INTO goods (id, category_id, brand_id, model, description, seo, pure_price, min_order, max_order, priority, show_in_home, image_url, discount_start, last_update_price, last_update_colour, last_update_availability, last_update_code, available_in_market, color_status, last_update,shop_id) VALUES (NULL, '$category_id', '$brand_id', '$model', '$description', '$seo', '$pure_price', '$min_order', '$max_order', '$priority', '$show_in_home', '$image_url', '$discount_start', '$this->newUpdateCode', '$this->newUpdateCode', '$this->newUpdateCode', '$this->newUpdateCode', '$available_in_market', '$color_status', current_timestamp(),'$shop_id')";
                    $stmt4 = $this->conn->exec($query4);
                }

            }
            $query3 = "DELETE FROM temp_goods_update";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedCategoryFromTemporary(){
        //first get data from temporary table and inset(update) to normal table
        //then delete data from temporary table

        $query1 = "SELECT * FROM temp_category_update";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                if($is_update == 1){
                    $query2 = "UPDATE category SET parent_id = '$parent_id', to_sub_category = '$to_sub_category', name = '$name', image_url = '$image_url', is_available = '$is_available', last_update_code = '$this->newUpdateCode' WHERE category.id = '$category_id'";
                    $stmt2 = $this->conn->exec($query2);
                }else{
                    $query4 = "INSERT INTO category (id, parent_id, to_sub_category, name, image_url, is_available, last_update_availability, last_update_code) VALUES (NULL, '$parent_id', '$to_sub_category', '$name', '$image_url', '$is_available', '$this->newUpdateCode', '$this->newUpdateCode')";
                    $stmt4 = $this->conn->exec($query4);
                }
            }
            $query3 = "DELETE FROM temp_category_update";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedPriceFromTemporary(){
        $query1 = "SELECT * FROM temp_goods_price";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $query2 = "UPDATE goods SET pure_price = '$new_price',last_update_price= '$this->newUpdateCode' WHERE goods.id = '$goods_id'";
                $stmt2 = $this->conn->exec($query2);
            }
            $query3 = "DELETE FROM temp_goods_price";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedColorFromTemporary(){

        //first we delete all colored goods form goods_color table then add what we want
        $query1 = "SELECT * FROM temp_goods_color";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            //first delete all the goods_id from goods_color table that we need to update
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $query2 = "DELETE FROM goods_color WHERE goods_id = '$goods_id'";
                $stmt2 = $this->conn->exec($query2);

                //second we update last_update_color in goods table to announce we make change to that goods
                $query4 = "UPDATE goods SET last_update_colour= '$this->newUpdateCode' WHERE goods.id = '$goods_id'";
                $stmt4 = $this->conn->exec($query4);
                
            }

            //then we insert goods_id and color_id to goods_color table... in this case we don't update b/ce we delete them
            $query6 = "SELECT * FROM temp_goods_color";
            $stmt6 = $this->conn->Prepare($query6);
            $stmt6->execute();
            while($row = $stmt6->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                //we insert only if we give them color but if it doesn't color we don't need to insert it...
                if($color_id != 1){
                $query5 = "INSERT INTO goods_color (color_id, goods_id, quantity) VALUES ('$color_id', '$goods_id', '$quantity')";
                $stmt5 = $this->conn->exec($query5);
                }
            }
            //finally we delte record from temporary goods_color table
            $query3 = "DELETE FROM temp_goods_color";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedGoodsAvailabilityFromTemporary(){

        $query1 = "SELECT * FROM temp_goods_availablity";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $query2 = "UPDATE goods SET available_in_market = '$is_available',last_update_availability= '$this->newUpdateCode' WHERE goods.id = '$goods_id'";
                $stmt2 = $this->conn->exec($query2);
            }
            $query3 = "DELETE FROM temp_goods_availablity";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedCategoryAvailabilityFromTemporary(){

        $query1 = "SELECT * FROM temp_category_availablity";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $query2 = "UPDATE category SET is_available = '$is_available',last_update_availability= '$this->newUpdateCode' WHERE category.id = '$category_id'";
                $stmt2 = $this->conn->exec($query2);
            }
            $query3 = "DELETE FROM temp_category_availablity";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedPriorityFromTemporary(){
        //we don't need last_update_code to check the priority 
        //first i update all the priority to zero then update based on temp_priority
        //i the last we don't delete the temp_priority 

        $query3 = "UPDATE goods SET priority = 0";
        $stmt3 = $this->conn->exec($query3);

        $query1 = "SELECT * FROM temp_goods_priority";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $query2 = "UPDATE goods SET priority = '$priority_value' WHERE goods.id = '$goods_id'";
                $stmt2 = $this->conn->exec($query2);
            }
        }
    }

    public function insertUpdatedCodeGiverFromTemporary(){
        $query1 = "SELECT * FROM temp_code_giver";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                if($is_update == 1){
                    $query2 = "UPDATE code_giver SET code_value = '$code_value',last_update_code= '$this->newUpdateCode' WHERE code = '$code'";
                    $stmt2 = $this->conn->exec($query2);
                }else{

                    $query4 = "INSERT INTO code_giver (code, code_value, last_update_code) VALUES ('$code', '$code_value', '$this->newUpdateCode')";
                    $stmt24 = $this->conn->exec($query4);
                }

            }
            $query3 = "DELETE FROM temp_code_giver";
            $stmt3 = $this->conn->exec($query3);
        }
    }

    public function insertUpdatedCommissionsFromTemporary(){
        $query1 = "SELECT * FROM temp_goods_commission";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $query2 = "INSERT INTO commussion (id, goods_id, address_id, commusion_value, discount_commusion, last_update_code) VALUES (NULL, '$goods_id', '$address_id', '$commission_value', '$discount_commission', '$this->newUpdateCode') ON DUPLICATE KEY UPDATE commusion_value = VALUES(commusion_value), discount_commusion = VALUES(discount_commusion),last_update_code = VALUES(last_update_code)";
                $stmt2 = $this->conn->exec($query2);
            }
            $query3 = "DELETE FROM temp_goods_commission";
            $stmt3 = $this->conn->exec($query3);
        }
    }















// this is to view the temporary tables .....

    public function getTemporaryUpdatedGoods(){

        //i will change this to insertTemporaryGoods
    }

    public function getTemporaryUpdatedCategories(){
    }

    public function getTemporaryUpdatedPrice(){

    }

    public function getTemporaryUpdatedColor(){
    }

    public function getTemporaryUpdatedGoodsAvailability(){

    }
    public function getTemporaryUpdatedCategoriesAvailability(){
    }

    public function getTemporaryUpdatedPriority(){

    }
    public function getTemporaryUpdatedCommission(){

    }

    public function getTemporaryUpdatedCodeGiver(){
        
    }

    


    //from this also we use those functions to update .... this means we insert them to temporary tables

    public function insertGoodsToTemporary($goodsId,$categoryId,$brandId,$model,$descrption,$seo,$purePrice,$minOrder,$maxOrder,$priority,$showInHome,$imageUrl,$discountStart,$availableInMarket,$colorStatus,$isUpdate,$shopId){

 $lastUpdateCode= $this->getNewUpdateCode() +1;
        //if the goods is new we also insert its commission and its color if it has
          $query4 = "INSERT INTO goods (id, category_id, brand_id, model, description, seo, pure_price, min_order, max_order, priority, show_in_home, image_url, discount_start, last_update_price, last_update_colour, last_update_availability, last_update_code, available_in_market, color_status, last_update,shop_id) VALUES (NULL, '$categoryId', '$brandId', '$model', '$descrption', '$seo', '$purePrice', '$minOrder', '$maxOrder', '$priority', '$showInHome', '$imageUrl', '$discountStart', '0', '0', '0', '$lastUpdateCode', '$availableInMarket', '$colorStatus', current_timestamp(),'$shopId')";
        $stmt4 = $this->conn->exec($query4);
        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }
    
    public function insertToSupplierGoods($goodsId,$shopId,$purePrice){
                     //in this case it is new and i add the goods to the supplier_goods
        $query1 = "INSERT INTO supplier_goods (id, supplier_id, goods_id, price, discount_start, discount_price) VALUES (NULL, '$shopId', '$goodsId', '$purePrice', '0', '0')";
        $stmt1 = $this->conn->exec($query1);
    }

    public function insertCategoryToTemporary($categoryId,$parentId,$toSubCategory,$name,$imageUrl,$isAvailable,$isUpdate){
        $query = "INSERT INTO temp_category_update (category_id, parent_id, to_sub_category, name, image_url, is_available, is_update) VALUES ('$categoryId', '$parentId', '$toSubCategory', '$name', '$imageUrl', '$isAvailable', '$isUpdate')";
        $stmt = $this->conn->exec($query);
    }

    public function insertPriceToTemporary($goodsId,$purePrice){
        $query = "INSERT INTO temp_goods_price (goods_id,new_price) VALUES ('$goodsId', '$purePrice')";
        $stmt = $this->conn->exec($query);
    }

    public function insertColorToTemporary($goodsId,$colorId,$quantity){
        $query = "INSERT INTO temp_goods_color (id, goods_id, color_id, quantity) VALUES (NULL, '$goodsId', '$colorId', '$quantity')";
        $stmt = $this->conn->exec($query);
    }

    public function insertGoodsAvailablityToTemporary($goodsId,$isAvailable){

        $query = "INSERT INTO temp_goods_availablity (goods_id, is_available) VALUES ('$goodsId', '$isAvailable')";
        $stmt = $this->conn->exec($query);
    }
    public function insertCategoryAvailablityToTemporary($categoryId,$isAvailable){
        $query = "INSERT INTO temp_category_availablity (category_id, is_available) VALUES ('$categoryId', '$isAvailable')";
        $stmt = $this->conn->exec($query);
    }

    public function insertPriorityToTemporary($goodsId,$priorityValue){

		$query1 = "SELECT * FROM temp_goods_priority WHERE goods_id='$goodsId'";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
			//update
	    $query = "UPDATE temp_goods_priority SET priority_value='$priorityValue' WHERE goods_id='$goodsId'";
        $stmt = $this->conn->exec($query);
		}else{
			//insert
	    $query = "INSERT INTO temp_goods_priority (goods_id, priority_value) VALUES ('$goodsId', '$priorityValue')";
        $stmt = $this->conn->exec($query);
		}

    }
    public function insertCommustionToTemporary($goodsId,$discountStarts,$AOC,$AODC,$ATC,$ATDC){
       $query = "INSERT INTO temp_goods_commission (goods_id, address_id, commission_value, discount_commission) VALUES ('$goodsId', 1, '$AOC', '$AODC')";
       $stmt = $this->conn->exec($query);
		
$q = "INSERT INTO temp_goods_commission (goods_id, address_id, commission_value, discount_commission) VALUES ('$goodsId', 2, '$ATC', '$ATDC')";
       $s = $this->conn->exec($q);
		
$qa = "UPDATE goods SET discount_start = '$discountStarts' WHERE goods.id = '$goodsId'";
       $sa = $this->conn->exec($qa);
		
		
		
    }
    
    public function insertCoverImage($url,$supplierId){
        $query = "INSERT INTO cover_image (id, url, supplier_id) VALUES (NULL, '$url', '$supplierId')";
        $stmt = $this->conn->exec($query);
    }
  public function updateCoverImage($id,$url,$supplierId){
        $query = "UPDATE cover_image SET url = '$url' WHERE cover_image.id = '$id'";
        $stmt = $this->conn->exec($query);
    }
    
    public function insertCodeGiVerToTemporary(){

    }


    //from this we write functions that delete items from temporary tables



    public function deleteTemporaryGoods(){
        //if the goods is new we also delete the related color and commissions
    }

    public function deleteTemporaryCategory(){
    }

    public function deleteTemporaryPrice(){

    }

    public function deleteTemporaryColor(){
    }

    public function deleteTemporaryGoodsAvailablity(){

    }
    public function deleteTemporaryCategoryAvailablity(){
    }

    public function deleteTemporaryPriority(){

    }
    public function deleteTemporaryCommission(){

    }
    public function deleteTemporaryCodeGiver(){

    }
    
    
        public function updateFinishedGoods(){

        $query1 = "SELECT id FROM goods WHERE isFinishable =1 AND store <1 AND available_in_market =1";
        $stmt1 = $this->conn->Prepare($query1);
        $stmt1->execute();
        $num = $stmt1->rowCount();
        if($num>0){
            while($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $this->insertGoodsAvailablityToTemporary($id,0);
            }
        }
    }
    
    public function cancelGoodsFromOrder($goodsId){
        
                //first get lastUpdateCode
         $lastUpdateCode= $this->getNewUpdateCode() +1;
        
        
        //update lastUpdateCode
        $query1 = "UPDATE settings SET value = '$lastUpdateCode' WHERE settings.setting_key = 'new_update_code'";
        $stmt1 = $this->conn->exec($query1);
        
                //updatePrice in goods database and its lastAvailablecode
        $query2 = "UPDATE goods SET available_in_market = '$isAvailable',last_update_availability= '$lastUpdateCode',store=0 WHERE goods.id ='$goodsId'";
        $stmt2 = $this->conn->exec($query2);
        
         $query3 = "UPDATE ordered_list SET ordered_list.is_cancelled=1 WHERE ordered_list.goods_id ='$goodsId' AND ordered_list.each_prepare = 0";
        $stmt3 = $this->conn->exec($query3);
    
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
    public function updatePrice($goodsId,$purePrice,$previousPrice,$store){
        //first get lastUpdateCode
         $lastUpdateCode= $this->getNewUpdateCode() +1;
        
        
        //update lastUpdateCode
        $query1 = "UPDATE settings SET value = '$lastUpdateCode' WHERE settings.setting_key = 'new_update_code'";
        $stmt1 = $this->conn->exec($query1);
        
        //updatePrice in goods database and its lastAvailablecode
        $query2 = "UPDATE goods SET pure_price = '$purePrice',last_update_price= '$lastUpdateCode' WHERE goods.id ='$goodsId'";
        $stmt2 = $this->conn->exec($query2);
        
        
        //get the goods quantity ordered but not still delivered
        $quantity = $this->getOrderedQuantity($goodsId);
       // echo $quantity;
        
        $totalQuantity = 0;
        //get quantity store + quantity of ordered goods

             $totalQuantity = $quantity+$store;
        
        //calculate gain ((current price - previousPrice *quanity))
        $gain=($purePrice-$previousPrice)*$totalQuantity;
        //echo $gain;
        //update purchase gain
        if($totalQuantity>0){
            $q1 = "INSERT INTO purchase_gain (id, goods_id, gain, reason, date) VALUES (NULL, '$goodsId', '$gain', 'change price', current_timestamp())";
             $s1 = $this->conn->exec($q1);   
        }

        
    }
    
        public function getNewUpdateCode() {
        $settings_query = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $newUpdateCode = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        return $newUpdateCode['value'];

    }
    
    public function getOrderedQuantity($goodsId){
        
        $query = "SELECT COALESCE(SUM(ordered_list.quantity),0)AS q FROM ordered_list LEFT JOIN orders ON orders.id=ordered_list.orders_id WHERE ordered_list.goods_id='$goodsId' AND orders.deliver_status<6";
        $stmt = $this->conn->prepare($query); 
        $stmt->execute();
        $quantity = $stmt->fetch(PDO::FETCH_ASSOC);

        return $quantity['q'];
    }


}








?>