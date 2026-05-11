<?php
$currentPage = $_GET['page'] ?? 'employee_dashboard';
?>
<button class="mobile-toggle no-print" onclick="document.querySelector('.sidebar').classList.toggle('active'); document.body.classList.toggle('sidebar-open');"><i class="fa-solid fa-bars"></i></button>
<aside class="sidebar no-print">
    <div class="sidebar-header" style="position:relative;">
        <img src="assets/images/payzen_logo.jpg" alt="Aadink Pharma" class="sidebar-logo">
        <button class="close-sidebar" onclick="document.querySelector('.sidebar').classList.remove('active'); document.body.classList.remove('sidebar-open');">&times;</button>
    </div>
    <ul class="nav-links">
        <li><a href="index.php?page=employee_dashboard" class="<?php echo $currentPage == 'employee_dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-user"></i> My Dashboard</a></li>
        <li><a href="index.php?page=employee_payslips" class="<?php echo $currentPage == 'employee_payslips' ? 'active' : ''; ?>"><i class="fa-solid fa-file-invoice-dollar"></i> My Payslips</a></li>
        <li><a href="index.php?page=employee_logs" class="<?php echo $currentPage == 'employee_logs' ? 'active' : ''; ?>"><i class="fa-solid fa-history"></i> My Logs</a></li>
        <li><a href="index.php?page=employee_account" class="<?php echo $currentPage == 'employee_account' ? 'active' : ''; ?>"><i class="fa-solid fa-id-badge"></i> My Account</a></li>
    </ul>
    <div class="sidebar-footer"><button class="logout-btn" onclick="logout()"><i class="fa-solid fa-sign-out-alt"></i> Logout</button></div>
</aside>
