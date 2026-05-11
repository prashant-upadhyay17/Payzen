<?php include 'includes/sidebar_admin.php'; ?>
<style>
    /* Desktop: selects have reasonable widths inside action-bar */
    .action-bar #empSelector { flex: 0 0 180px; }
    .action-bar #yearSelector, .action-bar #monthSelector { flex: 0 0 110px; }

    /* Dual layout for report and slip on large screens */
    .payslip-dual-layout { display: flex; flex-direction: column; gap: 30px; width: 100%; margin-top: 20px; }
    
    @media (min-width: 1400px) {
        .payslip-dual-layout { flex-direction: row; align-items: flex-start; gap: 30px; }
        #monthlyReportSection { flex: 1.1; margin-top: 0 !important; min-width: 0; }
        #pdfContainer { flex: 0.9; margin-top: 0 !important; min-width: 0; }
        .payslip-doc { margin: 0; max-width: 100%; }
    }

    /* Mobile: payslip inner doc header stacks */
    @media (max-width: 992px) {
        .action-bar #empSelector,
        .action-bar #yearSelector,
        .action-bar #monthSelector { flex: none; width: 100% !important; }
        .doc-header { flex-direction: column; text-align: center; gap: 10px; }
        .doc-header-left, .doc-header-right { align-items: center; text-align: center; }
        .emp-details-grid { grid-template-columns: 1fr; }
        .signature-block { text-align: center; }
        .sigg-box { margin: 0 auto; }
    }
