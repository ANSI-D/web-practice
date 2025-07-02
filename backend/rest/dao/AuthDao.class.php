<?php

class AuthDao {

    private $conn;

    /**
     * constructor of dao class
     */
    public function __construct(){
        $servername = "localhost";
        $username = "root";
        $password = "";  // XAMPP default MySQL has no password
        $dbname = "webprep";
        $port = 3306;
        
        try {
            $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e){
            error_log("Connection failed: ".$e->getMessage());
            $this->conn = null;
        }
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>