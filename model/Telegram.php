<?php

class Telegram{
    
    private $conn;
    
    public function __construct($conn){
        $this->conn = $conn;
    }
    
    public function checkUser($telegramId,$telegramUserName,$telegramName,$supplierId){
        
        //in this case it checkes if telegram id is exit and phone number is exist as well
        //and the user is approved 
        
        $result =0;
        
        $query = "SELECT * FROM telegram WHERE telegram_id = '$telegramId' LIMIT 1";
        
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();
      
      if($num>0){
          //in this case it may be phone number, is not approved, or phone number is not found in our database 
          //so enter phonenumber, contactus , correcly checked and return userId
          
          $r = $stmt->fetch(PDO::FETCH_ASSOC);
          $telegramId = $r['telegram_id'];
          $telegramName = $r['telegram_name'];
          $telegramUserName =$r['telegram_username'];
          $phoneNumber = $r['phone_number'];
          $userId = $r['user_id'];
          $isApproved = $r['is_approved'];
          
          if($phoneNumber =='no'){
              //phone number is not registered yet
              $result = 0;
          }else if($userId ==0){
              //has phone number but not matched with user table still
              //1 for no user Id
              $result = 1;
          }else if($isApproved <0){
              //the account is banned by some reseason e.g it uses by another user
              //2 for is approved
              $result = 2;
              
          }else{
              //in this case i first check if the link is not expired
              //if result is 3 it means the link is expired
              $qy = "SELECT * FROM cover_supplier WHERE id = '$supplierId' LIMIT 1";
            $st = $this->conn->prepare($qy);
            $st->execute();

            $n = $st->rowCount();
            if($n>0){
                $rr = $st->fetch(PDO::FETCH_ASSOC);
                $isExpired = $rr['is_expire'];
                if($isExpired ==1){
                    $result = 3;
                }else{
                   $result = $userId; 
                }
            }else{
                $result = 3;
            }
          }
          
          
      }else{
          //it is not registered and register it
          //and return to give phone number
          $q = "INSERT INTO telegram (id, telegram_id, telegram_name, telegram_username, is_approved, user_id, phone_number) VALUES (NULL, '$telegramId', '$telegramName', '$telegramUserName', '0', '0', 'no')";
          $stmt = $this->conn->exec($q);
          $result = 0;
          //echo 'no this telegram id is not registered yet';
      }
        
        //if result is zero ask user to inter phone number if it is one ask 
        //user to contact us else it means it is user's phone number 
       return $result;
        
    }
    
    
    
    
        public function checkUserForGame($telegramId,$telegramUserName,$telegramName){
        
        //in this case it checkes if telegram id is exit and phone number is exist as well
        //and the user is approved 
        
        $result =0;
        
        $query = "SELECT * FROM telegram WHERE telegram_id = '$telegramId' LIMIT 1";
        
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();
      
      if($num>0){
          //in this case it may be phone number, is not approved, or phone number is not found in our database 
          //so enter phonenumber, contactus , correcly checked and return userId
          
          $r = $stmt->fetch(PDO::FETCH_ASSOC);
          $telegramId = $r['telegram_id'];
          $telegramName = $r['telegram_name'];
          $telegramUserName =$r['telegram_username'];
          $phoneNumber = $r['phone_number'];
          $userId = $r['user_id'];
          $isApproved = $r['is_approved'];
          
          if($phoneNumber =='no'){
              //phone number is not registered yet
              $result = 0;
          }else if($userId ==0){
              //has phone number but not matched with user table still
              //1 for no user Id
              $result = 1;
          }else if($isApproved <0){
              //the account is banned by some reseason e.g it uses by another user
              //2 for is approved
              $result = 2;
              
          }else{
              //in this case i will check if the user already try his chance
              $qy = "SELECT * FROM game WHERE user_id ='$userId'";
            $st = $this->conn->prepare($qy);
            $st->execute();

            $n = $st->rowCount();
            if($n>0){
                $result = 3;
            }else{
                $result = $userId;
            }
          }
          
          
      }else{
          //it is not registered and register it
          //and return to give phone number
          $q = "INSERT INTO telegram (id, telegram_id, telegram_name, telegram_username, is_approved, user_id, phone_number) VALUES (NULL, '$telegramId', '$telegramName', '$telegramUserName', '0', '0', 'no')";
          $stmt = $this->conn->exec($q);
          $result = 0;
          //echo 'no this telegram id is not registered yet';
      }
        
        //if result is zero ask user to inter phone number if it is one ask 
        //user to contact us else it means it is user's phone number 
       return $result;
        
    }
    
