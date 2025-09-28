<?php

class Rules{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }
  
    public function getAllTerms(){
        $query = "SELECT * FROM terms_conditions";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allTerms = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $eachTerms = array(
                    "termsId" =>$terms_id,
                    "termsDetail" =>$terms_detail
                );
                array_push($allTerms,$eachTerms);
            }
        }
        return $allTerms;
    }

	    public function getAllRules($userId){
        $query = "SELECT user.name, customer.taxi,customer.show_taxi,customer.show_debit,customer.permitted_debit,customer.fast_delivery_value,customer.debit_you_have FROM user LEFT JOIN customer ON user.customer_id = customer.id WHERE user.id = '$userId'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();
			
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$result = array(
			"showTerms"=>0,
			"showTaxi"=>$row['show_taxi'],
			"taxi"=>$row['taxi'],
			"showDebit"=>$row['show_debit'],
			"permittedDebit"=>$row['permitted_debit'],
			"debitYouHave"=>$row['debit_you_have'],
			"debitReturnDate"=>"የወሰዱት ብድር በአራተኛ ቀን ይመልሳሉ",
			"ruleUpdated"=>0,
			"fastDeliverValue"=>$row["fast_delivery_value"],
			"freeDelivery"=>10000
			);

			return $result;
    }

}