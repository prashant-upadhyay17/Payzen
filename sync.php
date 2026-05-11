<?php
// sync.php
require 'db_config.php';

// Disable HTML warnings from breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? '';

if ($action === 'get_all') {
    // Admin pulls everything
    $res = $conn->query("SELECT * FROM employees");
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $key = strtolower($row['code']);
        
        $data_parsed = json_decode($row['data_json'] ?? '{}', true);
        if (!is_array($data_parsed)) $data_parsed = [];

        $data[$key] = [
            "name" => $row['name'] ?? '-',
            "code" => $row['code'] ?? '',
            "designation" => $row['designation'] ?? '-',
            "gross" => $row['gross'] ?? 0,
            "leaves" => $row['leaves'] ?? 0,
            "data" => $data_parsed
        ];
    }
    echo json_encode(["status" => "success", "db" => $data]);
    exit;
}

if ($action === 'get_user') {
    $code = $_GET['code'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM employees WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $data_parsed = json_decode($row['data_json'] ?? '{}', true);
        if (!is_array($data_parsed)) $data_parsed = [];

        $employee = [
            "name" => $row['name'] ?? '-',
            "code" => $row['code'] ?? '',
            "designation" => $row['designation'] ?? '-',
            "gross" => $row['gross'] ?? 0,
            "leaves" => $row['leaves'] ?? 0,
            "data" => $data_parsed
        ];
        echo json_encode(["status" => "success", "employee" => $employee]);
    } else {
        echo json_encode(["status" => "error", "message" => "Not found"]);
    }
    exit;
}

if ($action === 'save') {
    // Admin saves an employee
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) exit;

    $code = $input['code'];
    $name = $input['name'];
    $designation = $input['designation'];
    $gross = (float)($input['gross'] ?? 0);
    $leaves = (float)($input['leaves'] ?? 0);
    $data_json = json_encode($input['data'] ?? []);

    // Upsert
    $stmt = $conn->prepare("
        INSERT INTO employees (code, name, designation, gross, leaves, data_json) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name), 
            designation = VALUES(designation),
            gross = VALUES(gross),
            leaves = VALUES(leaves),
            data_json = VALUES(data_json)
    ");
    $stmt->bind_param("sssdss", $code, $name, $designation, $gross, $leaves, $data_json);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}
?>
