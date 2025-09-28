<?php

class Chat{


    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function sendMessage($idFromUser,$userId,$adminId,$content,$isReceivedFromUser){

        $query = "INSERT INTO chat (id,id_from_user, user_id, admin_id, is_received_from_user, content, sendAt, is_read) VALUES (NULL,'$idFromUser','$userId', '$adminId', '$isReceivedFromUser', '$content', current_timestamp(), '0')";
        $stmt = $this->conn->exec($query);

    }
    public function getAllMessage($userId){
        //for is_executable
       $query = "SELECT * FROM chat WHERE user_id = $userId";
       $stmt = $this->conn->query($query);
       $stmt->execute();

       $num = $stmt->rowCount();

       $allChats = array();
       if($num>0){
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          extract($row);
          $eachChat = array(
            "id" => $row["id_from_user"],
            "serverId" => $row["id"],
            "adminId" => $row["admin_id"],
            "userId" => $row["user_id"],
            "content" => $row["content"],
            "isSender" => $row["is_received_from_user"],
          );
          array_push($allChats,$eachChat);
        }
       }
       return $allChats;
    }

    public function isMessageAvailibilityInServer($userId,$idFromUser){
        $query = "SELECT * FROM chat WHERE user_id = $userId AND id_from_user = $idFromUser";
        $stmt = $this->conn->query($query);
        $stmt->execute();
 
        $num = $stmt->rowCount();

        if($num>0){
            return true;
        }else{
            return false;
        }

    }
}








?>