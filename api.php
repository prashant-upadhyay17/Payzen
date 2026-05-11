<?php
// api.php - Secure HRM API

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); exit();
}

error_reporting(E_ALL); ini_set('display_errors', 0);

require ('db_config.php');
$conn->set_charset("utf8mb4");

$action = $_GET['action'] ?? '';

// ─── HELPERS ─────────────────────────────────────────────────────────────────

function addLog($conn, $msg) {
    $res = $conn->query("SELECT logs_json FROM admins WHERE id = 1");
    if ($res && $row = $res->fetch_assoc()) {
        $logs = json_decode($row['logs_json'] ?? '[]', true) ?: [];
        array_unshift($logs, ["time" => date('Y-m-d H:i:s'), "message" => $msg]);
        $logs = array_slice($logs, 0, 100);
        $stmt = $conn->prepare("UPDATE admins SET logs_json = ? WHERE id = 1");
        $j = json_encode($logs);
        $stmt->bind_param("s", $j);
        $stmt->execute();
    }
}

function hashPassword($pwd) {
    return password_hash($pwd, PASSWORD_BCRYPT);
}

function isStrongPassword($pwd) {
    if (strlen($pwd) < 8) return false;
    if (!preg_match('/[A-Z]/', $pwd)) return false;
    if (!preg_match('/[0-9]/', $pwd)) return false;
    if (!preg_match('/[\W_]/', $pwd)) return false;
    return true;
}

