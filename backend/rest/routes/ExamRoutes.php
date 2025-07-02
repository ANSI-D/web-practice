<?php

Flight::route('GET /connection-check', function(){
    // This endpoint tests the database connection with a simple query
    try {
        $dao = new ExamDao();
        // Test connection with the simplest possible query
        $result = $dao->testConnection();
        if ($result) {
            echo "Connected successfully";
        } else {
            echo "Connection failed: Database connection is null";
        }
    } catch (Exception $e) {
        echo "Connection failed: " . $e->getMessage();
    }
});

Flight::route('GET /customers', function(){
    $service = new ExamService();
    Flight::json($service->get_customers());
});

Flight::route('GET /customer/meals/@customer_id', function($customer_id){
    $service = new ExamService();
    Flight::json($service->get_customer_meals($customer_id));
});

Flight::route('POST /customers/add', function() {
    $data = Flight::request()->data->getData();
    $service = new ExamService();
    Flight::json($service->add_customer($data));
});

Flight::route('GET /foods/report', function(){
    $service = new ExamService();
    $page = Flight::request()->query['page'] ?? 1;
    $limit = Flight::request()->query['limit'] ?? 10;
    Flight::json($service->foods_report((int)$page, (int)$limit));
});

?>
