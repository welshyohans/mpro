<?php

class HOLDER {


    //TODO: latter i will return 0 or something to those not values
    //This Class not directly to the user this holder class used update data
    //based on the $lastUpdateCode , $address and $newUpdateCode
    private $conn;
   // private $lastUpdateCode,$newUpdateCode,$addressId;
     
    //This class loops until it fulls the update_holder table
    //when the new_update_code is changed in the settings table we insert empty data into update_holder to each address
    //the purposse of this class is to update the update_holder table


    public function __construct($db) {
        $this->conn = $db;
    }

     /*public function getNewUpdateCode() {
        $settings_query = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $newUpdateCode = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        return $newUpdateCode['value'];

    }*/

    public function insertDataToHolder($newUpdateCode){

        //first fetch data from holder table then based on thier addressId and lastUpdateCode we update them
        $query = "SELECT last_update_code, address_id FROM update_holder";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $num = $stmt->rowCount();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $actualData = $this->getUpdatedData($last_update_code,$newUpdateCode,$address_id);
                $dataSize = strlen($actualData);
                $query1 = "UPDATE update_holder SET actual_data= '$actualData', data_size= '$dataSize' WHERE address_id = '$address_id' AND last_update_code = '$last_update_code'";
                $stmt1 = $this->conn->exec($query1);
               //echo "last_update_code:".$last_update_code."address_id:".$address_id."\n";	
            }

        }

    }
    public function getUpdatedData($lastUpdateCode,$newUpdateCode,$addressId) {

        // in this case first we will check if the updateHoler lastUpdateCode is the same  as the Settings table newUpdateCode 
        if($newUpdateCode == $lastUpdateCode){
            return "updated";
        }else{
        $setting = $this->getSetting($lastUpdateCode,$newUpdateCode,$addressId);
        $updatePrice = $this->getUpdatedPrice($lastUpdateCode,$newUpdateCode,$addressId);
        $updatedColor = $this->getUpdatedColor($lastUpdateCode);
        $updatedGoods = $this->getUpdatedGoods($lastUpdateCode,$addressId);
        $updatedCategory = $this->getUpdatedCategory($lastUpdateCode);
        $updatedGoodsAvailability = $this->getUpdatedGoodsAvailability($lastUpdateCode);
        $updatedCategoryAvailability = $this->getUpdatedCategoryAvailability($lastUpdateCode);
        $higherPriority = $this->getHigherPriority();
        $mediumPriority = $this->getMediumPriority();
        $updateCode = $this->getUpdatedCode($lastUpdateCode);

        //for testing purposes i use \n 
        return $setting."|".$updatePrice."|".$updatedColor."|".$updatedGoods."|".$updatedCategory."|".$updatedGoodsAvailability."|".$updatedCategoryAvailability."|".$higherPriority."|".$mediumPriority."|".$updateCode;

       // return $setting."|\n".$updatePrice."|\n".$updatedColor."|\n".$updatedGoods."|\n".$updatedCategory."|\n".$updatedGoodsAvailability."|\n".$updatedCategoryAvailability."|\n".$higherPriority."|\n".$mediumPriority."|\n".$updateCode;
    }
    }

    //return the settings value in string mode
    public function getSetting($lastUpdateCode,$newUpdateCode,$addressId){

    
        $deliver_query = "SELECT * FROM deliver_time WHERE address_id = ".$addressId." LIMIT 1";
        $deliver_stmt = $this->conn->prepare($deliver_query);
        $deliver_stmt->execute();
        $deliver_result = $deliver_stmt->fetch(PDO::FETCH_ASSOC);

        $deliverTime =  $deliver_result['when_to_deliver'];

        $settings_query = "SELECT * from settings WHERE setting_key = 'expire_time'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $expire_result = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        $expireTime =$expire_result['value'];

        $settings_query1 = "SELECT * from settings WHERE setting_key = 'apk_update'";
        $settings_stmt1 = $this->conn->prepare($settings_query1); 
        $settings_stmt1->execute();
        $apk_result = $settings_stmt1->fetch(PDO::FETCH_ASSOC);

        $apkUpdate = $apk_result['value'];

        return $expireTime."&".$lastUpdateCode."&".$newUpdateCode."&".$deliverTime."&".$apkUpdate;

        //get all settings ...
        //b/ce this code sends to version one apk only we send whether they need update or not (politly or not)
    }


    //update price information
    public function getUpdatedPrice($lastUpdateCode,$newUpdateCode,$addressId){

        //in this case we fetch goods with last_update_price is greater than the updateHolersTable lastUpdteCode and
        //the last_update_price must be greater than the last_update_code of that goods b/ce if it is equal or less than it will be updated in the goodsUpdate Section 
        $query = "SELECT goods.id,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId WHERE (goods.last_update_price>:lastUpdateCode OR commussion.last_update_code >:lastUpdateCode) AND (goods.last_update_price > goods.last_update_code OR commussion.last_update_code > goods.last_update_code)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':addressId',$addressId);
        $stmt->bindParam(':lastUpdateCode',$lastUpdateCode);
    
        $stmt->execute();
        
        $num = $stmt->rowCount();

        
         $result = "";
        if($num>0){

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $id.":".$goods_price.":".$discount_value."&";
            }
        }else{
            $result = "no";
        }

        //remove '&' from the last result 
        return rtrim($result,'&');
        
    }

    //get updated Colour of the goods
    public function getUpdatedColor($lastUpdateCode){

        $query = "SELECT id,color_status FROM goods WHERE last_update_colour>:lastUpdateCode AND last_update_colour>last_update_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lastUpdateCode',$lastUpdateCode);
        $stmt->execute();
        
        $num = $stmt->rowCount();
        $result ="";
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                
                    $result.=$id.":".$this->getColor($id)."&";
            }
        }else{
            $result = "no";
        }
        return rtrim($result,'&');
    }

    // in this we get color update based on goodsId
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


    //get updated goods
    public function getUpdatedGoods($lastUpdateCode,$addressId){

        $query = "SELECT goods.*,goods.pure_price+ COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id =:addressId WHERE goods.last_update_code>:lastUpdateCode";
        $stmt = $this->conn->Prepare($query);

        $stmt->bindParam(':addressId',$addressId);
        $stmt->bindParam(':lastUpdateCode',$lastUpdateCode);
        
        $stmt->execute();
        
        $num = $stmt->rowCount();

        $result = "";
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);  
                   $color = $this->getColor($id);
                $result .= $id.":".$category_id.":".$seo.":".$model.":".$description.":".$max_order.":".$min_order.":".$discount_start.":".$show_in_home.":".$color.":".$available_in_market.":".$goods_price.":".$discount_value.":".$image_url.":".$priority.":".$category_two."&";
            }

        }else{

            $result = "no";
        }
        return rtrim($result,'&');
    }


    //get updated Category
    public function getUpdatedCategory($lastUpdateCode){

        $query = "SELECT * FROM category WHERE last_update_code>:lastUpdatedCode";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':lastUpdatedCode',$lastUpdateCode);
        $stmt->execute();

        $num = $stmt->rowCount();

        $result = "";
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $id.":".$parent_id.":".$to_sub_category.":".$name.":".$image_url.":".$is_available.":".$priority.":".$brand.":".$show_info."&";
            }

        }else{
            $result = "no";
        }
        return rtrim($result,'&');
    }

    //gets updated goods availability 
    public function getUpdatedGoodsAvailability($lastUpdateCode){

        $query = "SELECT id,available_in_market FROM goods WHERE last_update_availability>:lastUpdateCode";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':lastUpdateCode',$lastUpdateCode);
        $stmt->execute();

        $num = $stmt->rowCount();

        $result = "";

        if($num > 0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $id.":".$available_in_market."&";
            }
        }else{
            $result = "no";
        }
        return rtrim($result,"&");
    }

    //gets updated Category availability
    public function getUpdatedCategoryAvailability($lastUpdateCode){

        $query = "SELECT id,is_available FROM category WHERE last_update_availability>:lastUpdateCode";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':lastUpdateCode',$lastUpdateCode);
        $stmt->execute();

        $num = $stmt->rowCount();

        $result = "";

        if($num > 0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $id.":".$is_available."&";
            }
        }else{
            $result = "no";
        }
        return rtrim($result,'&');
    }


    //we don't need lastupdatecode for priority b/ce it fetchs the leatest 
    public function getHigherPriority(){
        $query = "SELECT id FROM goods WHERE priority=2";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $result = "";

        if($num > 0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $id."&";
            }
        }else{
            $result = "no";
        }
        return rtrim($result,'&');
    }

    public function getMediumPriority(){
        $query = "SELECT id FROM goods WHERE priority=1";
        $stmt = $this->conn->Prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $result = "";

        if($num > 0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $id."&";
            }
        }else{
            $result = "no";
        }
        return rtrim($result,'&');
    }

    public function getUpdatedCode($lastUpdateCode){
        $query = "SELECT * FROM code_giver WHERE last_update_code>:lastUpdatedCode";
        $stmt = $this->conn->Prepare($query);
        $stmt->bindParam(':lastUpdatedCode',$lastUpdateCode);
        $stmt->execute();

        $num = $stmt->rowCount();

        $result = "";
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $result .= $code.":".$code_value."&";
            }
        }else{
            $result = "no";
        }
        return rtrim($result,'&');
    }

}















?>