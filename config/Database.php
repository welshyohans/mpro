<?php 


class Database{

    private $host;
    private $db_name;
    private $username;
    private $password;

    private $conn;

    public function __construct()
    {
        $this->host = $this->getRequiredEnv('DB_HOST');
        $this->db_name = $this->getRequiredEnv('DB_NAME');
        $this->username = $this->getRequiredEnv('DB_USERNAME');
        $this->password = $this->getRequiredEnv('DB_PASSWORD');
    }

    private function getRequiredEnv($key)
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            throw new RuntimeException("Environment variable {$key} is not set.");
        }

        return $value;
    }

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