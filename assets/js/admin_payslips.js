let allEmployees = [];
let currentEmp = null;
let isLocked = true;
let manualOverrides = { basic: false, da: false, special: false, pf: false, esi: false };

document.addEventListener('DOMContentLoaded', async () => {
    try {
        allEmployees = await fetchEmployees();
        const sel = document.getElementById('empSelector');
        if (allEmployees && Array.isArray(allEmployees)) {
            allEmployees.forEach(e => {
                sel.innerHTML += `<option value="${e.emp_code}">${e.emp_code} - ${e.first_name} ${e.last_name}</option>`;
            });
        }
        const preselect = localStorage.getItem('slipEmpCode');
        if (preselect) {
            sel.value = preselect;
            localStorage.removeItem('slipEmpCode');
            loadSlip();
        }
    } catch (err) { console.error("Error loading employees:", err); }

    ['monthSelector', 'yearSelector'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => {
            updateTitle();
            if (currentEmp) loadHistoricalSlip();
            if (document.getElementById('monthlyReportSection').style.display !== 'none') loadMonthlyReport();
        });
    });

    document.addEventListener('input', (e) => {
        if (!e.target.matches('.editable-amt, .editable-perc')) return;
        const id = e.target.id;
        if (id === 'amtGross') { manualOverrides.basic = false; manualOverrides.da = false; manualOverrides.special = false; }
        if (id === 'amtBasic') manualOverrides.basic = true;
        if (id === 'amtDA') manualOverrides.da = true;
        if (id === 'amtSpecial') manualOverrides.special = true;
        if (id === 'amtPF') manualOverrides.pf = true;
        if (id === 'amtESI') manualOverrides.esi = true;
        calculateSlip();
    });
});

function updateTitle() {
    const m = document.getElementById('monthSelector').value;
    const y = document.getElementById('yearSelector').value;
    document.getElementById('displayMonthTitle').innerText = `SALARY SLIP FOR ${m} ${y}`;
}

function loadHistoricalSlip() {
    if (!currentEmp) return;
    const month = document.getElementById('monthSelector').value;
    const year = document.getElementById('yearSelector').value;
    const slips = Array.isArray(currentEmp.payslips_json) ? currentEmp.payslips_json : [];
    const existingSlip = slips.find(s => s.month === month && String(s.year) === String(year));

    manualOverrides = { basic: false, da: false, special: false, pf: false, esi: false };
    const conf = currentEmp.salary_config_json || {};

    document.getElementById('amtGross').value = currentEmp.gross_monthly;
    document.getElementById('percBasic').value = conf.percBasic || 50;
    document.getElementById('percDA').value = conf.percDA || 30;
    document.getElementById('percPF').value = conf.percPF || 12;
    document.getElementById('percESI').value = conf.percESI || 0.75;
    document.getElementById('percEmpPF').value = conf.percEmpPF || 13;
    document.getElementById('percEmpESI').value = conf.percEmpESI || 3.25;
    document.getElementById('amtConveyance').value = conf.amtConveyance || 0;
    document.getElementById('amtMedical').value = conf.amtMedical || 0;
    document.getElementById('amtPT').value = conf.amtPT || 0;

    if (existingSlip) {
        document.getElementById('amtGross').value = existingSlip.gross || currentEmp.gross_monthly;
        if (existingSlip.basic) { document.getElementById('amtBasic').value = existingSlip.basic; manualOverrides.basic = true; }
        if (existingSlip.da) { document.getElementById('amtDA').value = existingSlip.da; manualOverrides.da = true; }
        if (existingSlip.special) { document.getElementById('amtSpecial').value = existingSlip.special; manualOverrides.special = true; }
        if (existingSlip.pf) { document.getElementById('amtPF').value = existingSlip.pf; manualOverrides.pf = true; }
        if (existingSlip.esi) { document.getElementById('amtESI').value = existingSlip.esi; manualOverrides.esi = true; }
        if (existingSlip.conveyance) document.getElementById('amtConveyance').value = existingSlip.conveyance;
        if (existingSlip.medical) document.getElementById('amtMedical').value = existingSlip.medical;
        if (existingSlip.pt) document.getElementById('amtPT').value = existingSlip.pt;
    }
    calculateSlip();
}

