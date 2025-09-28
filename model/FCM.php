<?php


class FCM{

    public function sendFCM($token,$title,$body,$isNotifable,$notificationDetail){
        define( 'API_ACCESS_KEY', 'AAAAEFJllvY:APA91bEWCT1ho5332ZSKNqQr3q_QIATqhelXX4KXI1wiu-am2y3OOO_udT_Fw_RPPvGbH11RB37GpJp22EID_0OBdD2C__8XxM8zeMAf1f91J7t7GkB7X92SDPHI8TE28wvSvVK-LV1_' ); // get API access 

        
     $registrationIds = array();

     array_push($registrationIds,$token);


        $toData = array(
            'body'=> $body
        );
        $msg;
        
        $toNotification = array
        (
                'body' => $body,
                'title' => $title
        );
        //for testing
        $datapayload = array(
            'notificationDetail'=>$notificationDetail
        );
        if($isNotifable == 1){
            $msg = $toNotification;
        }else{
            $msg = $toData;
        }
        
        $fieldsNotification = array
        (
            'registration_ids' => $registrationIds,
            'notification' => $msg,
            'data'=> $datapayload
        );
        $fieldsData = array
        (
            'registration_ids' => $registrationIds,
            'data' => $msg
        );
        
        
        $fields;
        if($isNotifable == 1){
            $fields =$fieldsNotification;
        }else{
            $fields = $fieldsData;
        }
        
        
        $headers = array
        (
            'Authorization: key='.API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'https://fcm.googleapis.com/fcm/send'); //For firebase, use https://fcm.googleapis.com/fcm/send
        
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}





?>