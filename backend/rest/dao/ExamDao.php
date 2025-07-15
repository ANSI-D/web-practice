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
      // Database connection parameters for XAMPP/LAMPP
      $servername = "localhost";
      $username = "root";
      $password = "";
      $db = "web_final";
      $port = "3306";

      // Create new PDO connection
      $this->conn = new PDO("mysql:host=$servername;port=$port;dbname=$db", $username, $password);
      // Set the PDO error mode to exception
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "Connected successfully";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  /** TODO
   * Implement DAO method used to get employees performance report
   */
  public function employees_performance_report() {
    $sql = "SELECT 
              e.employeeNumber as id,
              CONCAT(e.firstName, ' ', e.lastName) as full_name,
              e.email,
              SUM(p.amount) as total
            FROM employees e
            LEFT JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber
            LEFT JOIN payments p ON c.customerNumber = p.customerNumber
            GROUP BY e.employeeNumber
            order by total desc";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to delete employee by id
   */
  public function delete_employee($employee_id) {
    try {
      $sql = "DELETE FROM employees WHERE employeeNumber = :employee_id";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(['employee_id' => $employee_id]);
      return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
      // Return false for any database constraint issues
      return false;
    }
  }

   public function get_employee($employee_id) {
    try {
      $sql = "SELECT * FROM employees WHERE employeeNumber = :employee_id";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(['employee_id' => $employee_id]);
      return $stmt->fetch(PDO::FETCH_ASSOC); // <-- Return the employee data
    } catch (PDOException $e) {
      return false;
    }
  }

  /** TODO
   * Implement DAO method used to edit employee data
   */
  public function edit_employee($employee_id, $data) {
    $sql = "UPDATE employees SET firstName = ?, lastName = ?, email = ? WHERE employeeNumber = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $employee_id]);
    
    $sql = "SELECT employeeNumber as id, CONCAT(firstName, ' ', lastName) as full_name, email FROM employees WHERE employeeNumber = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$employee_id]);
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
    $sql = "SELECT 
              p.productName as product_name,
              od.quantityOrdered as quantity,
              od.priceEach as price_each
            FROM orderdetails od
            JOIN products p ON od.productCode = p.productCode
            WHERE od.orderNumber = ?";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
