<?php
require_once __DIR__."/../dao/ExamDao.php";

class ExamService {
    protected $dao;

    public function __construct(){
        $this->dao = new ExamDao();
    }

    /** TODO
    * Implement service method used to get employees performance report
    */
    public function employees_performance_report(){
        return $this->dao->employees_performance_report();
    }

    /** TODO
    * Implement service method used to delete employee by id
    */
    public function delete_employee($employee_id){
        return $this->dao->delete_employee($employee_id);
    }
        public function get_employee($employee_id){
        return $this->dao->get_employee($employee_id);
    }

    /** TODO
    * Implement service method used to edit employee data
    */
    public function edit_employee($employee_id, $data){
        return $this->dao->edit_employee($employee_id, $data);
    }

    /** TODO
    * Implement service method used to get orders report
    */
    public function get_orders_report(){
        $orders = $this->dao->get_orders_report();
        
        // Add the details HTML field as required by the API specification
        foreach ($orders as &$order) {
            $order['details'] = '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#order-details-modal" onclick="showOrderDetails(' . $order['order_number'] . ')">Details</button>';
        }
        
        return $orders;
    }

    /** TODO
    * Implement service method used to get all products in a single order
    */
    public function get_order_details($order_id){
        return $this->dao->get_order_details($order_id);
    }


    /**
     * Add a new customer to the database
     */
    public function add_customer($data) {
        return $this->dao->add_customer($data);
    }
}