<?php
// db_config.php

// Allow CORS so the Desktop HTML files can talk to this local API
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = 'localhost';

// Detect environment based on domain
if ($_SERVER['HTTP_HOST'] == "payzen.in" || $_SERVER['HTTP_HOST'] == "www.payzen.in")
{
    $db = 'db_name';
    $user = 'db_user';
    $pass = 'db_pass';
}

else
{
    // Localhost settings
    $db = "db_name";
    $user = "db_user";
    $pass = "db_pass";
    
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (\mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database Connection Failed"]);
    exit;
}
?>