</style>
    <main class="main-content">
        <div class="page-header no-print">
            <h1 class="page-title">Generate & Manage Payslips</h1>
        </div>

        <div class="action-bar no-print">
		
            <select id="empSelector" class="form-control"><option value="">Select Employee</option></select>
			
            <select id="yearSelector" class="form-control"></select> 
			
			<select id="monthSelector" class="form-control"></select>
			
            <button class="btn btn-primary" onclick="loadSlip()"><i class="fa-solid fa-eye"></i> View Slip</button>
			
            <button class="btn btn-warning" id="unlockBtn" onclick="toggleLock()" style="display:none;"><i class="fa-solid fa-lock"></i> Unlock for Editing</button>
			
            <button class="btn btn-success" id="saveBtn" onclick="savePayslip()" style="display:none;"><i class="fa-solid fa-file-circle-check"></i> Generate & Save Payslip</button>
			
            <button class="btn btn-outline" id="printBtn" onclick="window.print()" style="display:none;"><i class="fa-solid fa-print"></i> Print</button>
			
            <button class="btn btn-danger" id="pdfBtn" onclick="downloadPDF()" style="display:none;"><i class="fa-solid fa-file-pdf"></i> PDF</button>
			
            <button class="btn btn-primary" id="emailBtn" onclick="sendEmail()" style="display:none;"><i class="fa-solid fa-envelope"></i> Email</button>
			
			
            <button class="btn btn-outline" onclick="toggleReport()"><i class="fa-solid fa-chart-line"></i> View Monthly Report</button>
        </div>

        <div class="payslip-dual-layout">
            <div class="table-container no-print" id="monthlyReportSection" style="display:none; width:100%;">
                <h2 style="margin-bottom: 20px;">Monthly Payroll Report (<span id="reportMonthYear"></span>)</h2>
                <table class="data-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>Emp Code</th>
                            <th>Name</th>
                            <th>Gross</th>
                            <th>Net</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="slip-wrapper" id="pdfContainer" style="display:none;">
                <div class="payslip-doc is-locked" id="slipDoc">
                <div class="doc-header">
                    <div class="doc-header-left">
                        <center><img src="assets/images/payzen_logo.jpg" class="pdf-logo"></center><div class="doc-address">Ground Floor abc Tower,Sector 105, xyz (West),Uttar Pradesh INDIA</div>
                    </div>
                    <div class="doc-header-right">
                        <img src="assets/images/iso_logo.png" alt="ISO 9001:2015" style="height:70px; width:70px; object-fit:contain; margin-bottom:8px;">
                        <div class="doc-title" id="displayMonthTitle">SALARY SLIP FOR JAN 2026</div>
                    </div>
                </div>

                <div class="emp-details-grid">
                    <div class="emp-detail-item"><div class="emp-detail-label">Employee Code:</div><div class="emp-detail-value" id="dispCode">-</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">Designation:</div><div class="emp-detail-value" id="dispDesignation">-</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">First Name:</div><div class="emp-detail-value" id="dispFirstName">-</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">Last Name:</div><div class="emp-detail-value" id="dispLastName">-</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">Package (LPA):</div><div class="emp-detail-value" id="dispPkg">₹0</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">Paid Leaves Taken:</div><div class="emp-detail-value" id="dispLeaves">0</div></div>
                </div>

                <table class="payslip-table">
                    <thead><tr><th>Components</th><th>Percentage</th><th>Per month (₹)</th><th>Per annum (₹)</th></tr></thead>
                    <tbody>
                        <tr class="ps-section"><td colspan="4">Earnings</td></tr>
                        <tr><td>Basic Salary</td><td><input type="number" id="percBasic" class="editable-perc no-print-border" value="50">%</td><td><input type="number" id="amtBasic" class="editable-amt no-print-border"></td><td id="annBasic">0</td></tr>
                        <tr><td>DA (Dearness Allowance)</td><td><input type="number" id="percDA" class="editable-perc no-print-border" value="30">%</td><td><input type="number" id="amtDA" class="editable-amt no-print-border"></td><td id="annDA">0</td></tr>
                        <tr><td>Conveyance allowances</td><td>-</td><td><input type="number" id="amtConveyance" class="editable-amt no-print-border" value="0"></td><td id="annConveyance">0</td></tr>
                        <tr><td>Medical allowances</td><td>-</td><td><input type="number" id="amtMedical" class="editable-amt no-print-border" value="0"></td><td id="annMedical">0</td></tr>
                        <tr><td>Special allowances</td><td>-</td><td><input type="number" id="amtSpecial" class="editable-amt no-print-border" value="0"></td><td id="annSpecial">0</td></tr>
                        <tr class="ps-total"><td>Total Gross Salary</td><td></td><td><input type="number" id="amtGross" class="editable-amt no-print-border"></td><td id="annGross">0</td></tr>

                        <tr class="ps-section"><td colspan="4">Deductions</td></tr>
                        <tr><td>PF contribution</td><td><input type="number" id="percPF" class="editable-perc no-print-border" value="12">%</td><td><input type="number" id="amtPF" class="editable-amt no-print-border"></td><td id="annPF">0</td></tr>
                        <tr><td>ESI contribution</td><td><input type="number" id="percESI" class="editable-perc no-print-border" value="0.75" step="0.01">%</td><td><input type="number" id="amtESI" class="editable-amt no-print-border"></td><td id="annESI">0</td></tr>
                        <tr><td>Professional Tax</td><td>-</td><td><input type="number" id="amtPT" class="editable-amt no-print-border" value="0"></td><td id="annPT">0</td></tr>
                        <tr id="leaveDeductRow" style="display: none;"><td>Leave Deductions (<span id="extraLeavesCount">0</span> extra leaves)</td><td>-</td><td id="amtLeaveDeduct" style="color:var(--danger)">0</td><td id="annLeaveDeduct" style="color:var(--danger)">0</td></tr>
                        <tr class="ps-total"><td>Total deductions</td><td></td><td id="amtTotalDeductions">0</td><td id="annTotalDeductions">0</td></tr>
                        <tr class="ps-net"><td>Net Salary</td><td></td><td id="amtNetSalary">0</td><td id="annNetSalary">0</td></tr>
                        
                        <tr class="ps-section"><td colspan="4">CTC Calculation</td></tr>
                        <tr><td>Employer PF contribution</td><td><input type="number" id="percEmpPF" class="editable-perc no-print-border" value="13">%</td><td id="amtEmpPF">0</td><td id="annEmpPF">0</td></tr>
                        <tr><td>Employer ESI contribution</td><td><input type="number" id="percEmpESI" class="editable-perc no-print-border" value="3.25" step="0.01">%</td><td id="amtEmpESI">0</td><td id="annEmpESI">0</td></tr>
                        <tr class="ps-total"><td>CTC = Gross + Emp PF + Emp ESI</td><td></td><td id="amtCTC">0</td><td id="annCTC">0</td></tr>
                    </tbody>
                </table>

               <div class="signature-block">
                  <div class="sigg-box">
                    <img src="assets/images/hr_sign.png" class="signature-img"> 
                    <div class="signature-line"></div>
                    <div class="sig-title">Authorized Signatory</div>
                    <div class="sig-sub">Payzen</div>
                  </div>
               </div>
			   
            </div>
        </div>
    </div>
    <div id="emailMsg" class="email-msg"></div>
    </main>
    <!-- Versioning scripts to force refresh -->
    <script src="assets/js/admin_shared.js?v=1.1"></script>
    <script src="assets/js/admin_payslips.js?v=1.2"></script>

    <!-- Hidden Fixed Template for PDF/Email -->
<div id="printSlip" style="
    position: absolute;
    left: -9999px;
    top: 0;
    width: 800px;
    background: #ffffff;
    padding: 20px;
">
</div>