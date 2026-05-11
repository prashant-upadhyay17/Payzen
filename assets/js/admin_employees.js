let allEmployees = [];

document.addEventListener('DOMContentLoaded', async () => {
    await loadDesignations();
    await loadTable();

    document.getElementById('empForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateEmpForm()) return;

        const isEdit = document.getElementById('ec').readOnly;
        const payload = {
            emp_code:        document.getElementById('ec').value.trim().toUpperCase(),
            email:           document.getElementById('em').value.trim(),
            first_name:      document.getElementById('fn').value.trim(),
            last_name:       document.getElementById('ln').value.trim(),
            designation:     document.getElementById('desig').value,
            package:         parseFloat(document.getElementById('pkg').value),
            gross_monthly:   parseFloat(document.getElementById('gross').value),
            paid_leaves_taken: parseFloat(document.getElementById('lvs').value) || 0,
            is_active:       document.getElementById('isActive').value
        };
        // Include password only for new employee
        if (!isEdit) {
            const pwd = document.getElementById('empPwd').value;
            if (!validatePassword(pwd, 'empPwdErr')) return;
            payload.password = pwd;
        }

        try {
            const res  = await fetch('api.php?action=save_employee', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.status === 'success') { closeModal(); await loadTable(); }
            else showFormError(data.message || 'Error saving employee');
        } catch (err) { showFormError('Server error. Check if XAMPP is running.'); }
    });
});

// ── Validation ────────────────────────────────────────────────────────────────
function validateEmpForm() {
    clearFormErrors();
    let ok = true;
    const code  = document.getElementById('ec').value.trim();
    const email = document.getElementById('em').value.trim();
    const fn    = document.getElementById('fn').value.trim();
    const ln    = document.getElementById('ln').value.trim();
    const pkg   = parseFloat(document.getElementById('pkg').value);
    const gross = parseFloat(document.getElementById('gross').value);

    if (!code) { setFieldError('ec', 'Employee Code is required'); ok = false; }
    if (!fn)   { setFieldError('fn', 'First Name is required'); ok = false; }
    if (!ln)   { setFieldError('ln', 'Last Name is required'); ok = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setFieldError('em', 'Valid email required'); ok = false; }
    if (!pkg || pkg <= 0) { setFieldError('pkg', 'Package must be > 0'); ok = false; }
    if (!gross || gross <= 0) { setFieldError('gross', 'Gross Monthly must be > 0'); ok = false; }
    return ok;
}

function validatePassword(pwd, errId) {
    const el = document.getElementById(errId);
    if (!pwd || pwd.length < 8) { el.innerText = 'Min 8 characters required'; return false; }
    if (!/[A-Z]/.test(pwd)) { el.innerText = 'Must contain an uppercase letter'; return false; }
    if (!/[0-9]/.test(pwd)) { el.innerText = 'Must contain a number'; return false; }
    if (!/[\W_]/.test(pwd)) { el.innerText = 'Must contain a special character'; return false; }
    el.innerText = '';
    return true;
}

function setFieldError(id, msg) {
    const el = document.getElementById(id);
    if (el) {
        el.style.borderColor = 'var(--danger)';
        let err = document.getElementById(id + '_err');
        if (!err) { err = document.createElement('span'); err.id = id+'_err'; err.style.cssText='color:var(--danger);font-size:0.78rem;display:block;margin-top:3px'; el.parentNode.appendChild(err); }
        err.innerText = msg;
    }
}

function clearFormErrors() {
    document.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');
    document.querySelectorAll('[id$="_err"]').forEach(el => el.remove());
}

function showFormError(msg) {
    let err = document.getElementById('formGlobalErr');
    if (!err) { err = document.createElement('div'); err.id='formGlobalErr'; err.style.cssText='color:var(--danger);background:#fef2f2;padding:10px;border-radius:8px;margin-top:10px;font-size:0.9rem;'; document.getElementById('empForm').appendChild(err); }
    err.innerText = '⚠ ' + msg;
}