// ─── LOGIN ────────────────────────────────────────────────────────────────────
if ($action === 'login') {
    $input = json_decode(file_get_contents("php://input"), true);
    $loginId = trim($input['login_id'] ?? '');
    $pwd     = $input['password'] ?? '';
    $type    = $input['type'] ?? 'admin';

    if (empty($loginId) || empty($pwd)) {
        echo json_encode(["status" => "error", "message" => "Login ID and Password are required"]); exit;
    }

    if ($type === 'admin') {
        $stmt = $conn->prepare("SELECT a.*, r.role_name FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.email = ? AND a.is_deleted = 'N'");
        $stmt->bind_param("s", $loginId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            if ($row['is_active'] !== 'Y') {
                echo json_encode(["status" => "error", "message" => "Your admin account is inactive."]); exit;
            }
            if (password_verify($pwd, $row['password'])) {
                $row['password'] = null;
                addLog($conn, "Admin login: " . $row['email']);
                echo json_encode(["status" => "success", "user" => $row]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid Login ID or Password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid Login ID or Password"]);
        }
    } else {
        $stmt = $conn->prepare("SELECT e.*, r.role_name FROM employees e JOIN roles r ON e.role_id = r.id WHERE e.email = ? AND e.is_deleted = 'N'");
        $stmt->bind_param("s", $loginId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) {
            echo json_encode(["status" => "error", "message" => "Invalid Email or Password"]); exit;
        }
        if ($row['is_active'] !== 'Y') {
            echo json_encode(["status" => "error", "message" => "Your account has been deactivated. Contact your HR Admin."]); exit;
        }
        if (password_verify($pwd, $row['password'])) {
            $row['password'] = null;
            $row['salary_config_json'] = json_decode($row['salary_config_json'] ?? '{}', true);
            $row['payslips_json'] = json_decode($row['payslips_json'] ?? '[]', true);
            addLog($conn, "Employee login: " . $row['email']);
            echo json_encode(["status" => "success", "user" => $row]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid Email or Password"]);
        }
    }
    exit;
}

// ─── GET EMPLOYEES ────────────────────────────────────────────────────────────
if ($action === 'get_employees') {
    $res = $conn->query("SELECT e.*, d.designation_name FROM employees e LEFT JOIN designation d ON e.designation = d.id WHERE e.is_deleted = 'N' ORDER BY e.emp_code");
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $row['password'] = null;
        $row['salary_config_json'] = json_decode($row['salary_config_json'] ?? '{}', true);
        $row['payslips_json'] = json_decode($row['payslips_json'] ?? '[]', true);
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}

// ─── GET DESIGNATIONS ─────────────────────────────────────────────────────────
if ($action === 'get_designations') {
    $res = $conn->query("SELECT id, designation_name FROM designation WHERE active = 'Y' ORDER BY id");
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}

// ─── GET SINGLE EMPLOYEE ──────────────────────────────────────────────────────
if ($action === 'get_employee') {
    $code = $_GET['code'] ?? '';
    $stmt = $conn->prepare("SELECT e.*, d.designation_name FROM employees e LEFT JOIN designation d ON e.designation = d.id WHERE e.emp_code = ? AND e.is_deleted = 'N'");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    if ($row = $stmt->get_result()->fetch_assoc()) {
        $row['password'] = null;
        $row['salary_config_json'] = json_decode($row['salary_config_json'] ?? '{}', true);
        $row['payslips_json'] = json_decode($row['payslips_json'] ?? '[]', true);
        echo json_encode(["status" => "success", "data" => $row]);
    } else {
        echo json_encode(["status" => "error", "message" => "Not found"]);
    }
    exit;
}

// ─── SAVE EMPLOYEE ────────────────────────────────────────────────────────────
if ($action === 'save_employee') {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) { echo json_encode(["status"=>"error","message"=>"Invalid input"]); exit; }

    $code   = trim($input['emp_code'] ?? '');
    $email  = trim($input['email'] ?? '');
    $fn     = trim($input['first_name'] ?? '');
    $ln     = trim($input['last_name'] ?? '');
    $desig  = trim($input['designation'] ?? '');
    $pkg    = (float)($input['package'] ?? 0);
    $gross  = (float)($input['gross_monthly'] ?? 0);
    $leaves = (float)($input['paid_leaves_taken'] ?? 0);
    $config = json_encode($input['salary_config_json'] ?? []);
    $isActive = ($input['is_active'] ?? 'Y') === 'Y' ? 'Y' : 'N';

    $payslipsJson = isset($input['payslips_json']) ? json_encode($input['payslips_json']) : null;

    if (empty($code) || empty($email) || empty($fn) || empty($ln)) {
        echo json_encode(["status"=>"error","message"=>"Employee Code, Email, First Name and Last Name are required"]); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status"=>"error","message"=>"Invalid email format"]); exit;
    }
    if ($pkg <= 0 || $gross <= 0) {
        echo json_encode(["status"=>"error","message"=>"Package and Gross Monthly must be greater than 0"]); exit;
    }

    $chk = $conn->prepare("SELECT id FROM employees WHERE emp_code = ? AND is_deleted = 'N'");
    $chk->bind_param("s", $code);
    $chk->execute();
    $existing = $chk->get_result()->fetch_assoc();

    if ($existing) {
        if ($payslipsJson !== null) {
            $stmt = $conn->prepare("UPDATE employees SET email=?, first_name=?, last_name=?, designation=?, package=?, gross_monthly=?, paid_leaves_taken=?, salary_config_json=?, payslips_json=?, is_active=? WHERE emp_code=?");
            $stmt->bind_param("ssssdddssss", $email, $fn, $ln, $desig, $pkg, $gross, $leaves, $config, $payslipsJson, $isActive, $code);
        } else {
            $stmt = $conn->prepare("UPDATE employees SET email=?, first_name=?, last_name=?, designation=?, package=?, gross_monthly=?, paid_leaves_taken=?, salary_config_json=?, is_active=? WHERE emp_code=?");
            $stmt->bind_param("ssssdddsss", $email, $fn, $ln, $desig, $pkg, $gross, $leaves, $config, $isActive, $code);
        }
        $stmt->execute();
        addLog($conn, "Updated employee: " . $code);
    } else {
        $newPwd = trim($input['password'] ?? '');
        if (empty($newPwd)) $newPwd = 'Emp@12345';
        $hashedPwd = hashPassword($newPwd);
        $stmt = $conn->prepare("INSERT INTO employees (role_id, emp_code, email, password, first_name, last_name, designation, package, gross_monthly, paid_leaves_taken, salary_config_json, payslips_json, is_active, is_deleted) VALUES (3, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '[]', 'Y', 'N')");
        $stmt->bind_param("ssssssddds", $code, $email, $hashedPwd, $fn, $ln, $desig, $pkg, $gross, $leaves, $config);
        $stmt->execute();
        addLog($conn, "Added new employee: " . $code);
    }
    echo json_encode(["status" => "success"]);
    exit;
}

// ─── SOFT DELETE EMPLOYEE ─────────────────────────────────────────────────────
if ($action === 'delete_employee') {
    $code = $_GET['code'] ?? '';
    if (empty($code)) { echo json_encode(["status"=>"error","message"=>"No code provided"]); exit; }
    $stmt = $conn->prepare("UPDATE employees SET is_deleted='Y', is_active='N' WHERE emp_code=?");
    $stmt->bind_param("s", $code);
    if ($stmt->execute()) {
        addLog($conn, "Soft-deleted employee: " . $code);
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}

// ─── SAVE PAYSLIP ─────────────────────────────────────────────────────────────
if ($action === 'save_payslip') {
    $input = json_decode(file_get_contents("php://input"), true);
    $code  = $input['emp_code'] ?? '';
    $month = $input['month'] ?? '';
    $year  = $input['year'] ?? '';

    $stmt = $conn->prepare("SELECT payslips_json FROM employees WHERE emp_code = ? AND is_deleted='N'");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    if ($row = $stmt->get_result()->fetch_assoc()) {
        $slips = json_decode($row['payslips_json'] ?? '[]', true) ?: [];
        $slips = array_filter($slips, function($s) use ($month, $year) {
            return !($s['month'] === $month && (string)$s['year'] === (string)$year);
        });
        $slips[] = [
            "month"           => $month, "year"  => $year,
            "generated_date"  => date('Y-m-d H:i:s'),
            "gross"           => $input['gross'] ?? 0,
            "basic"           => $input['basic'] ?? 0,
            "da"              => $input['da'] ?? 0,
            "conveyance"      => $input['conveyance'] ?? 0,
            "medical"         => $input['medical'] ?? 0,
            "special"         => $input['special'] ?? 0,
            "pf"              => $input['pf'] ?? 0,
            "esi"             => $input['esi'] ?? 0,
            "pt"              => $input['pt'] ?? 0,
            "leave_deduction" => $input['leave_deduction'] ?? 0,
            "total_deductions"=> $input['total_deductions'] ?? 0,
            "net_salary"      => $input['net_salary'] ?? 0,
            "employer_pf"     => $input['employer_pf'] ?? 0,
            "employer_esi"    => $input['employer_esi'] ?? 0,
            "ctc"             => $input['ctc'] ?? 0,
            "status"          => "Paid"
        ];
        $j = json_encode(array_values($slips));
        $upd = $conn->prepare("UPDATE employees SET payslips_json = ? WHERE emp_code = ?");
        $upd->bind_param("ss", $j, $code);
        $upd->execute();
        addLog($conn, "Payslip generated for " . $code . " ($month $year)");
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Employee not found"]);
    }
    exit;
}

// ─── GET LOGS ─────────────────────────────────────────────────────────────────
if ($action === 'get_logs') {
    $res = $conn->query("SELECT logs_json FROM admins WHERE id = 1");
    if ($row = $res->fetch_assoc()) {
        echo json_encode(["status" => "success", "data" => json_decode($row['logs_json'] ?? '[]', true)]);
    }
    exit;
}

// ─── GET USERS (ADMINS) ───────────────────────────────────────────────────────
if ($action === 'get_users') {
    $res = $conn->query("SELECT a.id, a.email, a.first_name, a.last_name, a.role_id, a.is_active, r.role_name FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.is_deleted = 'N'");
    $data = [];
    while ($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}

// ─── SAVE USER (ADMIN) ────────────────────────────────────────────────────────
if ($action === 'save_user') {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) { echo json_encode(["status"=>"error","message"=>"Invalid input"]); exit; }

    $id    = $input['id'] ?? null;
    $fn    = trim($input['first_name'] ?? '');
    $ln    = trim($input['last_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $role  = (int)($input['role_id'] ?? 2);
    $isActive = ($input['is_active'] ?? 'Y') === 'Y' ? 'Y' : 'N';

    if (empty($fn) || empty($ln) || empty($email)) {
        echo json_encode(["status"=>"error","message"=>"All fields are required"]); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status"=>"error","message"=>"Invalid email format"]); exit;
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE admins SET first_name=?, last_name=?, email=?, role_id=?, is_active=? WHERE id=?");
        $stmt->bind_param("sssisi", $fn, $ln, $email, $role, $isActive, $id);
        $stmt->execute();
        addLog($conn, "Updated admin user: " . $email);
    } else {
        $rawPwd = trim($input['password'] ?? '');
        if (empty($rawPwd)) {
            echo json_encode(["status"=>"error","message"=>"Password is required for new users"]); exit;
        }
        if (!isStrongPassword($rawPwd)) {
            echo json_encode(["status"=>"error","message"=>"Password must be at least 8 characters with uppercase, number and special character"]); exit;
        }
        $hashedPwd = hashPassword($rawPwd);
        $stmt = $conn->prepare("INSERT INTO admins (role_id, email, password, first_name, last_name, logs_json, is_active, is_deleted) VALUES (?, ?, ?, ?, ?, '[]', 'Y', 'N')");
        $stmt->bind_param("issss", $role, $email, $hashedPwd, $fn, $ln);
        $stmt->execute();
        addLog($conn, "Added admin user: " . $email);
    }
    echo json_encode(["status" => "success"]);
    exit;
}

// ─── SOFT DELETE USER (ADMIN) ─────────────────────────────────────────────────
if ($action === 'delete_user') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id === 1) { echo json_encode(["status"=>"error","message"=>"Cannot delete Master Admin"]); exit; }
    $stmt = $conn->prepare("UPDATE admins SET is_deleted='Y', is_active='N' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        addLog($conn, "Soft-deleted admin ID: " . $id);
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit;
}

// ─── MONTHLY REPORT ───────────────────────────────────────────────────────────
if ($action === 'get_monthly_report') {
    $month = $_GET['month'] ?? '';
    $year  = $_GET['year'] ?? '';
    $res   = $conn->query("SELECT emp_code, first_name, last_name, gross_monthly, payslips_json FROM employees WHERE is_deleted='N' ORDER BY emp_code");
    $data  = [];
    while ($row = $res->fetch_assoc()) {
        $slips  = json_decode($row['payslips_json'] ?? '[]', true) ?: [];
        $status = 'Pending'; $net = '0';
        foreach ($slips as $s) {
            if ($s['month'] === $month && (string)$s['year'] === (string)$year) {
                $status = 'Paid'; $net = $s['net_salary']; break;
            }
        }
        $data[] = ["emp_code" => $row['emp_code'], "name" => $row['first_name'] . ' ' . $row['last_name'],
                   "gross" => $row['gross_monthly'], "net" => $net, "status" => $status];
    }
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}

// ─── UPDATE PASSWORD ──────────────────────────────────────────────────────────
if ($action === 'update_password') {
    $input  = json_decode(file_get_contents("php://input"), true);
    if (!$input) {
        echo json_encode(["status" => "error", "message" => "Invalid request data"]); exit;
    }

    $type   = $input['type'] ?? '';
    $loginId= trim($input['email'] ?? '');
    $newPwd = $input['new_password'] ?? '';

    if (empty($loginId)) {
        echo json_encode(["status" => "error", "message" => "Identifier (email/code) is missing"]); exit;
    }

    if (strlen($newPwd) < 8) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters"]); exit;
    }
    if (!isStrongPassword($newPwd)) {
        echo json_encode(["status" => "error", "message" => "Password must contain uppercase, number and special character"]); exit;
    }

    $hashed = hashPassword($newPwd);
    if ($type === 'Admin') {
        $stmt = $conn->prepare("UPDATE admins SET password=? WHERE email=? AND is_deleted='N'");
        $stmt->bind_param("ss", $hashed, $loginId);
        addLog($conn, "Admin password updated: " . $loginId);
    } else {
        $stmt = $conn->prepare("UPDATE employees SET password=? WHERE emp_code=? AND is_deleted='N'");
        $stmt->bind_param("ss", $hashed, $loginId);
        addLog($conn, "Employee password updated: " . $loginId);
    }
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No record found to update or password is same as current"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Database execution failed: " . $stmt->error]);
    }
    exit;
}

