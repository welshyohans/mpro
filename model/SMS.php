<?php

class SMS{

    private $apiSendUrl;
    private $apiBulkSendUrl;
    private $apiToken;
    private $from;
    private $sender;
    private $callback;
    private $statusCallback;
    private $createCallback;
    private $campaign;

    public function __construct()
    {
        $this->apiSendUrl = $this->getRequiredEnv('SMS_API_SEND_URL');
        $this->apiBulkSendUrl = $this->getRequiredEnv('SMS_API_BULK_URL');
        $this->apiToken = $this->getRequiredEnv('SMS_API_TOKEN');
        $this->from = getenv('SMS_FROM_ID');
        $this->from = $this->from === false ? '' : $this->from;
        $this->sender = $this->getRequiredEnv('SMS_SENDER_ID');
        $this->callback = getenv('SMS_CALLBACK_URL') ?: '';
        $this->statusCallback = getenv('SMS_STATUS_CALLBACK') ?: '';
        $this->createCallback = getenv('SMS_CREATE_CALLBACK') ?: '';
        $this->campaign = getenv('SMS_DEFAULT_CAMPAIGN') ?: 'Fist campain';
    }

    private function getRequiredEnv($key)
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            throw new RuntimeException("Environment variable {$key} is not set.");
        }

        return $value;
    }

    public function sendSms($tt,$mm){ /** We use php cURL for the samples **/

 /** We use php cURL for the samples **/
    $ch = curl_init();
    // base url
        $url = $this->apiSendUrl;
        $token = $this->apiToken;
    $from = $this->from;
    $sender = $this->sender;
        $to = $tt;
        $message = $mm;
        $callback = $this->callback;
    // request body
        $body = array("from" => $from,"sender" => $sender,"to" => $to,"message" => $message,"callback"=>$callback);
	
    /** configure request **/
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    /** request headers **/
	$headers = array();
	$headers[] = 'Authorization: Bearer '.$token;
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // send request
	$result = curl_exec($ch);

    /** handle response **/
	if (curl_errno($ch)) {
        /** general http error **/
		echo 'Error:' . curl_error($ch);
    } else {	
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		switch ($http_code) {
	    case 200:
                /** Inpsect `acknowledge` node and see if response is error or success **/
				$data = json_decode($result,true);
				if ($data['acknowledge'] == 'success') {
				//	echo "Api success";
                }else{
				//	echo "Api failure";
                }
				break;
	    default:
          /** Other API error ... mostly authorization related. Inpsect response body for details. **/
	      //echo 'Other HTTP Code: ', $http_code;
        }
    }
    /** finish **/
	curl_close ($ch);
    }
    
    
    
     public function sendBulkSms($tt,$mm){
             /** We use php cURL for the samples **/
    $ch = curl_init();
    // base url
        $url = $this->apiBulkSendUrl;
        $token = $this->apiToken;
    $from = $this->from;
    $sender = $this->sender;
	$to = ['+251943080871','+251930694101','+251979149445','+251921923976','+251921538686','+251984722935','+251979300009','+251932219305','+2519941080475','+251951075409','+251943090921','+251911771486','+251922449334'];
	$message = 'ከጥቂት ሰዓት ቦኋላ ወደ ሳሪስ እቃ እናደርሳለን። የምትፈልጉት እቃ ካለ ማዘዝ ትችላላቹ።';   
        $camp = $this->campaign;
    $scb = $this->statusCallback;
    $ccb = $this->createCallback;
    // request body
	$body = array("from" => $from,"sender" => $sender,"to" => $to,"message" => $message,"campaign"=>$camp,"statusCallback"=>$scb,"createCallback"=>$ccb);
	
    /** configure request **/
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    /** request headers **/
	$headers = array();
        $headers[] = 'Authorization: Bearer '.$token;
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // send request
	$result = curl_exec($ch);

    /** handle response **/
	if (curl_errno($ch)) {
        /** general http error **/
		echo 'Error:' . curl_error($ch);
    } else {	
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		switch ($http_code) {
	    case 200:
                /** Inpsect `acknowledge` node and see if response is error or success **/
				$data = json_decode($result,true);
				if ($data['acknowledge'] == 'success') {
					echo "Api success";
                }else{
					echo "Api failure";
                }
				break;
	    default:
          /** Other API error ... mostly authorization related. Inpsect response body for details. **/
	      echo 'Other HTTP Code: ', $http_code;
        }
    }
    /** finish **/
	curl_close ($ch);
     }
     
     public function addressBasedSms($phones,$m){
         
             /** We use php cURL for the samples **/
    $ch = curl_init();
    // base url
        $url = $this->apiBulkSendUrl;
        $token = $this->apiToken;
    $from = $this->from;
    $sender = $this->sender;
	$to = $phones;
	$message = $m;  
        $camp = $this->campaign;
    $scb = $this->statusCallback;
    $ccb = $this->createCallback;
    // request body
	$body = array("from" => $from,"sender" => $sender,"to" => $to,"message" => $message,"campaign"=>$camp,"statusCallback"=>$scb,"createCallback"=>$ccb);
	
    /** configure request **/
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    /** request headers **/
	$headers = array();
	$headers[] = 'Authorization: Bearer '.$token;
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // send request
	$result = curl_exec($ch);

    /** handle response **/
	if (curl_errno($ch)) {
        /** general http error **/
		echo 'Error:' . curl_error($ch);
    } else {	
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		switch ($http_code) {
	    case 200:
                /** Inpsect `acknowledge` node and see if response is error or success **/
				$data = json_decode($result,true);
				if ($data['acknowledge'] == 'success') {
					echo "Api success";
                }else{
					echo "Api failure";
                }
				break;
	    default:
          /** Other API error ... mostly authorization related. Inpsect response body for details. **/
	      echo 'Other HTTP Code: ', $http_code;
        }
    }
    /** finish **/
	curl_close ($ch);
     
     }
    
}



?>