        public function checkUserFromCategory($telegramId,$telegramUserName,$telegramName,$supplierId){
        
        //in this case it checkes if telegram id is exit and phone number is exist as well
        //and the user is approved 
        
        $result =0;
        
        $query = "SELECT * FROM telegram WHERE telegram_id = '$telegramId' LIMIT 1";
        
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();
      
      if($num>0){
          //in this case it may be phone number, is not approved, or phone number is not found in our database 
          //so enter phonenumber, contactus , correcly checked and return userId
          
          $r = $stmt->fetch(PDO::FETCH_ASSOC);
          $telegramId = $r['telegram_id'];
          $telegramName = $r['telegram_name'];
          $telegramUserName =$r['telegram_username'];
          $phoneNumber = $r['phone_number'];
          $userId = $r['user_id'];
          $isApproved = $r['is_approved'];
          
          if($phoneNumber =='no'){
              //phone number is not registered yet
              $result = 0;
          }else if($userId ==0){
              //has phone number but not matched with user table still
              //1 for no user Id
              $result = 1;
          }else if($isApproved <0){
              //the account is banned by some reseason e.g it uses by another user
              //2 for is approved
              $result = 2;
              
          }else{
              $result = $userId;
          }
          
          
      }else{
          //it is not registered and register it
          //and return to give phone number
          $q = "INSERT INTO telegram (id, telegram_id, telegram_name, telegram_username, is_approved, user_id, phone_number) VALUES (NULL, '$telegramId', '$telegramName', '$telegramUserName', '0', '0', 'no')";
          $stmt = $this->conn->exec($q);
          $result = 0;
          //echo 'no this telegram id is not registered yet';
      }
        
        //if result is zero ask user to inter phone number if it is one ask 
        //user to contact us else it means it is user's phone number 
       return $result;
        
    }
    
    
    public function checkPhone($telegramId,$phoneNumber,$supplierId){
        $query = "SELECT id,name FROM user WHERE phone_number = '$phoneNumber' AND customer_level>-9 LIMIT 1";
              $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();
      
      $result = 1;
      if($num>0){
           $r = $stmt->fetch(PDO::FETCH_ASSOC);
           $id = $r['id'];
           //then check if this id is exit in telegram table this checks from approved row only
           $q= "SELECT * FROM telegram WHERE user_id = '$id' AND is_approved >-1";
           $s = $this->conn->prepare($q);
           $s->execute();

          $n = $s->rowCount();
          if($n>0){
              //in this case the id is exit 
              $result = 2;
          }else{
              //the id is not exit in our telegram table then we update the user id and phone number
              $q1 = "UPDATE telegram SET user_id = '$id',phone_number='$phoneNumber' WHERE telegram_id = '$telegramId'";
              $s1 = $this->conn->exec($q1);
              //lastly i will check whether the link is expire or not
              
              
              
              //in this case i first check if the link is not expired
              //if result is 3 it means the link is expired
              $q3 = "SELECT * FROM cover_supplier WHERE id = '$supplierId' LIMIT 1";
            $s3 = $this->conn->prepare($q3);
            $s3->execute();

            $n3 = $s3->rowCount();
            if($n3>0){
                $rr = $s3->fetch(PDO::FETCH_ASSOC);
                $isExpired = $rr['is_expire'];
                if($isExpired ==1){
                    $result = 3;
                }else{
                   $result = $id; 
                }
            }else{
                $result = 3;
            }
          
          //    $result = $id;
          }
      }else{
          $result = 1;
      }
      
      return $result;
    }
    