function toggleLock() {
    const slipDoc = document.getElementById('slipDoc');
    const unlockBtn = document.getElementById('unlockBtn');
    isLocked = !isLocked;
    if (isLocked) {
        slipDoc.classList.add('is-locked');
        unlockBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Unlock for Editing';
        unlockBtn.className = 'btn btn-warning';
    } else {
        slipDoc.classList.remove('is-locked');
        unlockBtn.innerHTML = '<i class="fa-solid fa-lock-open"></i> Lock Slip';
        unlockBtn.className = 'btn btn-outline';
    }
}

function loadSlip() {
    const code = document.getElementById('empSelector').value;
    if (!code) return alert("Select an employee");
    currentEmp = allEmployees.find(e => e.emp_code === code);
    if (!currentEmp) return;

    document.getElementById('dispCode').innerText = currentEmp.emp_code;
    document.getElementById('dispDesignation').innerText = currentEmp.designation_name || currentEmp.designation;
    document.getElementById('dispFirstName').innerText = currentEmp.first_name;
    document.getElementById('dispLastName').innerText = currentEmp.last_name;
    document.getElementById('dispPkg').innerText = '₹' + parseFloat(currentEmp.package).toLocaleString('en-IN');
    document.getElementById('dispLeaves').innerText = currentEmp.paid_leaves_taken;

    const conf = currentEmp.salary_config_json || {};
    manualOverrides = { basic: false, da: false, special: false, pf: false, esi: false };
    document.getElementById('amtGross').value = currentEmp.gross_monthly;
    document.getElementById('percBasic').value = conf.percBasic || 50;
    document.getElementById('percDA').value = conf.percDA || 30;
    document.getElementById('percPF').value = conf.percPF || 12;
    document.getElementById('percESI').value = conf.percESI || 0.75;
    document.getElementById('percEmpPF').value = conf.percEmpPF || 13;
    document.getElementById('percEmpESI').value = conf.percEmpESI || 3.25;
    document.getElementById('amtConveyance').value = conf.amtConveyance || 0;
    document.getElementById('amtMedical').value = conf.amtMedical || 0;
    document.getElementById('amtPT').value = conf.amtPT || 0;
    if (conf.amtBasic !== undefined) { document.getElementById('amtBasic').value = conf.amtBasic; manualOverrides.basic = true; }
    if (conf.amtDA !== undefined) { document.getElementById('amtDA').value = conf.amtDA; manualOverrides.da = true; }
    if (conf.amtSpecial !== undefined) { document.getElementById('amtSpecial').value = conf.amtSpecial; manualOverrides.special = true; }
    if (conf.amtPF !== undefined) { document.getElementById('amtPF').value = conf.amtPF; manualOverrides.pf = true; }
    if (conf.amtESI !== undefined) { document.getElementById('amtESI').value = conf.amtESI; manualOverrides.esi = true; }

    loadHistoricalSlip();
    updateTitle();

    document.getElementById('pdfContainer').style.display = 'flex';
    document.getElementById('slipDoc').classList.add('is-locked');
    isLocked = true;

    // Show all action buttons
    document.getElementById('unlockBtn').style.display = 'inline-flex';
    document.getElementById('unlockBtn').innerHTML = '<i class="fa-solid fa-lock"></i> Unlock for Editing';
    document.getElementById('saveBtn').style.display = 'inline-flex';
    document.getElementById('printBtn').style.display = 'inline-flex';
    document.getElementById('pdfBtn').style.display = 'inline-flex';
    document.getElementById('emailBtn').style.display = 'inline-flex';
}

function toggleReport() {
    const sec = document.getElementById('monthlyReportSection');
    if (sec.style.display === 'none' || sec.style.display === '') {
        sec.style.display = 'block';
        sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        loadMonthlyReport();
    } else {
        sec.style.display = 'none';
    }
}

async function loadMonthlyReport() {
    const month = document.getElementById('monthSelector').value;
    const year = document.getElementById('yearSelector').value;
    document.getElementById('reportMonthYear').innerText = `${month} ${year}`;
    try {
        const res = await fetch(`api.php?action=get_monthly_report&month=${month}&year=${year}`);
        const payload = await res.json();
        if (payload.status === 'success') {
            const tbody = document.querySelector('#reportTable tbody');
            tbody.innerHTML = '';
            payload.data.forEach(row => {
                const statusColor = row.status === 'Paid' ? 'var(--success)' : 'var(--danger)';
                const netStr = String(row.net || '0');
                const netDisplay = parseFloat(netStr.replace(/,/g, '')).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                tbody.innerHTML += `
                    <tr>
                        <td>${row.emp_code}</td>
                        <td>${row.name}</td>
                        <td>₹${parseFloat(row.gross).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                        <td>₹${netDisplay}</td>
                        <td style="color:${statusColor}; font-weight:700;">${row.status}</td>
                        <td><button class="btn btn-outline" style="padding:5px 10px;font-size:0.8rem;" onclick="preSelectEmp('${row.emp_code}')">Select</button></td>
                    </tr>`;
            });
        }
    } catch (e) { console.error(e); }
}

