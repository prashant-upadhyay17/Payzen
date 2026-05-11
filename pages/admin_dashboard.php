<?php include 'includes/sidebar_admin.php'; ?>
    <main class="main-content" style="flex:1;">
        <div class="page-header">
            <h1 class="page-title">Dashboard Overview</h1>
            <div>
                Logged in as: <strong id="adminName">Loading...</strong>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Employees</h3>
                <div class="value" id="totalEmpCount">0</div>
            </div>
            <div class="stat-card">
                <h3>Total Payslips Generated</h3>
                <div class="value" id="totalSlips">0</div>
            </div>
            <div class="stat-card">
                <h3>Monthly Salary Disbursed</h3>
                <div class="value" id="totalSalary">₹0.00</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="chart-container">
                <h3 style="margin-bottom:15px; color:var(--text-secondary);">Salary Distribution (Gross)</h3>
                <canvas id="salaryChart"></canvas>
            </div>
            <div class="chart-container">
                <h3 style="margin-bottom:15px; color:var(--text-secondary);">Employee Departments</h3>
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </main>
</div>
<!-- Specific script for this page -->
<script src="assets/js/admin_shared.js"></script>
<script src="assets/js/admin_dashboard.js"></script>

