<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "techhub";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed. Check database.php and make sure the techhub database exists.");
}


define('BASE_URL', '/Activity2finaltech');