function preSelectEmp(code) {
    document.getElementById('empSelector').value = code;
    loadSlip();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/*function sendEmail() {

    const empCode = document.getElementById("empSelector").value;
    const year    = document.getElementById("yearSelector").value;
    const month   = document.getElementById("monthSelector").value;

    if (!empCode || !year || !month) {
        showMessage("Please select employee, year and month", "error");
        return;
    }

    const btn = document.getElementById("emailBtn");

    // 🔄 Loading state
    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

    fetch("api.php?action=send_payslip_email", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            emp_code: empCode,
            month: month,
            year: year
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "success") {
            showMessage(data.message, "success");
        } else {
            showMessage(data.message, "error");
        }

    })
    .catch(err => {
        showMessage("Something went wrong while sending email", "error");
        console.error(err);
    })
    .finally(() => {
        // 🔁 Restore button
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}*/

function generatePDFBlob(element) {

    return html2pdf()
        .set({
            margin: 0,

            image: { type: 'jpeg', quality: 1 },

            html2canvas: {
                scale: 2,
                useCORS: true
            },

            jsPDF: {
                unit: 'mm',          // 🔥 CHANGE HERE
                format: 'a4',        // 🔥 CHANGE HERE
                orientation: 'portrait'
            },

            pagebreak: { mode: [] } // 🔥 REMOVE avoid-all
        })
        .from(element)
        .outputPdf('blob');
}

function showEmailMessage(message, type = "success") {

    const msgBox = document.getElementById("emailMsg");

    msgBox.className = "email-msg " + type;
    msgBox.innerText = message;

    msgBox.style.display = "block";

    // 🔥 animate in
    setTimeout(() => {
        msgBox.style.opacity = "1";
        msgBox.style.transform = "translate(-50%, -50%) scale(1)";
    }, 10);

    // 🔥 auto hide
    setTimeout(() => {
        msgBox.style.opacity = "0";
        msgBox.style.transform = "translate(-50%, -50%) scale(0.95)";

        setTimeout(() => {
            msgBox.style.display = "none";
        }, 300);
    }, 9000);
}

function sendEmail() {

    const empCode = document.getElementById("empSelector").value;
    const year = document.getElementById("yearSelector").value;
    const month = document.getElementById("monthSelector").value;
    const element = document.querySelector('.payslip-doc');
    const btn = document.getElementById("emailBtn");

    // 🔴 Validation
    if (!empCode || !year || !month) {
        showEmailMessage("Please select employee, year and month", "error");
        return;
    }

    // 🔄 Button loading state
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

    // 🔄 Generate PDF
    generatePDFBlob(element)
        .then(function (pdfBlob) {

            let formData = new FormData();
            formData.append("emp_code", empCode);
            formData.append("month", month);
            formData.append("year", year);
            formData.append("file", pdfBlob, "Payslip.pdf");

            return fetch("api.php?action=send_payslip_email", {
                method: "POST",
                body: formData
            });
        })
        .then(res => {
            if (!res.ok) throw new Error("Server error");
            return res.json();
        })
        .then(data => {

            if (data.status === "success") {
                showEmailMessage(data.message || "Email sent successfully", "success");
            } else {
                showEmailMessage(data.message || "Failed to send email", "error");
            }

        })
        .catch(err => {
            console.error(err);
            showEmailMessage("Something went wrong while sending email", "error");
        })
        .finally(() => {
            // 🔁 Restore button
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

function calculateSlip() {
    if (!currentEmp) return;
    const eGross = document.getElementById('amtGross');
    let gross = parseFloat(eGross.value) || 0;

    const basicPerc = parseFloat(document.getElementById('percBasic').value) || 0;
    let basicAmt = manualOverrides.basic ? (parseFloat(document.getElementById('amtBasic').value) || 0) : (gross * basicPerc) / 100;
    if (!manualOverrides.basic) document.getElementById('amtBasic').value = basicAmt.toFixed(2);

    const daPerc = parseFloat(document.getElementById('percDA').value) || 0;
    let daAmt = manualOverrides.da ? (parseFloat(document.getElementById('amtDA').value) || 0) : (gross * daPerc) / 100;
    if (!manualOverrides.da) document.getElementById('amtDA').value = daAmt.toFixed(2);

    const convAmt = parseFloat(document.getElementById('amtConveyance').value) || 0;
    const medAmt = parseFloat(document.getElementById('amtMedical').value) || 0;

    let specialAmt = manualOverrides.special ? (parseFloat(document.getElementById('amtSpecial').value) || 0) : Math.max(0, gross - (basicAmt + daAmt + convAmt + medAmt));
    if (!manualOverrides.special) {
        document.getElementById('amtSpecial').value = specialAmt.toFixed(2);
    } else {
        gross = basicAmt + daAmt + convAmt + medAmt + specialAmt;
        eGross.value = gross.toFixed(2);
    }

    const pfPerc = parseFloat(document.getElementById('percPF').value) || 0;
    let pfAmt = manualOverrides.pf ? (parseFloat(document.getElementById('amtPF').value) || 0) : (basicAmt * pfPerc) / 100;
    if (!manualOverrides.pf) document.getElementById('amtPF').value = pfAmt.toFixed(2);

    const esiPerc = parseFloat(document.getElementById('percESI').value) || 0;
    let esiAmt = manualOverrides.esi ? (parseFloat(document.getElementById('amtESI').value) || 0) : (gross * esiPerc) / 100;
    if (!manualOverrides.esi) document.getElementById('amtESI').value = esiAmt.toFixed(2);

    const ptAmt = parseFloat(document.getElementById('amtPT').value) || 0;
    const leaves = parseFloat(currentEmp.paid_leaves_taken) || 0;
    let leaveDeductAmt = 0;
    if (leaves > 2) {
        leaveDeductAmt = (gross / 30) * (leaves - 2);
        document.getElementById('leaveDeductRow').style.display = 'table-row';
        document.getElementById('extraLeavesCount').innerText = leaves - 2;
        document.getElementById('amtLeaveDeduct').innerText = leaveDeductAmt.toFixed(2);
        document.getElementById('annLeaveDeduct').innerText = (leaveDeductAmt * 12).toFixed(2);
    } else {
        document.getElementById('leaveDeductRow').style.display = 'none';
        document.getElementById('amtLeaveDeduct').innerText = '0';
        document.getElementById('annLeaveDeduct').innerText = '0';
    }

    const totalDeductions = pfAmt + esiAmt + ptAmt + leaveDeductAmt;
    document.getElementById('amtTotalDeductions').innerText = totalDeductions.toFixed(2);
    document.getElementById('annTotalDeductions').innerText = (totalDeductions * 12).toFixed(2);

    const netSalary = gross - totalDeductions;
    document.getElementById('amtNetSalary').innerText = netSalary.toFixed(2);
    document.getElementById('annNetSalary').innerText = (netSalary * 12).toFixed(2);

    const empPFPerc = parseFloat(document.getElementById('percEmpPF').value) || 0;
    const empPFAmt = (basicAmt * empPFPerc) / 100;
    document.getElementById('amtEmpPF').innerText = empPFAmt.toFixed(2);
    document.getElementById('annEmpPF').innerText = (empPFAmt * 12).toFixed(2);

    const empESIPerc = parseFloat(document.getElementById('percEmpESI').value) || 0;
    const empESIAmt = (gross * empESIPerc) / 100;
    document.getElementById('amtEmpESI').innerText = empESIAmt.toFixed(2);
    document.getElementById('annEmpESI').innerText = (empESIAmt * 12).toFixed(2);

    const ctc = gross + empPFAmt + empESIAmt;
    document.getElementById('amtCTC').innerText = ctc.toFixed(2);
    document.getElementById('annCTC').innerText = (ctc * 12).toFixed(2);

    document.getElementById('annBasic').innerText = (basicAmt * 12).toFixed(2);
    document.getElementById('annDA').innerText = (daAmt * 12).toFixed(2);
    document.getElementById('annConveyance').innerText = (convAmt * 12).toFixed(2);
    document.getElementById('annMedical').innerText = (medAmt * 12).toFixed(2);
    document.getElementById('annSpecial').innerText = (specialAmt * 12).toFixed(2);
    document.getElementById('annGross').innerText = (gross * 12).toFixed(2);
    document.getElementById('annPF').innerText = (pfAmt * 12).toFixed(2);
    document.getElementById('annESI').innerText = (esiAmt * 12).toFixed(2);
    document.getElementById('annPT').innerText = (ptAmt * 12).toFixed(2);
}

async function savePayslip() {
    if (!currentEmp) return;
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    const customData = {
        percBasic: document.getElementById('percBasic').value,
        percDA: document.getElementById('percDA').value,
        amtConveyance: document.getElementById('amtConveyance').value,
        amtMedical: document.getElementById('amtMedical').value,
        percPF: document.getElementById('percPF').value,
        percESI: document.getElementById('percESI').value,
        amtPT: document.getElementById('amtPT').value,
        percEmpPF: document.getElementById('percEmpPF').value,
        percEmpESI: document.getElementById('percEmpESI').value
    };
    if (manualOverrides.basic) customData.amtBasic = document.getElementById('amtBasic').value;
    if (manualOverrides.da) customData.amtDA = document.getElementById('amtDA').value;
    if (manualOverrides.special) customData.amtSpecial = document.getElementById('amtSpecial').value;
    if (manualOverrides.pf) customData.amtPF = document.getElementById('amtPF').value;
    if (manualOverrides.esi) customData.amtESI = document.getElementById('amtESI').value;

    const psPayload = {
        emp_code: currentEmp.emp_code,
        month: document.getElementById('monthSelector').value,
        year: document.getElementById('yearSelector').value,
        generated_date: new Date().toLocaleDateString('en-GB'),
        gross: document.getElementById('amtGross').value,
        basic: document.getElementById('amtBasic').value,
        da: document.getElementById('amtDA').value,
        conveyance: document.getElementById('amtConveyance').value,
        medical: document.getElementById('amtMedical').value,
        special: document.getElementById('amtSpecial').value,
        pf: document.getElementById('amtPF').value,
        esi: document.getElementById('amtESI').value,
        pt: document.getElementById('amtPT').value,
        leave_deduction: document.getElementById('amtLeaveDeduct').innerText,
        total_deductions: document.getElementById('amtTotalDeductions').innerText,
        net_salary: document.getElementById('amtNetSalary').innerText,
        employer_pf: document.getElementById('amtEmpPF').innerText,
        employer_esi: document.getElementById('amtEmpESI').innerText,
        ctc: document.getElementById('amtCTC').innerText,
        status: 'Paid'
    };

    try {
        await fetch('api.php?action=save_employee', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...currentEmp, salary_config_json: customData, gross_monthly: psPayload.gross })
        });

        const res = await fetch('api.php?action=save_payslip', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(psPayload)
        });
        const data = await res.json();
        if (data.status === 'success') {
            allEmployees = await fetchEmployees();
            currentEmp = allEmployees.find(e => e.emp_code === psPayload.emp_code);
            document.getElementById('slipDoc').classList.add('is-locked');
            isLocked = true;
            document.getElementById('unlockBtn').innerHTML = '<i class="fa-solid fa-lock"></i> Unlock for Editing';
            alert('✅ Payslip saved successfully for ' + psPayload.month + ' ' + psPayload.year);
        } else {
            alert('Error: ' + (data.message || 'Save failed'));
        }
    } catch (e) { alert('Server connection error.'); }

    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-file-circle-check"></i> Generate & Save Payslip';
}

/*
function downloadPDF() {

    const element = document.querySelector('.payslip-doc');

    // 👉 Apply A4 width temporarily
    const originalWidth = element.style.width;
    element.style.width = "794px";

    document.querySelectorAll('.no-print-border')
        .forEach(el => el.style.border = 'none');

    const opt = {
        margin: 0.2,
        filename: `${currentEmp.emp_code}_SalarySlip_${monthSelector.value}${yearSelector.value}.pdf`,
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 3 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {

        // 👉 Restore original width
        element.style.width = originalWidth;

        document.querySelectorAll('.no-print-border')
            .forEach(el => el.style.border = '1px solid #cbd5e1');
    });
}
*/

function downloadPDF() {

    const element = document.querySelector('.payslip-doc');

    generatePDFBlob(element).then(function (pdfBlob) {

        const link = document.createElement("a");
        link.href = URL.createObjectURL(pdfBlob);
        link.download = `${currentEmp.emp_code}_SalarySlip_${monthSelector.value}${yearSelector.value}.pdf`;
        link.click();

    });
}



// Dynamic year and month selection in payslips
const months = [
    "JAN", "FEB", "MAR", "APR", "MAY", "JUN",
    "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"
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

