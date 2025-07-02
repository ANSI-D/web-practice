<?php

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authentication, Authorization, X-API-Key");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require "../vendor/autoload.php";
require "./dao/ExamDao.php";
require "./services/ExamService.php";
require "./middleware/AuthMiddleware.php";

Flight::register('examService', 'ExamService');

require 'routes/ExamRoutes.php';

Flight::start();
 ?>
