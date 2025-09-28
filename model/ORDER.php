<?php

class ORDER{

    private $conn;
    public function __construct($conn){
 
        $this->conn = $conn;
    }

    public function insertOrder($userId,$customerId,$totalPrice,$orderLastUpdateCode,$comment){

        $presentLastUpdateCode = $this->getNewUpdateCode();
        $query = "INSERT INTO orders (id, customer_id, user_id, total_price, ordered_last_update_code, present_last_update_code, profit, order_time, deliver_time, deliver_status,comment) VALUES (NULL, '$customerId', '$userId', '$totalPrice', '$orderLastUpdateCode', '$presentLastUpdateCode', '0', current_timestamp(), current_timestamp(), '1','$comment')";
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }
        public function insertManualOrder($userId){

        $presentLastUpdateCode = $this->getNewUpdateCode();
        $query = "INSERT INTO orders (id, customer_id, user_id, total_price, ordered_last_update_code, present_last_update_code, profit, order_time, deliver_time, deliver_status,comment) VALUES (NULL, '0', '$userId', '0', '0', '$presentLastUpdateCode', '0', current_timestamp(), current_timestamp(), '1','added manually')";
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }
    public function updateToFastDelivery($orderId,$deliveryStatus){
        $query = "UPDATE orders SET deliver_status = '$deliveryStatus' WHERE orders.id = '$orderId'";
        $stmt = $this->conn->exec($query);
        //return $orderId;
    }
    
    public function getPhoneNumber($userId){
        	 
        $user_query = "SELECT * FROM user WHERE user.id = '$userId'";
        $user_stmt = $this->conn->prepare($user_query); 
        $user_stmt->execute();
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

        return $user['phone_number'];
    
    }

    public function insertEachOrder($orderId,$goodsId,$quantity,$eachPrice,$useDiscount,$colorOne,$colorTwo,$colorThree,$isColorTouched){
        
        $gId = $goodsId;
        $isSpecial = 0;
/*        if($goodsId>10000){
            $gId = $goodsId-10000;
            $isSpecial = 1;
        }*/
        
        $query = "INSERT INTO ordered_list (id, orders_id, goods_id, quantity, each_price, use_discount,color_one,color_two,color_three,is_color_touched,isSpecial) VALUES (NULL, '$orderId', '$gId', '$quantity', '$eachPrice', '$useDiscount', '$colorOne', '$colorTwo', '$colorThree', '$isColorTouched','$isSpecial')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if($gId>100000){
         $q = "UPDATE cover_supplier_model SET store = store - '$quantity' WHERE id='$gId'";
         $s = $this->conn->exec($q);    
        }else if($gId>10000 && $gId<100000){
            
         $q1 = "UPDATE new_goods SET store = store - '$quantity' WHERE id='$gId'";
         $s1 = $this->conn->exec($q1); 
        
        //after manual order it must update the total order price 
        $q2 = "UPDATE orders o JOIN ( SELECT orders_id, SUM(each_price * quantity) as total_price FROM ordered_list WHERE is_cancelled=0 GROUP BY orders_id ) ol ON o.id = ol.orders_id SET o.total_price = ol.total_price WHERE o.deliver_status<5";
            $s2 = $this->conn->exec($q2); 
        }else{
          $qq = "UPDATE goods SET store = store - '$quantity' WHERE id='$gId'";
         $ss = $this->conn->exec($qq);   
        }
 
    }
    public function manualOrder($orderId,$name,$quantity,$price,$profit){
        
        
        $query = "INSERT INTO new_goods (id, name, price, supplier_id, shop_id, profit, image_url, store) VALUES (NULL, '$name', '$price', '1', '1', '$profit', 'no', 0);";
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        
        return $last_id;
    }
    public function getNewUpdateCode() {

        $settings_query = "SELECT * from settings WHERE setting_key = 'new_update_code'";
        $settings_stmt = $this->conn->prepare($settings_query); 
        $settings_stmt->execute();
        $newUpdateCode = $settings_stmt->fetch(PDO::FETCH_ASSOC);
        return $newUpdateCode['value'];

    }

	    public function getAllOrders() {
        $query = "SELECT user.id AS userId,user.name, customer.debit_you_have,customer.phone,customer.specific_address,customer.location_description,customer.location, customer.address_id,orders.id AS order_id,orders.total_price,orders.deliver_status,orders.comment FROM orders LEFT JOIN user ON orders.user_id = user.id LEFT JOIN customer ON customer.id = user.customer_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrdered = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrder = array(
                    "userId"=>$userId,
                    "specificAddress"=>$specific_address,
                    "debit"=>$debit_you_have,
                    "phone"=>$phone,
                    "orderId" => $order_id,
                    "name" => $name,
                    "totalPrice" => $total_price,
                    "deliverStatus"=>$deliver_status,
                    "addressId"=>$address_id,
                    "description"=>$location_description,
                    "comment"=>$comment,
                    "location"=>$location
                );
                array_push($totalOrdered,$eachOrder);
            }
        }
        return $totalOrdered;

    }
    
    // MODIFIED FUNCTION
