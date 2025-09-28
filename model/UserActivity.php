<?php

class UserActivity{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function updateUserLastCode($userId,$lastUpdateCode){
        $query = "UPDATE user_last_update SET last_update_code = '$lastUpdateCode',is_proved = '1' WHERE user_last_update.user_id = '$userId'";
        $stmt = $this->conn->exec($query);
    }
    public function insertUserActivity($userId,$userPhoneTime,$activityType,$insertTime,$additionalInfo){
        $query = "INSERT INTO user_activity (id, user_id, user_phone_time, server_time, activity_type, inserted_time, additional_info) VALUES (NULL, '$userId', '$userPhoneTime', current_timestamp(), '$activityType', '$insertTime', '$additionalInfo')";
        $stmt = $this->conn->exec($query);
    }
}


?>