<?php 


class Database{

    //db params
   // private $host = 'localhost';
   // private $db_name = 'ngus_merkato';
    //private $username = 'root';
    //private $password = '';

        //db params
        private $host = '109.70.148.60';
        private $db_name = 'kulushrk_ngus_merkato';
        private $username = 'kulushrk_welsh';
        private $password = 'w#t59Ud13';

    private $conn;

    public function connect(){

        try{
            $dsn="mysql:host=".$this->host.";dbname=".$this->db_name.";charset=utf8";
            $this->conn = new PDO($dsn,$this->username,$this->password);
            //$this->conn = new PDO('mysql:host = '.$this->host.';dbname = ' . $this->db_name.';charset=utf8',$this->username,$this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "connected sucessfully...";
        }catch(PDOException $e){
            echo "Error connection" . $e->getMessage();
        }
        //echo "hello world";
        return $this->conn;

    }

}



?>