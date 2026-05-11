<?php include 'includes/sidebar_employee.php'; ?>
<style>
    /* Filter row — desktop: inline flex; mobile: stacked */
    .filter-row { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
    .filter-row select { flex: 0 0 130px; min-width: 100px; }
    .filter-row .btn { flex: 0 0 auto; }
    #payslipSection { overflow: visible; }

    @media (min-width: 1400px) {
        .payslip-doc { max-width: 1000px !important; }
    }

    @media (max-width: 992px) {
        .filter-row { flex-direction: column; align-items: stretch; }
        .filter-row select, .filter-row .btn { width: 100% !important; flex: none; }
        #payslipSection { padding: 14px; }
    }
    @media (max-width: 576px) {
        #payslipSection h3 { font-size: 1rem; }
        #payslipSection > div:first-child { flex-direction: column; gap: 10px; align-items: flex-start; }
    }
</style>
    <main class="main-content" style="flex:1;">
        <div class="page-header no-print">
            <h1 class="page-title">Welcome, <span id="empName"></span>!</h1>
            <div style="font-weight:600; color:var(--primary-color);">Employee Code: <span id="empCode"></span></div>
        </div>

        <div class="stats-grid no-print">
            <div class="stat-card">
                <h3>My Designation</h3>
                <div class="value" style="font-size: 1.5rem" id="empDesig">-</div>
            </div>
            <div class="stat-card">
                <h3>Current Package (LPA)</h3>
                <div class="value" style="font-size: 1.5rem" id="empPkg">-</div>
            </div>
            <div class="stat-card">
                <h3>Total Paid Leaves</h3>
                <div class="value" style="font-size: 1.5rem" id="empLeaves">0</div>
            </div>
        </div>

        <div id="payslipSection" class="chart-container">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="color:var(--text-primary);"><i class="fa-solid fa-file-invoice-dollar"></i> View Salary Slip</h3>
                <button class="btn btn-outline no-print" onclick="printSlip()"><i class="fa-solid fa-print"></i> Print Slip</button>
            </div>

            <div class="filter-row no-print">
                <select id="yearSelector" class="form-control"></select>
			    <select id="monthSelector" class="form-control"></select>
				
                <button class="btn btn-primary" onclick="loadSelectedSlip()" style="padding: 10px 25px; font-weight: 700;">
                    <i class="fa-solid fa-eye"></i> Show Salary Slip
                </button>
            </div>

            <div id="noSlipMsg" style="display:none; text-align:center; padding:40px; color:var(--text-secondary);">
                <i class="fa-solid fa-circle-info" style="font-size:2rem; margin-bottom:10px;"></i><br>
                No payslip found for the selected period.
            </div>

            <div class="slip-wrapper" id="pdfContainer" style="display:none;">
                <div class="payslip-doc is-locked" id="slipDoc" style="box-shadow:none; border:1px solid #eee;">
                    <div class="doc-header">
                        <div class="doc-header-left">
                            <img src="assets/images/aadink_logo.png" alt="Aadink Pharma" style="height:60px; object-fit:contain; margin-bottom:8px;">
                            <div class="doc-address">Ground Floor, 94, Gaur City Center, Sector 4, Greater Noida (West), Uttar Pradesh-201318, INDIA</div>
                        </div>
                        <div class="doc-header-right">
                            <img src="assets/images/iso_logo.png" alt="ISO 9001:2015" style="height:70px; width:70px; object-fit:contain; margin-bottom:8px;">
                            <div class="doc-title" id="displayMonthTitle">SALARY SLIP</div>
                        </div>
                    </div>
    
                    <div class="emp-details-grid">
                        <div class="emp-detail-item"><div class="emp-detail-label">Employee Code:</div><div class="emp-detail-value" id="dispCode">-</div></div>
                        <div class="emp-detail-item"><div class="emp-detail-label">Designation:</div><div class="emp-detail-value" id="dispDesignation">-</div></div>
                        <div class="emp-detail-item"><div class="emp-detail-label">First Name:</div><div class="emp-detail-value" id="dispFirstName">-</div></div>
                        <div class="emp-detail-item"><div class="emp-detail-label">Last Name:</div><div class="emp-detail-value" id="dispLastName">-</div></div>
                    </div>
    
                    <table class="payslip-table">
                        <thead><tr><th>Components</th><th>Per month (₹)</th></tr></thead>
                        <tbody id="slipComponents"></tbody>
                    </table>
    
                    <div class="signature-block">
                     <div class="sigg-box">
                      <img src="assets/images/hr_sign.png" class="signature-img"> 
                      <div class="signature-line"></div>
                      <div class="sig-title">Authorized Signatory</div>
                      <div class="sig-sub">Aadink Pharma Pvt Ltd</div>
                     </div>
                    </div>
					
                </div>
            </div>
        </div>

        <div class="chart-container no-print" style="margin-top: 30px;">
            <h3 style="margin-bottom:15px; color:var(--text-secondary);">Salary Growth Chart</h3>
            <canvas id="empSalaryChart"></canvas>
        </div>
    </main>
</div>
<!-- Specific script for this page -->
<script src="assets/js/employee_dashboard.js?v=1.1"></script>