// ── Table ─────────────────────────────────────────────────────────────────────
async function loadTable() {
    try {
        allEmployees = await fetchEmployees();
        const tbody = document.querySelector('#empTable tbody');
        tbody.innerHTML = '';
        if (!allEmployees.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-secondary)">No employees found. Add one!</td></tr>';
            return;
        }
        allEmployees.forEach(emp => {
            const activeTag = emp.is_active === 'Y'
                ? '<span style="color:var(--success);font-weight:700;">● Active</span>'
                : '<span style="color:var(--danger);font-weight:700;">● Inactive</span>';
            tbody.innerHTML += `
                <tr>
                    <td><strong>${emp.emp_code}</strong></td>
                    <td>${emp.first_name} ${emp.last_name}</td>
                    <td>${emp.email}</td>
                    <td>${emp.designation_name || emp.designation}</td>
                    <td>₹${parseFloat(emp.package).toLocaleString('en-IN')}</td>
                    <td>${activeTag}</td>
                    <td>
                        <button class="btn btn-outline" style="padding:5px 10px;font-size:0.8rem;" onclick="editEmp('${emp.emp_code}')" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-primary" style="padding:5px 10px;font-size:0.8rem;" onclick="goToSlip('${emp.emp_code}')" title="Generate Payslip"><i class="fa-solid fa-file-invoice-dollar"></i></button>
                        <button class="btn btn-danger" style="padding:5px 10px;font-size:0.8rem;" onclick="delEmp('${emp.emp_code}')" title="Remove (Soft Delete)"><i class="fa-solid fa-user-slash"></i></button>
                    </td>
                </tr>`;
        });
    } catch (e) { console.error(e); }
}

async function loadDesignations() {
    try {
        const res = await fetch('api.php?action=get_designations');
        const data = await res.json();
        if (data.status === 'success') {
            const select = document.getElementById('desig');
            if (select) {
                select.innerHTML = '';
                data.data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.designation_name;
                    select.appendChild(opt);
                });
            }
        }
    } catch (e) { console.error('Error loading designations', e); }
}

function goToSlip(code) {
    localStorage.setItem('slipEmpCode', code);
    window.location.href = 'index.php?page=admin_payslips';
}

function openModal() {
    clearFormErrors();
    document.getElementById('empForm').reset();
    document.getElementById('ec').readOnly = false;
    document.getElementById('modalTitle').innerText = 'Add New Employee';
    document.getElementById('pwdRow').style.display = '';
    document.getElementById('empModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('empModal').style.display = 'none';
    clearFormErrors();
    const ge = document.getElementById('formGlobalErr');
    if (ge) ge.remove();
}

function editEmp(code) {
    clearFormErrors();
    const emp = allEmployees.find(e => e.emp_code === code);
    if (!emp) return;
    document.getElementById('ec').value     = emp.emp_code;
    document.getElementById('ec').readOnly  = true;
    document.getElementById('em').value     = emp.email;
    document.getElementById('fn').value     = emp.first_name;
    document.getElementById('ln').value     = emp.last_name;
    document.getElementById('desig').value  = emp.designation;
    document.getElementById('pkg').value    = emp.package;
    document.getElementById('gross').value  = emp.gross_monthly;
    document.getElementById('lvs').value    = emp.paid_leaves_taken;
    document.getElementById('isActive').value = emp.is_active || 'Y';
    document.getElementById('modalTitle').innerText = 'Edit Employee';
    document.getElementById('pwdRow').style.display = 'none'; // hide pwd for edit
    document.getElementById('empModal').style.display = 'flex';
}

async function delEmp(code) {
    const emp = allEmployees.find(e => e.emp_code === code);
    const name = emp ? `${emp.first_name} ${emp.last_name}` : code;
    if (!confirm(`Remove employee "${name}" (${code})?\n\nThis will deactivate their account. Their records are preserved in the database.`)) return;
    try {
        const res  = await fetch(`api.php?action=delete_employee&code=${code}`);
        const data = await res.json();
        if (data.status === 'success') await loadTable();
        else alert('Error: ' + (data.message || 'Could not delete'));
    } catch (e) { alert('Server error'); }
}

function exportCSV() {
    if (!allEmployees.length) return;
    let csv = "Code,First Name,Last Name,Email,Designation,Package,Gross Monthly,Active\n";
    allEmployees.forEach(e => {
        csv += `${e.emp_code},${e.first_name},${e.last_name},${e.email},${e.designation},${e.package},${e.gross_monthly},${e.is_active}\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob); a.download = 'employees.csv'; a.click();
}
