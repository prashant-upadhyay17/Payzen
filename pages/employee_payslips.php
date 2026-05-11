<?php include 'includes/sidebar_employee.php'; ?>
<link rel="stylesheet" href="assets/css/style.css?v=1.2">
<style>
    /* Remove duplicate force-desktop-layout CSS, now in style.css */
    @media (max-width: 992px) {
        .doc-header { flex-direction: column; text-align: center; gap: 10px; }
        .doc-header-left, .doc-header-right { align-items: center; text-align: center; }
        .emp-details-grid { grid-template-columns: 1fr; }
        .signature-block { text-align: center; }
        .sigg-box { margin: 0 auto; }
    }
</style>
    <main class="main-content">
        <div class="page-header no-print">
            <h1 class="page-title">Payslips History</h1>
            <button class="btn btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print View</button>
        </div>

        <div class="table-container no-print" style="margin-bottom: 30px;">
            <table class="data-table" id="empSlipsTable">
                <thead>
                    <tr>
                        <th>Month / Year</th>
                        <th>Generated On</th>
                        <th>Net Salary</th>
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
    </main>
<script>
        const sessionUser = JSON.parse(localStorage.getItem('sessionUser'));
        if(!sessionUser || (sessionUser.role_name !== 'Employee' && sessionUser.role_id != 3)) window.location.href = 'index.php?page=login';
        function logout() { localStorage.removeItem('sessionUser'); window.location.href = 'index.php?page=login'; }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadSlips();
        });

        async function loadSlips() {
            try {
                const res = await fetch('api.php?action=get_employee&code=' + sessionUser.emp_code);
                const payload = await res.json();
                if(payload.status === 'success') {
                    let slips = payload.data.payslips_json || [];
                    if (typeof slips === 'string') {
                        try { slips = JSON.parse(slips); } catch(e) { slips = []; }
                    }
                    const tbody = document.querySelector('#empSlipsTable tbody');
                    tbody.innerHTML = '';
                    if(!Array.isArray(slips) || slips.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:var(--text-secondary)">No payslips generated yet. Contact your HR Admin.</td></tr>';
                    } else {
                        // Show newest first
                        [...slips].reverse().forEach((s, index) => {
                            const realIndex = slips.length - 1 - index;
                            const netDisplay = parseFloat((s.net_salary||'0').toString().replace(/,/g,'')).toLocaleString('en-IN', {minimumFractionDigits:2});
                            tbody.innerHTML += `
                                <tr>
                                    <td><strong>${s.month} ${s.year}</strong></td>
                                    <td>${s.generated_date || '-'}</td>
                                    <td><strong>₹${netDisplay}</strong></td>
                                    <td style="color:var(--success); font-weight:bold;">${s.status || 'Paid'}</td>
                                    <td><button class="btn btn-primary" style="padding:6px 14px;" onclick="viewSlip(${realIndex})"><i class="fa-solid fa-eye"></i> View</button></td>
                                </tr>
                            `;
                        });
                        window.allSlips = slips;
                        window.empFullData = payload.data;
                    }
                }
            } catch(e) { console.error(e); }
        }

        function viewSlip(index) {
            const s = window.allSlips[index];
            const emp = window.empFullData;
            document.getElementById('displayMonthTitle').innerText = `SALARY SLIP FOR ${s.month} ${s.year}`;
            document.getElementById('dispCode').innerText = emp.emp_code;
            document.getElementById('dispDesignation').innerText = emp.designation_name || emp.designation;
            document.getElementById('dispFirstName').innerText = emp.first_name;
            document.getElementById('dispLastName').innerText = emp.last_name;

            const tbody = document.getElementById('slipComponents');
            const conf = emp.salary_config_json || {};
            
            const gross    = parseFloat(s.gross    || emp.gross_monthly || 0);
            const basic    = parseFloat(s.basic    || (gross * (conf.percBasic || 50) / 100));
            const da       = parseFloat(s.da       || (gross * (conf.percDA || 30) / 100));
            const conv     = parseFloat(s.conveyance || conf.amtConveyance || 0);
            const med      = parseFloat(s.medical  || conf.amtMedical || 0);
            const special  = parseFloat(s.special  || Math.max(0, gross - (basic + da + conv + med)));
            const pf       = parseFloat(s.pf       || (basic * (conf.percPF || 12) / 100));
            const esi      = parseFloat(s.esi      || (gross * (conf.percESI || 0.75) / 100));
            const pt       = parseFloat(s.pt       || conf.amtPT || 0);
            const lDeduct  = parseFloat(s.leave_deduction || 0);
            const totalDeduct = parseFloat(s.total_deductions || (pf + esi + pt + lDeduct));
            const net      = parseFloat((s.net_salary||'0').toString().replace(/,/g,'')) || (gross - totalDeduct);
            const empPF    = parseFloat(s.employer_pf  || (basic * (conf.percEmpPF || 13) / 100));
            const empESI   = parseFloat(s.employer_esi || (gross * (conf.percEmpESI || 3.25) / 100));
            const ctc      = parseFloat(s.ctc || (gross + empPF + empESI));

            const fmt = (n) => '₹' + parseFloat(n).toLocaleString('en-IN', {minimumFractionDigits:2});

            tbody.innerHTML = `
                <tr class="ps-section"><td colspan="2">Earnings</td></tr>
                <tr><td>Basic Salary</td><td>${fmt(basic)}</td></tr>
                <tr><td>DA (Dearness Allowance)</td><td>${fmt(da)}</td></tr>
                <tr><td>Conveyance Allowance</td><td>${fmt(conv)}</td></tr>
                <tr><td>Medical Allowance</td><td>${fmt(med)}</td></tr>
                <tr><td>Special Allowance</td><td>${fmt(special)}</td></tr>
                <tr class="ps-total"><td>Total Gross Salary</td><td>${fmt(gross)}</td></tr>
                <tr class="ps-section"><td colspan="2">Deductions</td></tr>
                <tr><td>PF Contribution</td><td>${fmt(pf)}</td></tr>
                <tr><td>ESI Contribution</td><td>${fmt(esi)}</td></tr>
                <tr><td>Professional Tax</td><td>${fmt(pt)}</td></tr>
                <tr><td>Leave Deductions</td><td>${fmt(lDeduct)}</td></tr>
                <tr class="ps-total"><td>Total Deductions</td><td>${fmt(totalDeduct)}</td></tr>
                <tr class="ps-net"><td>Net Salary Paid</td><td>${fmt(net)}</td></tr>
                <tr class="ps-section"><td colspan="2">CTC (Company's Contribution)</td></tr>
                <tr><td>Employer PF Contribution</td><td>${fmt(empPF)}</td></tr>
                <tr><td>Employer ESI Contribution</td><td>${fmt(empESI)}</td></tr>
                <tr class="ps-total"><td>Cost to Company (CTC)</td><td>${fmt(ctc)}</td></tr>
                ${!s.basic ? '<tr><td colspan="2" style="font-size:0.75rem;color:var(--text-secondary);font-style:italic;">* Values estimated from current config (old record)</td></tr>' : ''}
            `;

            document.getElementById('pdfContainer').style.display = 'flex';
            document.getElementById('pdfContainer').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
