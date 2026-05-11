// Global logout function - must be global for sidebar onclick=""logout()""
function logout() {
    localStorage.removeItem('sessionUser');
    window.location.href = 'index.php?page=login';
}
        const sessionUser = JSON.parse(localStorage.getItem('sessionUser'));
        // We handle logout globally, but redirect if no session
        if(!sessionUser || (sessionUser.role_name !== 'Employee' && sessionUser.role_id != 3)) window.location.href = 'index.php?page=login';
        
        let allSlips = [];
        let empData = null;

        document.addEventListener('DOMContentLoaded', async () => {
            const now = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            document.getElementById('monthSelector').value = months[now.getMonth()];
            document.getElementById('yearSelector').value = now.getFullYear();

            document.getElementById('empName').innerText = sessionUser.first_name + ' ' + sessionUser.last_name;
            document.getElementById('empCode').innerText = sessionUser.emp_code;
            let desig = sessionUser.designation_name || sessionUser.designation;
            if (desig == '2') desig = 'Sales Officer';
            if (desig == '1') desig = 'HR Manager';
            document.getElementById('empDesig').innerText = desig;
            document.getElementById('empPkg').innerText = '₹' + parseFloat(sessionUser.package).toLocaleString('en-IN');
            document.getElementById('empLeaves').innerText = sessionUser.paid_leaves_taken;

            await loadEmployeeData();
            initChart();
        });

        async function loadEmployeeData() {
            try {
                // Update to point to correct API path if moved, otherwise use absolute path for now
                const res = await fetch('api.php?action=get_employee&code=' + sessionUser.emp_code);
                const payload = await res.json();
                if(payload.status === 'success') {
                    empData = payload.data;
                    let slips = empData.payslips_json || [];
                    if (typeof slips === 'string') {
                        try { slips = JSON.parse(slips); } catch(e) { slips = []; }
                    }
                    allSlips = slips;
                    
                    if(allSlips.length > 0) {
                        const latest = allSlips[allSlips.length - 1];
                        document.getElementById('monthSelector').value = latest.month;
                        document.getElementById('yearSelector').value = latest.year;
                    }
                    
                    loadSelectedSlip(); 
                }
            } catch(e) { console.error(e); }
        }

        function loadSelectedSlip() {
            const m = document.getElementById('monthSelector').value;
            const y = document.getElementById('yearSelector').value;
            const slip = allSlips.find(s => s.month === m && s.year == y);

            const container = document.getElementById('pdfContainer');
            const noMsg = document.getElementById('noSlipMsg');

            if(slip) {
                noMsg.style.display = 'none';
                document.getElementById('displayMonthTitle').innerText = `SALARY SLIP FOR ${slip.month} ${slip.year}`;
                document.getElementById('dispCode').innerText = empData.emp_code;
                let sDesig = empData.designation_name || empData.designation;
                if (sDesig == '2') sDesig = 'Sales Officer';
                if (sDesig == '1') sDesig = 'HR Manager';
                document.getElementById('dispDesignation').innerText = sDesig;
                document.getElementById('dispFirstName').innerText = empData.first_name;
                document.getElementById('dispLastName').innerText = empData.last_name;
                
                const conf = empData.salary_config_json || {};
                const gross = parseFloat(slip.gross || empData.gross_monthly || 0);
                const basic = parseFloat(slip.basic || (gross * (conf.percBasic || 50) / 100));
                const da = parseFloat(slip.da || (gross * (conf.percDA || 30) / 100));
                const conv = parseFloat(slip.conveyance || conf.amtConveyance || 0);
                const med = parseFloat(slip.medical || conf.amtMedical || 0);
                const special = parseFloat(slip.special || (gross - (basic + da + conv + med)));
                
                const pf = parseFloat(slip.pf || (basic * (conf.percPF || 12) / 100));
                const esi = parseFloat(slip.esi || (gross * (conf.percESI || 0.75) / 100));
                const pt = parseFloat(slip.pt || conf.amtPT || 0);
                const lDeduct = parseFloat(slip.leave_deduction || 0);
                const totalDeduct = pf + esi + pt + lDeduct;

                const net = parseFloat((slip.net_salary || "").toString().replace(/,/g,'')) || (gross - totalDeduct);

                const empPF = parseFloat(slip.employer_pf || (basic * (conf.percEmpPF || 13) / 100));
                const empESI = parseFloat(slip.employer_esi || (gross * (conf.percEmpESI || 3.25) / 100));
                const ctc = parseFloat(slip.ctc || (gross + empPF + empESI));

                document.getElementById('slipComponents').innerHTML = `
                    <tr class="ps-section"><td colspan="2">Earnings</td></tr>
                    <tr><td>Basic Salary</td><td>₹${basic.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>DA (Dearness Allowance)</td><td>₹${da.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>Conveyance Allowance</td><td>₹${conv.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>Medical Allowance</td><td>₹${med.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>Special Allowance</td><td>₹${special.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr class="ps-total"><td>Total Gross Salary</td><td>₹${gross.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    
                    <tr class="ps-section"><td colspan="2">Deductions</td></tr>
                    <tr><td>PF Contribution</td><td>₹${pf.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>ESI Contribution</td><td>₹${esi.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>Professional Tax</td><td>₹${pt.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>Leave Deductions</td><td>₹${lDeduct.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr class="ps-total"><td>Total Deductions</td><td>₹${totalDeduct.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr class="ps-net"><td>Net Salary Paid</td><td>₹${net.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    
                    <tr class="ps-section"><td colspan="2">CTC Calculation (Company's Contribution)</td></tr>
                    <tr><td>Employer PF Contribution</td><td>₹${empPF.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr><td>Employer ESI Contribution</td><td>₹${empESI.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>
                    <tr class="ps-total"><td>Cost to Company (CTC)</td><td>₹${ctc.toLocaleString('en-IN', {minimumFractionDigits:2})}</td></tr>

                    <tr class="ps-section"><td colspan="2">Details</td></tr>
                    <tr><td>Status</td><td><span style="color:var(--success); font-weight:bold;">${slip.status || 'Paid'}</span></td></tr>
                    <tr><td>Generation Date</td><td>${slip.generated_date}</td></tr>
                    ${!slip.basic ? '<tr><td colspan="2" style="font-size:0.7rem; color:var(--text-secondary); font-style:italic;">* Breakdown estimated from current config (old record)</td></tr>' : ''}
                `;
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
                noMsg.style.display = 'block';
            }
        }