/*
// ─── SEND EMAIL ───────────────────────────────────────────────────
if ($action === 'send_payslip_email') {

    require_once __DIR__ . '/mail/mail_helper.php';

    // Always return JSON
    header("Content-Type: application/json");

    // Read JSON input safely
    $rawInput = file_get_contents("php://input");
    $input = json_decode($rawInput, true);

    if (!is_array($input)) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
        exit;
    }

    $emp_code = trim($input['emp_code'] ?? '');
    $month    = trim($input['month'] ?? '');
    $year     = trim($input['year'] ?? '');

    if ($emp_code === '' || $month === '' || $year === '') {
        echo json_encode(["status" => "error", "message" => "Employee, month and year are required"]);
        exit;
    }

    // DB safety check
    if (!isset($conn) || !$conn) {
        echo json_encode(["status" => "error", "message" => "Database connection failed"]);
        exit;
    }

    // Fetch employee safely
    $stmt = $conn->prepare("
        SELECT first_name, last_name, email 
        FROM employees 
        WHERE emp_code = ? AND is_deleted='N'
        LIMIT 1
    ");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "DB prepare failed"]);
        exit;
    }

    $stmt->bind_param("s", $emp_code);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "DB execute failed"]);
        exit;
    }

    $stmt->bind_result($first_name, $last_name, $email);

    if (!$stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Employee not found"]);
        exit;
    }

    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Employee email not found"]);
        exit;
    }

    $empName = trim($first_name . ' ' . $last_name);

    // Send email
    $mailStatus = sendPayslipEmail($email, $empName, $month, $year);

    if ($mailStatus === true) {

        // Safe logging (won’t break API)
        if (function_exists('addLog')) {
            try {
                addLog($conn, "Payslip Email sent: $month $year → $email ($emp_code)");
            } catch (Throwable $e) {
                // ignore logging errors
            }
        }

        echo json_encode([
            "status" => "success",
            "message" => "Payslip email sent to $email"
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => $mailStatus
        ]);
    }

    exit;
} */


