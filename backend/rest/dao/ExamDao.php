<?php

class ExamDao {

    private $conn;

    /**
     * constructor of dao class
     */
    public function __construct(){
        $servername = "127.0.0.1";  // Use IP instead of localhost to force TCP
        $username = "root";
        $password = "";  // XAMPP default MySQL has no password
        $dbname = "webprep";
        $port = 3306;
        // TODO: List parameters such as servername, username, password, schema. Make sure to use appropriate port
        // TODO: Create new connection
        try {
            $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            error_log("Connected successfully to database: $dbname");
        } catch(PDOException $e){
            error_log("Connection failed: ".$e->getMessage());
            $this->conn = null;
        }
    }

    /** TODO
     * Implement DAO method used to get customer information
     */
    public function get_customers(){
        // TODO: Implement
        if ($this->conn === null) {
            throw new Exception("Database connection failed");
        }
        $stmt = $this->conn->query("SELECT * FROM customers");
        return $stmt ->fetchAll(PDO::FETCH_ASSOC);
    }

    /** TODO
     * Implement DAO method used to get customer meals
     */
    public function get_customer_meals($customer_id) {
        if ($this->conn === null) {
            throw new Exception("Database connection failed");
        }
        $stmt = $this->conn->prepare("SELECT f.name as food_name, f.brand as food_brand, m.created_at as meal_date FROM meals m INNER JOIN foods f ON m.food_id = f.id WHERE m.customer_id = :customer_id ORDER BY m.created_at DESC");
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();
        return $stmt ->fetchAll(PDO::FETCH_ASSOC);
    }

    /** TODO
     * Implement DAO method used to save customer data
     */
    public function add_customer($data){
        // TODO: Implement
        if ($this->conn === null) {
            throw new Exception("Database connection failed");
        }
        $stmt = $this->conn->prepare("INSERT INTO customers(first_name, last_name, birth_date) VALUES (:first_name, :last_name, :birth_date)");
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':birth_date', $data['birth_date']);
        $stmt->execute();
        $id = $this->conn->lastInsertId();
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** TODO
     * Implement DAO method used to get foods report
     */
public function get_foods_report($page = 1, $limit = 10){
    if ($this->conn === null) {
        throw new Exception("Database connection failed");
    }
    
    $offset = ($page - 1) * $limit;
    
    $stmt = $this->conn->prepare("
        SELECT 
            f.id,
            f.name, 
            f.brand, 
            f.image_url,
            MAX(CASE WHEN n.name = 'Energy' THEN fn.quantity END) as energy,
            MAX(CASE WHEN n.name = 'Protein' THEN fn.quantity END) as protein,
            MAX(CASE WHEN n.name = 'Fat' THEN fn.quantity END) as fat,
            MAX(CASE WHEN n.name = 'Fiber' THEN fn.quantity END) as fiber,
            MAX(CASE WHEN n.name = 'Carbs' THEN fn.quantity END) as carbs
        FROM foods f
        LEFT JOIN food_nutrients fn ON f.id = fn.food_id
        LEFT JOIN nutrients n ON fn.nutrient_id = n.id
        GROUP BY f.id, f.name, f.brand, f.image_url
        ORDER BY f.name 
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>