public function getAllOrdersByDate($order_date) {
    // Note the added WHERE clause
    $query = "SELECT 
                u.id AS userId, 
                u.name, 
                c.debit_you_have, 
                c.phone, 
                c.specific_address, 
                c.location_description, 
                c.location, 
                c.address_id, 
                o.id AS order_id, 
                o.total_price, 
                o.deliver_status, 
                o.comment 
              FROM orders o 
              LEFT JOIN user u ON o.user_id = u.id 
              LEFT JOIN customer c ON u.customer_id = c.id
              WHERE DATE(o.order_time) = :order_date AND o.deliver_status<7"; // Filter by date
    
    $stmt = $this->conn->prepare($query);

    // Bind the date parameter
    $stmt->bindParam(':order_date', $order_date);
    
    $stmt->execute();
    $num = $stmt->rowCount();

    $ordersArray = array();

    if($num > 0) {
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $eachOrder = array(
                "userId" => $userId,
                "specificAddress" => $specific_address,
                "debit" => $debit_you_have,
                "phone" => $phone,
                "orderId" => $order_id,
                "name" => $name,
                "totalPrice" => $total_price,
                "deliverStatus" => $deliver_status,
                "addressId" => $address_id,
                "description" => $location_description,
                "comment" => $comment,
                "location" => $location
            );
            array_push($ordersArray, $eachOrder);
        }
    }
    return $ordersArray;
}
    
     public function getOrderStatus($userId) {
        $query = "SELECT orders.id,orders.deliver_status FROM orders WHERE user_id ='$userId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrdered = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrder = array(
                    "orderId" => $id,
                    "orderedDate" => "date",
                    "totalPrice" => 0,
                    "orderedGoods"=>"goods",
                    "status"=>$deliver_status
                );
                array_push($totalOrdered,$eachOrder);
            }
        }
        return $totalOrdered;

    }
    
    public function updateSentSms($addresses,$suppliers){
        
        $ads = implode(',', $addresses);
        $srs = implode(',', $suppliers);
         $q = "UPDATE ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id LEFT JOIN orders ON orders.id = ordered_list.orders_id LEFT JOIN user ON orders.user_id = user.id LEFT JOIN customer ON customer.id = user.customer_id SET ordered_list.is_sms_send = 1 WHERE orders.deliver_status = 1 AND customer.address_id IN ($ads) AND goods.shop_id IN ($srs)";
         $s = $this->conn->exec($q);
    }
    
    public function getOrderBasedOnSupplier($addresses){

          // Implode into a comma-separated list
            $inClause = implode(',', $addresses);
            //echo $inClause;
        $query = "SELECT ordered_list.goods_id,goods.shop_id,goods.model,goods.pure_price,goods.image_url,SUM(ordered_list.quantity) AS total_quantity FROM ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id LEFT JOIN orders ON orders.id = ordered_list.orders_id LEFT JOIN user ON orders.user_id = user.id LEFT JOIN customer ON customer.id = user.customer_id WHERE orders.deliver_status = 1 AND ordered_list.is_sms_send = 0 AND ordered_list.is_cancelled = 0 AND customer.address_id IN ($inClause)  AND DATE(orders.order_time) = CURRENT_DATE GROUP BY goods.id ORDER BY ordered_list.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrderList = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrderList = array(
                    "goodsId" => $goods_id,
                    "model" => $model,
                    "price" => $pure_price,
                    "totalQuantity" => $total_quantity,
					"imageUrl"=>$image_url,
					"supplierId"=>$shop_id
                );
                array_push($totalOrderList,$eachOrderList);
            }
        }
        return $totalOrderList;
    
        
    }
                                                                                                                                                                                          
	public function getOrderListBasedOnUser($orderId){

        $query = "SELECT ordered_list.*,goods.model,goods.image_url,goods.isFinishable FROM ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id WHERE ordered_list.orders_id = '$orderId' AND ordered_list.goods_id < 10000";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrderList = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrderList = array(
                    "orderId" => $orders_id,
                    "orderListId"=>$id,
                    "model" => $model,
                    "goodsId" => $goods_id,
                    "quantity" => $quantity,
					"imageUrl"=>$image_url,
                    "price" => $each_price,
                    "isPrepared"=>$each_prepare,
                    "isCancelled"=>$is_cancelled,
                    "isFinishable"=>$isFinishable
                );
                array_push($totalOrderList,$eachOrderList);
            }
        }
        return $totalOrderList;
    }
    
    
    	public function getOrderListFromNewGoods($orderId){

        $query = "SELECT ordered_list.*,new_goods.name,new_goods.image_url FROM ordered_list LEFT JOIN new_goods ON ordered_list.goods_id = new_goods.id WHERE ordered_list.orders_id = '$orderId' AND ordered_list.goods_id>10000 AND ordered_list.goods_id<100000";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrderList = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrderList = array(
                    "orderId" => $orders_id,
                    "orderListId"=>$id,
                    "model" => $name,
                    "goodsId" => $goods_id,
                    "quantity" => $quantity,
					"imageUrl"=>$image_url,
                    "price" => $each_price,
                    "isPrepared"=>$each_prepare,
                    "isCancelled"=>$is_cancelled,
                    "isFinishable"=>0
                );
                array_push($totalOrderList,$eachOrderList);
            }
        }
        return $totalOrderList;
    }
    
    
    public function getOrderListFromCover($orderId){

        $query = "SELECT ordered_list.*,cover_model.model,COALESCE((SELECT cover_image.url FROM cover_image WHERE cover_image.supplier_id = cover_supplier_model.supplier_id LIMIT 1),'no') AS image_url FROM ordered_list LEFT JOIN cover_supplier_model ON cover_supplier_model.id = ordered_list.goods_id LEFT JOIN cover_model ON cover_model.id =cover_supplier_model.model_id WHERE ordered_list.orders_id = '$orderId' AND ordered_list.goods_id>100000";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrderList = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrderList = array(
                    "orderId" => $orders_id,
                    "orderListId"=>$id,
                    "model" => $model." cover",
                    "goodsId" => $goods_id,
                    "quantity" => $quantity,
					"imageUrl"=>$image_url,
                    "price" => $each_price,
                    "isPrepared"=>$each_prepare,
                    "isCancelled"=>$is_cancelled,
                    "isFinishable"=>0
                );
                array_push($totalOrderList,$eachOrderList);
            }
        }
        return $totalOrderList;
    }
    
	public function getOverAllOrderList(){

        $query = "SELECT SUM(ordered_list.quantity) AS Q,ordered_list.orders_id,ordered_list.goods_id,ordered_list.overall_prepare,ordered_list.is_cancelled,goods.model,goods.image_url,goods.isFinishable FROM ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id LEFT JOIN orders ON ordered_list.orders_id = orders.id WHERE orders.deliver_status < 5 GROUP BY goods.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrderList = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrderList = array(
                    "orderId" => 0,
                    "orderListId"=>0,
                    "model" => $model,
                    "goodsId" => $goods_id,
                    "quantity" => $Q,
					"imageUrl"=>$image_url,
                    "price" => 0,
                    "isPrepared"=>$overall_prepare,
                    "isCancelled"=>$is_cancelled,
                    "isFinishable"=>$isFinishable
                );
                array_push($totalOrderList,$eachOrderList);
            }
        }
        return $totalOrderList;
    }
    public function getOrderListBasedOnGoods(){
        /*$query = "SELECT ordered_list.goods_id, goods.model,goods.pure_price,goods.image_url, COALESCE(SUM(ordered_list.quantity),0) AS ordered_quantity, COALESCE(available_purchased_goods.quantity,0) AS purchased_quantity FROM ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id LEFT JOIN available_purchased_goods ON ordered_list.goods_id = available_purchased_goods.goods_id WHERE ordered_list.is_shipped = 0 GROUP BY ordered_list.goods_id";*/
        //later i will add address
        $query = "SELECT ordered_list.goods_id,goods.model,goods.store,goods.image_url,goods.pure_price,goods.shop_id,COALESCE(supplier.shop_name,'no') AS shop_name FROM ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id LEFT JOIN orders ON ordered_list.orders_id = orders.id LEFT JOIN supplier ON supplier.shop_id = goods.shop_id WHERE orders.deliver_status<5 AND goods.store<0 GROUP BY goods.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrdered = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $orderedGoods = array(
                    "goodsId" => $goods_id,
                    "price" => $pure_price,
                    "imageUrl" => $image_url,
                    "store" => $store,
                    "shopName" => $shop_name,
                    "shopId"=>$shop_id,
                    "model"=>$model,
                    "priority"=>1,
                    "categoryId"=>8,
                    "isAvailable"=>1
                );

                array_push($totalOrdered,$orderedGoods);
            }

        }
        return $totalOrdered;


    }
    
        public function getAllGoods(){

        $query = "SELECT goods.id,goods.model,goods.category_id,goods.image_url,goods.pure_price,goods.priority,goods.store,goods.available_in_market,goods.shop_id ,supplier.shop_name FROM goods LEFT JOIN supplier ON goods.shop_id = supplier.shop_id WHERE goods.last_delete_code =0 ORDER BY goods.available_in_market DESC,goods.priority DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrdered = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $orderedGoods = array(
                    "goodsId" => $id,
                    "price" => $pure_price,
                    "imageUrl" => $image_url,
                    "store" => $store,
                    "shopName" => $shop_name,
                    "shopId"=>$shop_id,
                    "model"=>$model,
                    "priority"=>$priority,
                    "categoryId"=>$category_id,
                    "isAvailable"=>$available_in_market
                );

                array_push($totalOrdered,$orderedGoods);
            }

        }
        return $totalOrdered;


    }
    
    public function getAllGoodsBasedOnSupplier(){
        

        $query = "SELECT goods.id,goods.model,goods.category_id,goods.image_url,goods.pure_price,goods.priority,goods.store,goods.available_in_market,goods.shop_id ,supplier.shop_name FROM goods LEFT JOIN supplier ON goods.shop_id = supplier.shop_id WHERE goods.shop_id =3 ORDER BY goods.available_in_market DESC,goods.priority DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrdered = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $orderedGoods = array(
                    "goodsId" => $id,
                    "price" => $pure_price,
                    "imageUrl" => $image_url,
                    "store" => $store,
                    "shopName" => $shop_name,
                    "shopId"=>$shop_id,
                    "model"=>$model,
                    "priority"=>$priority,
                    "categoryId"=>$category_id,
                    "isAvailable"=>$available_in_market
                );

                array_push($totalOrdered,$orderedGoods);
            }

        }
        return $totalOrdered;


    
    }

    public function getOrderedGoodsDetail($goodsId){
    //this is used to see the detail of a goods e.g who order the goods colors price
    //this is based on the user's order

    $query = "SELECT goods.id AS goods_id,goods.model,ordered_list.quantity,ordered_list.each_price, ordered_list.color_one,ordered_list.color_two, ordered_list.color_three, ordered_list.is_color_touched,user.name AS user_name FROM ordered_list LEFT JOIN goods ON ordered_list.goods_id = goods.id LEFT JOIN orders ON ordered_list.orders_id = orders.id LEFT JOIN user ON orders.user_id = user.id WHERE ordered_list.goods_id = '$goodsId' AND ordered_list.is_shipped = 0";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $num = $stmt->rowCount();

    $goodsOrderList = array();

    if($num>0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $goodsOrder = array(
                "goodsId" =>$goods_id,
                "model" =>$model,
                "quantity" =>$quantity,
                "eachPrice" =>$each_price,
                "colorOne" =>$color_one,
                "colorTwo" =>$color_two,
                "colorThree" =>$color_three,
                "isColorTuoched" =>$is_color_touched,
                "userName" =>$user_name
            );
            array_push($goodsOrderList,$goodsOrder);
        }
    }
    return $goodsOrderList;
    }

    public function getDeliveryTime($customerId){
        $query = "SELECT  active_deliver FROM customer LEFT JOIN address ON customer.address_id = address.id LEFT JOIN deliver_time ON deliver_time.address_id = address.id WHERE customer.id = '$customerId' LIMIT 1";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result['active_deliver'];
    }

    public function purchaseGoods($goodsId,$adminId,$eachPrice,$previousPrice,$quantity){
        $query = "INSERT INTO purchase_goods (id, goods_id, admins_id, quantity, each_price, privious_price) VALUES (NULL, '$goodsId', '$adminId', '$quantity', '$eachPrice', '$previousPrice')";
        $stmt = $this->conn->exec($query);
        
        //first insert to purchase_goods then update store 
        $q = "UPDATE goods SET store = store + '$quantity' WHERE id='$goodsId'";
         $s = $this->conn->exec($q);
         
         //if previous price is not the same as price of now it will insert to purchase gain table
         if($eachPrice != $previousPrice){
             $gain = ($previousPrice-$eachPrice)*$quantity;
             $q1 = "INSERT INTO purchase_gain (id, goods_id, gain, reason, date) VALUES (NULL, '$goodsId', '$gain', 'purchase', current_timestamp())";
             $s1 = $this->conn->exec($q1);
         }
  

/*
        $qeury1 ="SELECT COALESCE(quantity,0) AS previousQuantity FROM available_purchased_goods WHERE goods_id = '$goodsId' LIMIT 1";	
        $stmt1 = $this->conn->prepare($qeury1);
        $stmt1->execute();
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);

        
        if($stmt1->rowCount() <1){
            //in this case it needs to be inserted
            $query2 = "INSERT INTO available_purchased_goods(goods_id,quantity) VALUES ('$goodsId','$quantity')";
            $stmt2 = $this->conn->exec($query2);
        }else{
            $oldQuantity = $result['previousQuantity'];	
            $totalQuantity = $oldQuantity + $quantity;
            // in this case it needs update
            $query3 = "UPDATE available_purchased_goods SET quantity = '$totalQuantity' WHERE goods_id = '$goodsId'";
            $stmt3 = $this->conn->exec($query3);
        }


        */
    }
    
        public function manualSell($goodsId,$price,$additionalInfo,$quantity){
        $query = "INSERT INTO manual_sell (id, goods_id, quantity, price, additional_info, selling_time, is_closed) VALUES (NULL, '$goodsId', '$quantity', '$price', '$additionalInfo', current_timestamp(), '0')";
        $stmt = $this->conn->exec($query);
        
        //first insert to purchase_goods then update store 
        $q = "UPDATE goods SET store = store - '$quantity' WHERE id='$goodsId'";
         $s = $this->conn->exec($q);
        }
        
        public function cancelOrder($orderId){
            //first select ordered goods based on 
        $query = "SELECT * FROM ordered_list WHERE orders_id ='$orderId'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrderList = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
        //update the store 
            if($goods_id>100000){
              $q3 = "UPDATE cover_supplier_model SET store = store + '$quantity' WHERE id='$goods_id'";
               $s3 = $this->conn->exec($q3);    
           }else if($goods_id>10000 && $goods_id<100000){
            
              $q1 = "UPDATE new_goods SET store = store + '$quantity' WHERE id='$goods_id'";
              $s1 = $this->conn->exec($q1); 
        
            }else{
              $qq1 = "UPDATE goods SET store = store + '$quantity' WHERE id='$goods_id'";
            $ss1 = $this->conn->exec($qq1);   
          }
        
            }
        $qq = "UPDATE orders SET deliver_status = '7' WHERE orders.id = '$orderId'";
         $ss = $this->conn->exec($qq); 
        }

        }
        
        public function updateStoreToZero($goods_id){
        $q = "UPDATE goods SET store = 0 WHERE id='$goods_id'";
         $s = $this->conn->exec($q);  
        }
        public function updateToDeliver($orderId,$deliverStatus,$customerId){
        $q = "UPDATE orders SET deliver_status = '$deliverStatus' WHERE orders.id = '$orderId'";
         $s = $this->conn->exec($q); 
         if($deliverStatus == 6){
             //calculate Profit
         $qq = "UPDATE orders SET orders.profit = ( SELECT SUM((ordered_list.each_price-goods.pure_price)*ordered_list.quantity) AS total FROM ordered_list LEFT JOIN goods ON goods.id = ordered_list.goods_id LEFT JOIN orders ON orders.id =ordered_list.orders_id WHERE ordered_list.goods_id<10000 AND ordered_list.is_cancelled =0 AND ordered_list.orders_id = '$orderId') WHERE orders.id = '$orderId'";
         $ss = $this->conn->exec($qq); 
         
              $query2 = "UPDATE customer SET unpaid = unpaid + (
        SELECT total_price FROM orders WHERE id ='$orderId'
    ) WHERE customer.id = '$customerId'";
        $stmt2 = $this->conn->prepare($query2);
        return $stmt2->execute();
         }
        }
        
        public function updateEachPrepare($orderId,$value){
        $q = "UPDATE ordered_list SET each_prepare = '$value' WHERE id = '$orderId'";
         $s = $this->conn->exec($q); 
        }
        public function updateEachCancel($orderId,$value,$quantity,$gId){
        $q = "UPDATE ordered_list SET is_cancelled = '$value' WHERE id = '$orderId'";
         $s = $this->conn->exec($q); 
         
        //update the store 
               if($gId>100000){
         $q3 = "UPDATE cover_supplier_model SET store = store + '$quantity' WHERE id='$gId'";
         $s2 = $this->conn->exec($q3);    
        }else if($gId>10000 && $gId<100000){
            
         $q1 = "UPDATE new_goods SET store = store + '$quantity' WHERE id='$gId'";
         $s1 = $this->conn->exec($q1); 
        
        }else{
          $qq = "UPDATE goods SET store = store + '$quantity' WHERE id='$gId'";
         $ss = $this->conn->exec($qq);   
        }
         
        $q2 = "UPDATE orders o JOIN ( SELECT orders_id, SUM(each_price * quantity) as total_price FROM ordered_list WHERE is_cancelled=0 GROUP BY orders_id ) ol ON o.id = ol.orders_id SET o.total_price = ol.total_price WHERE o.deliver_status<5";
            $s2 = $this->conn->exec($q2); 
        }
        
        
        
        public function updateEachQuantity($orderId,$value,$quantity,$gId){
        $q = "UPDATE ordered_list SET quantity = '$value' WHERE id = '$orderId'";
         $s = $this->conn->exec($q); 
        
        //update the store 
               if($gId>100000){
         $q3 = "UPDATE cover_supplier_model SET store = store + '$quantity' WHERE id='$gId'";
         $s2 = $this->conn->exec($q3);    
        }else if($gId>10000 && $gId<100000){
            
         $q1 = "UPDATE new_goods SET store = store + '$quantity' WHERE id='$gId'";
         $s1 = $this->conn->exec($q1); 
        
        }else{
          $qq = "UPDATE goods SET store = store + '$quantity' WHERE id='$gId'";
         $ss = $this->conn->exec($qq);   
        }
        
        
         
        $q2 = "UPDATE orders o JOIN ( SELECT orders_id, SUM(each_price * quantity) as total_price FROM ordered_list WHERE is_cancelled=0 GROUP BY orders_id ) ol ON o.id = ol.orders_id SET o.total_price = ol.total_price WHERE o.deliver_status<5";
            $s2 = $this->conn->exec($q2); 
        }
        
        public function mixOrder($orderId,$userId){
            $this->mixComment($userId,$orderId); //first mix the comment
            $q = "UPDATE ordered_list
LEFT JOIN orders ON ordered_list.orders_id = orders.id
SET ordered_list.orders_id = '$orderId'
WHERE orders.user_id = '$userId'
  AND orders.deliver_status < 5
  AND DATE(orders.order_time) = CURDATE()";
            $s = $this->conn->exec($q); 
            
             $q1 = "UPDATE orders SET orders.deliver_status = 9 WHERE orders.user_id ='$userId' AND orders.deliver_status <5 AND orders.id !='$orderId' AND DATE(orders.order_time) = CURDATE()";
            $s1 = $this->conn->exec($q1); 
            
            
            
            $q2 = "UPDATE orders o JOIN ( SELECT orders_id, SUM(each_price * quantity) as total_price FROM ordered_list WHERE is_cancelled=0 GROUP BY orders_id ) ol ON o.id = ol.orders_id SET o.total_price = ol.total_price WHERE o.deliver_status<5 AND DATE(o.order_time) = CURDATE()";
            $s2 = $this->conn->exec($q2); 
            
            
            //update all orderlist there order categrory
            //update the total price of the order 
            //update the statues of the order with out the main order 
        }
        //local function for mixing comment 
        function mixComment($userId,$orderId){
            
        $query = "SELECT orders.comment FROM orders WHERE orders.user_id ='$userId' AND orders.deliver_status <5 AND DATE(orders.order_time) = CURDATE()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

          $comments ="";
        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $comments.="\n".$comment;
            }
        }
     $q = "UPDATE orders SET comment ='$comments' WHERE orders.id = '$orderId'";
         $s = $this->conn->exec($q); 

    
    
        }
        public function getQuantityBasedOnUser($goodsId){
            
            $q = "SELECT ordered_list.id,ordered_list.quantity,user.name,customer.specific_address FROM ordered_list LEFT JOIN orders ON orders.id = ordered_list.orders_id LEFT JOIN user ON orders.user_id=user.id LEFT JOIN customer ON customer.id = user.customer_id WHERE ordered_list.goods_id ='$goodsId' AND orders.deliver_status <5";
            
    $stmt = $this->conn->prepare($q);
    $stmt->execute();

    $num = $stmt->rowCount();

    $quantityList = array();

    if($num>0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $eachQuantity = array(
                "userName" =>$name,
                "address" =>$specific_address,
                "quantity" =>$quantity,
                "orderListId" =>$id
            );
            array_push($quantityList,$eachQuantity);
        }
    }
    return $quantityList;
        }
        
        
    
    public function getAllSupplier(){
        
        
        $query = "SELECT shop_id,shop_name,phone FROM supplier WHERE isVisible =1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allShops = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachShop = array(
                    "supplierId" => $shop_id,
                    "supplierName" => $shop_name,
                    "phone"=>$phone
                );
                array_push($allShops,$eachShop);
            }
        }
        return $allShops;

    
    
    }
    
    public function getAllAddress(){
        //latter i will filter only address we got order
        
        
        
        $query = "SELECT DISTINCT address.id,address.sub_city FROM orders LEFT JOIN user ON user.id = orders.user_id LEFT JOIN customer ON user.customer_id = customer.id LEFT JOIN address ON customer.address_id = address.id WHERE orders.deliver_status = 1 AND DATE(orders.order_time) = CURRENT_DATE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allShops = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachShop = array(
                    "addressId" => $id,
                    "addressName" => $sub_city
                );
                array_push($allShops,$eachShop);
            }
        }
        return $allShops;

    
    
    }
        
    public function getAllShops(){
        
        $query = "SELECT shop_id,shop_name FROM supplier";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allShops = array();

        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachShop = array(
                    "addressId" => $shop_id,
                    "addressName" => $shop_name
                );
                array_push($allShops,$eachShop);
            }
        }
        return $allShops;

    
    }
    public function changeShop($goodsId,$shopsId){
     $q = "UPDATE goods SET shop_id ='$shopsId' WHERE goods.id ='$goodsId'";
         $s = $this->conn->exec($q); 
         return $s;
    }
    
    public function updateStoreQuantity($goodsId,$store){
     $q = "UPDATE goods SET store = '$store' WHERE goods.id = '$goodsId'";
         $s = $this->conn->exec($q); 
         return $s;
    }
    

     public function getAllOrderForUser($userId) {
        $query = "SELECT orders.id,orders.total_price,orders.deliver_status,DATE_FORMAT(orders.order_time, '%W, %M %d') AS formatted_date FROM orders WHERE user_id ='$userId'AND orders.deliver_status<9 ORDER BY deliver_status,id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $totalOrdered = array();

    
        if($num > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $eachOrder = array(
                    "orderId" => $id,
                    "orderedDate" => $formatted_date,
                    "totalPrice" => $total_price,
                    "orderedGoods"=>"goods",
                    "status"=>$deliver_status,
                    "additionalFee"=>-1
                );
                array_push($totalOrdered,$eachOrder);
            }
        }
        return $totalOrdered;

    }
}









?>