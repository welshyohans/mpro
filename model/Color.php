<?php

class Color{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getAllColors(){

        $query = "SELECT * FROM color";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allColor = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                 $eachColor = array(
                    "colorId" => $id,
                    "name" => $name,
                    "colorCode" =>$color_code
                 );

                 array_push($allColor,$eachColor);
            }
        }
        return $allColor;
    }
    

}







?>