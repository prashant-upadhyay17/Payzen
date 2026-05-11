<?php
$currentPage = $_GET['page'] ?? 'admin_dashboard';
?>
<button class="mobile-toggle no-print" onclick="document.querySelector('.sidebar').classList.toggle('active'); document.body.classList.toggle('sidebar-open');"><i class="fa-solid fa-bars"></i></button>
<aside class="sidebar no-print">
    <div class="sidebar-header" style="position:relative;">
        <img src="assets/images/payzen_logo.jpg" alt="Aadink Pharma" class="sidebar-logo">
        <button class="close-sidebar" onclick="document.querySelector('.sidebar').classList.remove('active'); document.body.classList.remove('sidebar-open');">&times;</button>
    </div>
    <ul class="nav-links">
        <li><a href="index.php?page=admin_dashboard" class="<?php echo $currentPage == 'admin_dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="index.php?page=admin_employees" class="<?php echo $currentPage == 'admin_employees' ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> Employees</a></li>
        <li><a href="index.php?page=admin_users" class="<?php echo $currentPage == 'admin_users' ? 'active' : ''; ?>"><i class="fa-solid fa-user-tie"></i> System Users</a></li>
        <li><a href="index.php?page=admin_payslips" class="<?php echo $currentPage == 'admin_payslips' ? 'active' : ''; ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Payslips</a></li>
        <li><a href="index.php?page=admin_logs" class="<?php echo $currentPage == 'admin_logs' ? 'active' : ''; ?>"><i class="fa-solid fa-history"></i> Activity Logs</a></li>
        <li><a href="index.php?page=admin_account" class="<?php echo $currentPage == 'admin_account' ? 'active' : ''; ?>"><i class="fa-solid fa-id-badge"></i> My Account</a></li>
    </ul>
    <div class="sidebar-footer">
        <button class="logout-btn" onclick="logout()"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
    </div>
</aside>