// ─── SEND EMAIL ───────────────────────────────────────────────────
if ($action === 'send_payslip_email') {

    require_once __DIR__ . '/mail/mail_helper.php';

    header("Content-Type: application/json");

    // 🔥 Detect request type (FormData OR JSON)
    $isFileUpload = isset($_FILES['file']);

    if ($isFileUpload) {
        // ✅ From html2pdf (FormData)
        $emp_code = trim($_POST['emp_code'] ?? '');
        $month    = trim($_POST['month'] ?? '');
        $year     = trim($_POST['year'] ?? '');
        $pdfPath  = $_FILES['file']['tmp_name'];

    } else {
        // ✅ Fallback (old JSON flow)
        $rawInput = file_get_contents("php://input");
        $input = json_decode($rawInput, true);

        if (!is_array($input)) {
            echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
            exit;
        }

        $emp_code = trim($input['emp_code'] ?? '');
        $month    = trim($input['month'] ?? '');
        $year     = trim($input['year'] ?? '');
        $pdfPath  = null; // ❌ no PDF in this mode
    }

    // 🔴 Validation
    if ($emp_code === '' || $month === '' || $year === '') {
        echo json_encode(["status" => "error", "message" => "Employee, month and year are required"]);
        exit;
    }

    if (!isset($conn) || !$conn) {
        echo json_encode(["status" => "error", "message" => "Database connection failed"]);
        exit;
    }

    // 🔍 Fetch employee
    $stmt = $conn->prepare("
        SELECT first_name, last_name, email 
        FROM employees 
        WHERE emp_code = ? AND is_deleted='N'
        LIMIT 1
    ");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "DB prepare failed"]);
        exit;
    }

    $stmt->bind_param("s", $emp_code);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "DB execute failed"]);
        exit;
    }

    $stmt->bind_result($first_name, $last_name, $email);

    if (!$stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Employee not found"]);
        exit;
    }

    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Employee email not found"]);
        exit;
    }

    $empName = trim($first_name . ' ' . $last_name);

    // 🔥 Send email (with or without PDF)
    if ($pdfPath) {
        $mailStatus = sendPayslipEmail($email, $empName, $month, $year, $pdfPath);
    } else {
        $mailStatus = sendPayslipEmail($email, $empName, $month, $year, null);
    }

    if ($mailStatus === true) {

        // Optional logging
        if (function_exists('addLog')) {
            try {
                addLog($conn, "Payslip Email sent: $month $year → $email ($emp_code)");
            } catch (Throwable $e) {}
        }

        echo json_encode([
            "status" => "success",
            "message" => $pdfPath ? "Payslip email sent with PDF" : "Payslip email sent"
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => $mailStatus
        ]);
    }

    exit;
}

?>
