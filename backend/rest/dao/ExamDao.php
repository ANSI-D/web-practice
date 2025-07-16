<?php

class ExamDao
{

  private $conn;

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    try {
      $host = 'db1.ibu.edu.ba';
      $dbname = 'webfinal_db_3006';
      $username = 'webfinal_db_user2';
      $password = 'webFinal3006';
      $port = 3306;

      $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            error_log("Connected successfully to database: $dbname");
        } catch(PDOException $e){
            error_log("Connection failed: ".$e->getMessage());
            $this->conn = null;
        }
      /** TODO
       * List parameters such as servername, username, password, schema. Make sure to use appropriate port
       */

      /** TODO
       * Create new connection
       */
      echo "Connected successfully";
    } 
//    catch (PDOException $e) {
//      echo "Connemployees_performance_reportection failed: " . $e->getMessage();
//    }
//  }

  /** TODO
   * Implement DAO method used to get employees performance report
   */
public function employees_performance_report() {
    if ($this->conn === null) {
        throw new Exception("Database connection failed");
    }
    $stmt = $this->conn->query(
        "SELECT 
            e.employeeNumber, 
            CONCAT(e.firstName, ' ', e.lastName) as full_name,
            e.email, 
            SUM(od.orderNumber * od.quantityOrdered) as total
        FROM employees e
        LEFT JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber
        LEFT JOIN orders o USING(customerNumber)
        LEFT JOIN orderdetails od USING(orderNumber)
        GROUP BY e.employeeNumber"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

  

  /** TODO
   * Implement DAO method used to delete employee by id
   */
 public function delete_employee($employee_id) {
    try {
        // Disable foreign key checks
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        $stmt = $this->conn->prepare("DELETE FROM employees WHERE employeeNumber = :employeeNumber");
        $stmt->bindParam(':employeeNumber', $employee_id, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        // Re-enable foreign key checks
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        return $result;
    } catch (Exception $e) {
        // Make sure to re-enable foreign key checks even if there's an error
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        throw new Exception("Error deleting employee");
    }
}

  /** TODO
   * Implement DAO method used to edit employee data
   */
public function edit_employee($employee_id, $data) {

    $stmt = $this->conn->prepare("UPDATE employees SET first_name = :first_name, last_name = :last_name, email = :email WHERE employeeNumber = :id");
    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':id', $employee_id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt = $this->conn->prepare("SELECT * FROM employees WHERE employeeNumber = :id");
    $stmt->bindParam(':id', $employee_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
  

  /** TODO
   * Implement DAO method used to get orders report
   */
  public function get_orders_report() {
    $sql = "SELECT 
              o.orderNumber as order_number,
              SUM(od.quantityOrdered * od.priceEach) as total_amount
            FROM orders o
            JOIN orderdetails od ON o.orderNumber = od.orderNumber
            GROUP BY o.orderNumber";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  /** TODO
   * Implement DAO method used to get all products in a single order
   */
  public function get_order_details($order_id) {
    if ($this->conn === null) {
        throw new Exception("Database connection failed");
    }
    
    $stmt = $this->conn->prepare("SELECT p.productName as product_name, od.quantityOrdered as quantity, od.priceEach as price_each
        FROM products p
        JOIN orderdetails od USING(productCode)
        WHERE od.orderNumber = :orderNumber");
    $stmt->bindParam(':orderNumber', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


public function add_customer($data) {
    $sql = "INSERT INTO customers (customerNumber, customerName, contactLastName, contactFirstName, phone, addressLine1, addressLine2, city, state, postalCode, country, salesRepEmployeeNumber, creditLimit) VALUES (:customerNumber, :customerName, :contactLastName, :contactFirstName, :phone, :addressLine1, :addressLine2, :city, :state, :postalCode, :country, :salesRepEmployeeNumber, :creditLimit)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'customerNumber' => $data['customerNumber'],
      'customerName' => $data['customerName'],
      'contactLastName' => $data['contactLastName'],
      'contactFirstName' => $data['contactFirstName'],
      'phone' => $data['phone'],
      'addressLine1' => $data['addressLine1'],
      'addressLine2' => $data['addressLine2'],
      'city' => $data['city'],
      'state' => $data['state'],
      'postalCode' => $data['postalCode'],
      'country' => $data['country'],
      'salesRepEmployeeNumber' => $data['salesRepEmployeeNumber'],
      'creditLimit' => $data['creditLimit']
    ]);
    // Return the newly created customer
    $stmt = $this->conn->prepare("SELECT * FROM customers WHERE customerNumber = ?");
    $stmt->execute([$data['customerNumber']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}

// For testing stuff
//    if ($this->conn === null) {
//        throw new Exception("Database connection failed");
//    }
