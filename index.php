<?php
session_start();

$page = $_GET['page'] ?? 'login';

$validPages = [
    'login' => 'login.php',
    'admin_dashboard' => 'pages/admin_dashboard.php',
    'admin_employees' => 'pages/admin_employees.php',
    'admin_users' => 'pages/admin_users.php',
    'admin_payslips' => 'pages/admin_payslips.php',
    'admin_logs' => 'pages/admin_logs.php',
    'admin_account' => 'pages/admin_account.php',
    'employee_dashboard' => 'pages/employee_dashboard.php',
    'employee_payslips' => 'pages/employee_payslips.php',
    'employee_logs' => 'pages/employee_logs.php',
    'employee_account' => 'pages/employee_account.php'
];

if (!array_key_exists($page, $validPages)) {
    $page = 'login';
}

$includeFile = $validPages[$page];

$pageTitles = [
    'login' => 'HR Portal Login',
    'admin_dashboard' => 'Admin Dashboard',
    'admin_employees' => 'Manage Employees',
    'admin_users' => 'System Users',
    'admin_payslips' => 'Manage Payslips',
    'admin_logs' => 'Activity Logs',
    'admin_account' => 'My Account',
    'employee_dashboard' => 'My Dashboard',
    'employee_payslips' => 'My Payslips',
    'employee_logs' => 'My Logs',
    'employee_account' => 'My Profile'
];
$pageTitle = $pageTitles[$page] ?? 'HRM System';

if ($page === 'login') {
    require_once $includeFile;
} else {
    require_once 'includes/header.php';
    require_once $includeFile;
    require_once 'includes/footer.php';
}
?>