function printSlip() {

    const slip = document.querySelector('.payslip-doc');

    if (!slip) {
        alert("No payslip to print");
        return;
    }

    // 🔥 Save original page
    const originalContent = document.body.innerHTML;

    // 🔥 Replace with only payslip
    document.body.innerHTML = slip.outerHTML;

    // 🔥 Print
    window.print();

    // 🔄 Restore page
    document.body.innerHTML = originalContent;

    // 🔁 Reload to restore JS state (important)
    location.reload();
}


// Dynamic year and month selection in payslips
const months = [
    "JAN","FEB","MAR","APR","MAY","JUN",
    "JUL","AUG","SEP","OCT","NOV","DEC"
];

// Dynamic Year (2020 → Current Year AUTO)
function loadYears() {
    const yearSelector = document.getElementById("yearSelector");
    const currentYear = new Date().getFullYear();

    yearSelector.innerHTML = "";

    for (let year = 2020; year <= currentYear; year++) {
        let opt = document.createElement("option");
        opt.value = year;
        opt.textContent = year;

        if (year === currentYear) opt.selected = true;

        yearSelector.appendChild(opt);
    }
}

// Dynamic Months
function loadMonths() {
    const selectedYear = parseInt(document.getElementById("yearSelector").value);
    const monthSelector = document.getElementById("monthSelector");

    const today = new Date();
    const currentYear = today.getFullYear();
    const currentMonth = today.getMonth();

    monthSelector.innerHTML = "";

    let limit = (selectedYear === currentYear) ? currentMonth : 11;

    for (let i = 0; i <= limit; i++) {
        let opt = document.createElement("option");
        opt.value = months[i];
        opt.textContent = months[i];

        if (selectedYear === currentYear && i === currentMonth) {
            opt.selected = true;
        }

        monthSelector.appendChild(opt);
    }
}

// Init
loadYears();
loadMonths();

document.getElementById("yearSelector").addEventListener("change", loadMonths);





        function initChart() {
            const ctx = document.getElementById('empSalaryChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Net Monthly Salary (₹)',
                        data: [sessionUser.gross_monthly * 0.8, sessionUser.gross_monthly * 0.82, sessionUser.gross_monthly * 0.8, sessionUser.gross_monthly * 0.85, sessionUser.gross_monthly * 0.8, sessionUser.gross_monthly * 0.83],
                        borderColor: 'rgba(79, 70, 229, 1)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }


function logout() { localStorage.removeItem('sessionUser'); window.location.href = 'index.php?page=login'; }