        public function checkPhoneForGames($telegramId,$phoneNumber){
        $query = "SELECT id,name FROM user WHERE phone_number = '$phoneNumber' AND customer_level>-9 LIMIT 1";
              $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();
      
      $result = 1;
      if($num>0){
           $r = $stmt->fetch(PDO::FETCH_ASSOC);
           $id = $r['id'];
           //then check if this id is exit in telegram table this checks from approved row only
           $q= "SELECT * FROM telegram WHERE user_id = '$id' AND is_approved >-1";
           $s = $this->conn->prepare($q);
           $s->execute();

          $n = $s->rowCount();
          if($n>0){
              //in this case the id is exit 
              $result = 2;
          }else{
              //the id is not exit in our telegram table then we update the user id and phone number
              $q1 = "UPDATE telegram SET user_id = '$id',phone_number='$phoneNumber' WHERE telegram_id = '$telegramId'";
              $s1 = $this->conn->exec($q1);
              //lastly i will check whether the link is expire or not
              
              
             $result = $id;
          }
      }else{
            //IN THIS CASE I WILL REGISTER USER TO MY TABLE....
      $q5 = "INSERT INTO telegram_phones (id, telegram_id, phone) VALUES (NULL, '$telegramId', '$phoneNumber')";
        $s5 = $this->conn->exec($q5);
          $result = 1;
      }
      
      return $result;
    }
    
    
    public function checkPhoneForCategory($telegramId,$phoneNumber,$supplierId){
        $query = "SELECT id,name FROM user WHERE phone_number = '$phoneNumber' AND customer_level >-9 LIMIT 1";
              $stmt = $this->conn->prepare($query);
      $stmt->execute();

      $num = $stmt->rowCount();
      
      $result = 1;
      if($num>0){
           $r = $stmt->fetch(PDO::FETCH_ASSOC);
           $id = $r['id'];
           //then check if this id is exit in telegram table this checks from approved row only
           $q= "SELECT * FROM telegram WHERE user_id = '$id' AND is_approved >-1";
           $s = $this->conn->prepare($q);
           $s->execute();

          $n = $s->rowCount();
          if($n>0){
              //in this case the id is exit 
              $result = 2;
          }else{
              //the id is not exit in our telegram table then we update the user id and phone number
              $q1 = "UPDATE telegram SET user_id = '$id',phone_number='$phoneNumber' WHERE telegram_id = '$telegramId'";
              $s1 = $this->conn->exec($q1);
              
          
            $result = $id;
          }
      }else{
          //IN THIS CASE I WILL REGISTER USER TO MY TABLE....
      $q5 = "INSERT INTO telegram_phones (id, telegram_id, phone) VALUES (NULL, '$telegramId', '$phoneNumber')";
        $s5 = $this->conn->exec($q5);
          $result = 1;
      }
      
      return $result;
    }
   public function getAllCover($supplierId) {
       
        $query = "SELECT cover_model.id, cover_model.model, cover_model.category, cover_supplier_model.price FROM cover_model LEFT JOIN cover_supplier_model ON cover_supplier_model.model_id = cover_model.id LEFT JOIN cover_supplier ON cover_supplier.id = cover_supplier_model.supplier_id WHERE cover_supplier.id ='$supplierId' AND cover_supplier_model.is_available =1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allCovers = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

               //latter i will change the long name to one letter
                 $eachCover = array(
                    "id" => $id,
                    "n" => $model,
                    "c" => $category,
                    "p" =>$price
                 );

                 array_push($allCovers,$eachCover);
            }
        }
        return $allCovers;
    }
    
 public function getAddressId($userId) {

        $query = "SELECT address.commission_address FROM customer LEFT JOIN address ON customer.address_id = address.id LEFT JOIN user ON user.customer_id = customer.id WHERE user.id ='$userId' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $addressId =  $result['commission_address'];
        return $addressId;
    }
  public function getAllGoods($userId){ 
      
       $addressId = $this->getAddressId($userId);
       
        $query = "SELECT goods.*,goods.pure_price+COALESCE(commussion.commusion_value,0) AS goods_price,COALESCE(commussion.commusion_value,0)-COALESCE(commussion.discount_commusion,0) AS discount_value FROM goods LEFT JOIN commussion ON goods.id = commussion.goods_id AND commussion.address_id ='$addressId' WHERE goods.available_in_market >0 AND goods.last_delete_code=0 ORDER BY goods.priority DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allCovers = array();
        $sc = 0;
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                if($category_id>13){
                    $sc = $seo;
                }else{
                    $sc = $category_two;
                }
               //latter i will change the long name to one letter
                 $eachCover = array(
                    "id" => $id,
                    "n" => $model,
                    "cid" => $category_id,
                    "sc"=>$sc,
                    "p" =>$goods_price,
                    "img"=>$image_url
                 );

                 array_push($allCovers,$eachCover);
            }
        }
        return $allCovers;
    
  }
  
public function insertWebOrder($userId,$totalPrice,$comment){

        //$presentLastUpdateCode = $this->getNewUpdateCode();
        $query = "INSERT INTO orders (id, customer_id, user_id, total_price, ordered_last_update_code, present_last_update_code, profit, order_time, deliver_time, deliver_status,comment) VALUES (NULL, '0', '$userId', '$totalPrice', '0', '0', '0', current_timestamp(), current_timestamp(), '1','$comment')";
        $stmt = $this->conn->exec($query);
        $last_id = $this->conn->lastInsertId();
        return $last_id;
    }
    
    public function insertPlayerResult($userId,$telegramId,$drawNumber){
         $query = "INSERT INTO game (id, user_id, telegram_id, draw_number, is_given) VALUES (NULL, '$userId', '$telegramId', '$drawNumber', '0')";
        $stmt = $this->conn->exec($query);
    }
    public function updateIsGiven($userId){
         $query = "UPDATE game SET is_given = '2' WHERE game.user_id = '$userId'";
        $stmt = $this->conn->exec($query); 
    }
    public function insertWebsEachOrder($orderId,$goodsId,$quantity,$eachPrice){
        
        $gId = $goodsId;
        $isSpecial = 0;
        
        $query = "INSERT INTO ordered_list (id, orders_id, goods_id, quantity, each_price, use_discount,color_one,color_two,color_three,is_color_touched,isSpecial) VALUES (NULL, '$orderId', '$goodsId', '$quantity', '$eachPrice', '0', '0', '0', '0', '0','$isSpecial')";
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
}

?>