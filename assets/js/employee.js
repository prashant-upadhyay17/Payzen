document.addEventListener('DOMContentLoaded', () => {
    // Session Check
    const sessionUser = JSON.parse(localStorage.getItem('sessionUser') || 'null');
const sessionCode = sessionUser ? sessionUser.emp_code : null;
    if(!sessionCode) window.location.href = 'index.php?page=login';

    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.removeItem('sessionCode');
        window.location.href = 'index.php?page=login';
    });

    const slipContainer = document.getElementById('slipContainer');
    document.getElementById('dispEmpCode').innerText = "Logged in as: " + sessionCode;

    document.getElementById('generateBtn').addEventListener('click', async () => {
        const month = document.getElementById('selectMonth').value;
        const year = document.getElementById('selectYear').value;
        document.getElementById('displayMonthTitle').innerText = `Salary Slip for ${month} ${year}`;

        try {
            const res = await fetch(`sync.php?action=get_user&code=${sessionCode}`);
            const payload = await res.json();
            
            if(payload.status === 'success' && payload.employee) {
                renderSlip(payload.employee);
                slipContainer.style.display = 'flex';
            } else {
                alert("Could not load your records. Please contact HR.");
            }
        } catch(e) {
            alert("Database connection failed.");
        }
    });

    function renderSlip(emp) {
        document.getElementById('dispName').innerText = emp.name;
        document.getElementById('dispDesignation').innerText = emp.designation;
        document.getElementById('dispCode').innerText = emp.code;
        document.getElementById('dispLeaves').innerText = emp.leaves;

        const data = emp.data || {};
        const gross = parseFloat(emp.gross) || 0;
        
        let customBasic = parseFloat(data.percBasic) || 50;
        let customDA = parseFloat(data.percDA) || 30;
        let customPF = parseFloat(data.percPF) || 12;
        let customESI = parseFloat(data.percESI) || 0.75;
        let customEmpPF = parseFloat(data.percEmpPF) || 13;
        let customEmpESI = parseFloat(data.percEmpESI) || 3.25;

        let basicAmt = parseFloat(data.amtBasic);
        if(isNaN(basicAmt)) basicAmt = gross * (customBasic/100);
        document.getElementById('amtBasic').innerText = basicAmt.toFixed(2);
        document.getElementById('percBasic').innerText = customBasic + '%';

        let daAmt = parseFloat(data.amtDA);
        if(isNaN(daAmt)) daAmt = gross * (customDA/100);
        document.getElementById('amtDA').innerText = daAmt.toFixed(2);
        document.getElementById('percDA').innerText = customDA + '%';

        let convAmt = parseFloat(data.amtConveyance) || 0;
        document.getElementById('amtConveyance').innerText = convAmt.toFixed(2);
        let medAmt = parseFloat(data.amtMedical) || 0;
        document.getElementById('amtMedical').innerText = medAmt.toFixed(2);

        let specialAmt = parseFloat(data.amtSpecial);
        if(isNaN(specialAmt)) specialAmt = gross - (basicAmt + daAmt + convAmt + medAmt);
        document.getElementById('amtSpecial').innerText = specialAmt.toFixed(2);

        document.getElementById('amtGross').innerText = gross.toFixed(2);

        let pfAmt = parseFloat(data.amtPF);
        if(isNaN(pfAmt)) pfAmt = basicAmt * (customPF/100);
        document.getElementById('amtPF').innerText = pfAmt.toFixed(2);
        document.getElementById('percPF').innerText = customPF + '%';

        let esiAmt = parseFloat(data.amtESI);
        if(isNaN(esiAmt)) esiAmt = gross * (customESI/100);
        document.getElementById('amtESI').innerText = esiAmt.toFixed(2);
        document.getElementById('percESI').innerText = customESI + '%';

        let ptAmt = parseFloat(data.amtPT) || 0;
        document.getElementById('amtPT').innerText = ptAmt.toFixed(2);

        const leaves = parseFloat(emp.leaves) || 0;
        let deductAmt = 0;
        if(leaves > 2) {
            deductAmt = (gross / 30) * (leaves - 2);
            document.getElementById('leaveDeductRow').style.display = 'table-row';
            document.getElementById('extraLeavesCount').innerText = leaves - 2;
            document.getElementById('amtLeaveDeduct').innerText = deductAmt.toFixed(2);
        } else {
            document.getElementById('leaveDeductRow').style.display = 'none';
        }

        const totalDeductions = pfAmt + esiAmt + ptAmt + deductAmt;
        document.getElementById('amtTotalDeductions').innerText = totalDeductions.toFixed(2);
        
        const net = gross - totalDeductions;
        document.getElementById('amtNetSalary').innerText = net.toFixed(2);

        let empPFAmt = basicAmt * (customEmpPF/100);
        document.getElementById('amtEmpPF').innerText = empPFAmt.toFixed(2);
        document.getElementById('percEmpPF').innerText = customEmpPF + '%';

        let empESIAmt = gross * (customEmpESI/100);
        document.getElementById('amtEmpESI').innerText = empESIAmt.toFixed(2);
        document.getElementById('percEmpESI').innerText = customEmpESI + '%';

        const ctc = gross + empPFAmt + empESIAmt;
        document.getElementById('amtCTC').innerText = ctc.toFixed(2);

        // Annuals
        document.getElementById('annBasic').innerText = (basicAmt * 12).toFixed(2);
        document.getElementById('annDA').innerText = (daAmt * 12).toFixed(2);
        document.getElementById('annConveyance').innerText = (convAmt * 12).toFixed(2);
        document.getElementById('annMedical').innerText = (medAmt * 12).toFixed(2);
        document.getElementById('annSpecial').innerText = (specialAmt * 12).toFixed(2);
        document.getElementById('annGross').innerText = (gross * 12).toFixed(2);
        document.getElementById('annPF').innerText = (pfAmt * 12).toFixed(2);
        document.getElementById('annESI').innerText = (esiAmt * 12).toFixed(2);
        document.getElementById('annPT').innerText = (ptAmt * 12).toFixed(2);
        document.getElementById('annLeaveDeduct').innerText = (deductAmt * 12).toFixed(2);
        document.getElementById('annTotalDeductions').innerText = (totalDeductions * 12).toFixed(2);
        document.getElementById('annNetSalary').innerText = (net * 12).toFixed(2);
        document.getElementById('annEmpPF').innerText = (empPFAmt * 12).toFixed(2);
        document.getElementById('annEmpESI').innerText = (empESIAmt * 12).toFixed(2);
        document.getElementById('annCTC').innerText = (ctc * 12).toFixed(2);
    }
});


function logout() { localStorage.removeItem('sessionUser'); window.location.href = 'index.php?page=login'; }

