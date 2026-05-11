document.addEventListener('DOMContentLoaded', () => {
    // Session Check
    const sessionCode = localStorage.getItem('sessionCode');
    if(!sessionCode) window.location.href = 'index.php?page=login';

    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.removeItem('sessionCode');
        window.location.href = 'index.php?page=login';
    });

    const monthEl = document.getElementById('selectMonth');
    const yearEl = document.getElementById('selectYear');
    const dispTitle = document.getElementById('displayMonthTitle');
    function updateTitle() { dispTitle.innerText = `Salary Slip for ${monthEl.value} ${yearEl.value}`; }
    monthEl.addEventListener('change', updateTitle);
    yearEl.addEventListener('input', updateTitle);

    // Dynamic Header Reflections
    const dbEmpName = document.getElementById('dbEmpName');
    const dbEmpCode = document.getElementById('dbEmpCode');
    const dbDesignation = document.getElementById('dbDesignation');
    
    dbEmpName.addEventListener('input', () => { document.getElementById('dispName').innerText = dbEmpName.value || '-'; });
    dbEmpCode.addEventListener('input', () => { document.getElementById('dispCode').innerText = dbEmpCode.value || '-'; });
    dbDesignation.addEventListener('input', () => { document.getElementById('dispDesignation').innerText = dbDesignation.value || '-'; });

    // Dynamic Leave Reflections
    const leavesTaken = document.getElementById('leavesTaken');
    leavesTaken.addEventListener('input', () => {
        document.getElementById('dispLeaves').innerText = leavesTaken.value || '0';
        updateCalculations(); 
    });

    // Calculations
    const eGross = document.getElementById('amtGross');
    const allInputs = document.querySelectorAll('input.editable-amt, input.editable-perc, #leavesTaken, #amtSpecial, #amtPF, #amtESI');

    let manualOverrides = {
        basic: false, da: false, special: false, pf: false, esi: false
    };

    allInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            const id = e.target.id;
            if(id === 'amtBasic') manualOverrides.basic = true;
            if(id === 'amtDA') manualOverrides.da = true;
            if(id === 'amtSpecial') manualOverrides.special = true;
            if(id === 'amtPF') manualOverrides.pf = true;
            if(id === 'amtESI') manualOverrides.esi = true;
            if(id === 'amtGross') {
                // If Gross changes manually, we should recalculate the top-down values and reset special manual override
                manualOverrides.special = false;
                manualOverrides.basic = false;
                manualOverrides.da = false;
            }
            updateCalculations();
        });
    });

    function updateCalculations() {
        // If special allowance is overridden, the gross should equal sum of all earnings
        let gross = parseFloat(eGross.value) || 0;
        
        const basicPerc = parseFloat(document.getElementById('percBasic').value) || 0;
        let basicAmt = manualOverrides.basic ? (parseFloat(document.getElementById('amtBasic').value)||0) : (gross * basicPerc) / 100;
        if(!manualOverrides.basic) document.getElementById('amtBasic').value = basicAmt.toFixed(2);

        const daPerc = parseFloat(document.getElementById('percDA').value) || 0;
        let daAmt = manualOverrides.da ? (parseFloat(document.getElementById('amtDA').value)||0) : (gross * daPerc) / 100;
        if(!manualOverrides.da) document.getElementById('amtDA').value = daAmt.toFixed(2);

        const convAmt = parseFloat(document.getElementById('amtConveyance').value) || 0;
        const medAmt = parseFloat(document.getElementById('amtMedical').value) || 0;

        let specialAmt = manualOverrides.special ? (parseFloat(document.getElementById('amtSpecial').value)||0) : gross - (basicAmt + daAmt + convAmt + medAmt);
        if(!manualOverrides.special) {
            document.getElementById('amtSpecial').value = specialAmt.toFixed(2);
        } else {
            // Admin requested: If I make changes in special allowance, let's recalcluate GROSS to match the new sum!
            gross = basicAmt + daAmt + convAmt + medAmt + specialAmt;
            eGross.value = gross.toFixed(2);
        }

        const pfPerc = parseFloat(document.getElementById('percPF').value) || 0;
        let pfAmt = manualOverrides.pf ? (parseFloat(document.getElementById('amtPF').value)||0) : (basicAmt * pfPerc) / 100;
        if(!manualOverrides.pf) document.getElementById('amtPF').value = pfAmt.toFixed(2);

        const esiPerc = parseFloat(document.getElementById('percESI').value) || 0;
        let esiAmt = manualOverrides.esi ? (parseFloat(document.getElementById('amtESI').value)||0) : (gross * esiPerc) / 100;
        if(!manualOverrides.esi) document.getElementById('amtESI').value = esiAmt.toFixed(2);

        const ptAmt = parseFloat(document.getElementById('amtPT').value) || 0;

        const leaves = parseFloat(leavesTaken.value) || 0;
        let leaveDeductAmt = 0;
        if (leaves > 2) {
            leaveDeductAmt = (gross / 30) * (leaves - 2);
            document.getElementById('leaveDeductRow').style.display = 'table-row';
            document.getElementById('extraLeavesCount').innerText = leaves - 2;
            document.getElementById('amtLeaveDeduct').innerText = leaveDeductAmt.toFixed(2);
        } else {
            document.getElementById('leaveDeductRow').style.display = 'none';
        }

        const totalDeductions = pfAmt + esiAmt + ptAmt + leaveDeductAmt;
        document.getElementById('amtTotalDeductions').innerText = totalDeductions.toFixed(2);

        const netSalary = gross - totalDeductions;
        document.getElementById('amtNetSalary').innerText = netSalary.toFixed(2);

        const empPFPerc = parseFloat(document.getElementById('percEmpPF').value) || 0;
        const empPFAmt = (basicAmt * empPFPerc) / 100;
        document.getElementById('amtEmpPF').innerText = empPFAmt.toFixed(2);

        const empESIPerc = parseFloat(document.getElementById('percEmpESI').value) || 0;
        const empESIAmt = (gross * empESIPerc) / 100;
        document.getElementById('amtEmpESI').innerText = empESIAmt.toFixed(2);

        const ctc = gross + empPFAmt + empESIAmt;
        document.getElementById('amtCTC').innerText = ctc.toFixed(2);

        // Annual Updates
        document.getElementById('annBasic').innerText = (basicAmt * 12).toFixed(2);
        document.getElementById('annDA').innerText = (daAmt * 12).toFixed(2);
        document.getElementById('annConveyance').innerText = (convAmt * 12).toFixed(2);
        document.getElementById('annMedical').innerText = (medAmt * 12).toFixed(2);
        document.getElementById('annSpecial').innerText = (specialAmt * 12).toFixed(2);
        document.getElementById('annGross').innerText = (gross * 12).toFixed(2);
        document.getElementById('annPF').innerText = (pfAmt * 12).toFixed(2);
        document.getElementById('annESI').innerText = (esiAmt * 12).toFixed(2);
        document.getElementById('annPT').innerText = (ptAmt * 12).toFixed(2);
        document.getElementById('annLeaveDeduct').innerText = (leaveDeductAmt * 12).toFixed(2);
        document.getElementById('annTotalDeductions').innerText = (totalDeductions * 12).toFixed(2);
        document.getElementById('annNetSalary').innerText = (netSalary * 12).toFixed(2);
        document.getElementById('annEmpPF').innerText = (empPFAmt * 12).toFixed(2);
        document.getElementById('annEmpESI').innerText = (empESIAmt * 12).toFixed(2);
        document.getElementById('annCTC').innerText = (ctc * 12).toFixed(2);
    }

    // Connect to XAMPP API
    document.getElementById('searchBtn').addEventListener('click', async () => {
        const code = document.getElementById('searchInput').value.trim();
        const msg = document.getElementById('searchMsg');
        try {
            const res = await fetch(`sync.php?action=get_user&code=${code}`);
            const payload = await res.json();
            if(payload.status === 'success') {
                const emp = payload.employee;
                dbEmpName.value = emp.name;
                dbEmpCode.value = emp.code;
                dbDesignation.value = emp.designation;
                document.getElementById('dbGross').value = emp.gross;
                leavesTaken.value = emp.leaves;
                
                // Fire events to update header UI
                dbEmpName.dispatchEvent(new Event('input'));
                dbEmpCode.dispatchEvent(new Event('input'));
                dbDesignation.dispatchEvent(new Event('input'));
                leavesTaken.dispatchEvent(new Event('input'));

                if(emp.gross > 0) eGross.value = emp.gross;
                
                const data = emp.data || {};
                
                manualOverrides = { basic: false, da: false, special: false, pf: false, esi: false };
                
                document.getElementById('percBasic').value = data.percBasic || 50;
                if(data.amtBasic !== undefined) { document.getElementById('amtBasic').value = data.amtBasic; manualOverrides.basic = true; }
                
                document.getElementById('percDA').value = data.percDA || 30;
                if(data.amtDA !== undefined) { document.getElementById('amtDA').value = data.amtDA; manualOverrides.da = true; }
                
                document.getElementById('amtConveyance').value = data.amtConveyance || 0;
                document.getElementById('amtMedical').value = data.amtMedical || 0;
                
                if(data.amtSpecial !== undefined) { document.getElementById('amtSpecial').value = data.amtSpecial; manualOverrides.special = true; }
                
                document.getElementById('percPF').value = data.percPF || 12;
                if(data.amtPF !== undefined) { document.getElementById('amtPF').value = data.amtPF; manualOverrides.pf = true; }
                
                document.getElementById('percESI').value = data.percESI || 0.75;
                if(data.amtESI !== undefined) { document.getElementById('amtESI').value = data.amtESI; manualOverrides.esi = true; }
                
                document.getElementById('amtPT').value = data.amtPT || 0;
                document.getElementById('percEmpPF').value = data.percEmpPF || 13;
                document.getElementById('percEmpESI').value = data.percEmpESI || 3.25;

                updateCalculations();
                msg.innerText = "Loaded " + emp.name;
                msg.className = "helper-text text-success";
            } else {
                msg.innerText = "Employee not found in XAMPP";
                msg.className = "helper-text text-error";
            }
        } catch(e) {
            msg.innerText = "Error connecting to XAMPP";
            msg.className = "helper-text text-error";
        }
    });

    document.getElementById('saveBtn').addEventListener('click', async () => {
        if(!dbEmpCode.value) { alert("Code is required"); return; }
        
        let customData = {
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

        if(manualOverrides.basic) customData.amtBasic = document.getElementById('amtBasic').value;
        if(manualOverrides.da) customData.amtDA = document.getElementById('amtDA').value;
        if(manualOverrides.special) customData.amtSpecial = document.getElementById('amtSpecial').value;
        if(manualOverrides.pf) customData.amtPF = document.getElementById('amtPF').value;
        if(manualOverrides.esi) customData.amtESI = document.getElementById('amtESI').value;

        const payload = {
            code: dbEmpCode.value,
            name: dbEmpName.value,
            designation: dbDesignation.value,
            gross: eGross.value,
            leaves: leavesTaken.value,
            data: customData
        };

        const msg = document.getElementById('saveMsg');
        try {
            const res = await fetch('sync.php?action=save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const dbRes = await res.json();
            if(dbRes.status === 'success') {
                msg.innerText = "Saved to XAMPP Database successfully!";
                msg.className = "helper-text text-success";
                setTimeout(()=> msg.innerText="", 3000);
            } else {
                msg.innerText = "XAMPP Error: " + dbRes.message;
                msg.className = "helper-text text-error";
            }
        } catch(e) {
            msg.innerText = "Server Error";
        }
    });

    updateCalculations();
});

