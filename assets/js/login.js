// login.js - Secure login with validation

function switchRole(role) {
    document.getElementById('btnAdmin').classList.toggle('active', role === 'admin');
    document.getElementById('btnEmployee').classList.toggle('active', role === 'employee');
    document.getElementById('adminSection').classList.toggle('active', role === 'admin');
    document.getElementById('employeeSection').classList.toggle('active', role === 'employee');
}

function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId), i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
}

async function attemptLogin(loginId, pwd, formType, errorEl, btn) {
    if (!loginId) { errorEl.innerText = 'Login ID is required.'; return; }
    if (!pwd)     { errorEl.innerText = 'Password is required.'; return; }

    errorEl.style.color = '#64748b';
    errorEl.innerText = 'Signing in...';
    btn.disabled = true;

    try {
        const res = await fetch('api.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ login_id: loginId, password: pwd, type: formType })
        });
        const data = await res.json();
        if (data.status === 'success') {
            localStorage.setItem('sessionUser', JSON.stringify(data.user));
            const role = data.user.role_name;
            if (role === 'Admin' || role === 'HR Manager' || data.user.role_id <= 2) {
                window.location.href = 'index.php?page=admin_dashboard';
            } else {
                window.location.href = 'index.php?page=employee_dashboard';
            }
        } else {
            errorEl.style.color = '#ef4444';
            errorEl.innerText = data.message || 'Invalid credentials. Please try again.';
            btn.disabled = false;
        }
    } catch (err) {
        errorEl.style.color = '#ef4444';
        errorEl.innerText = 'Server error. Make sure XAMPP Apache & MySQL are running.';
        btn.disabled = false;
    }
}

document.getElementById('adminForm').addEventListener('submit', (e) => {
    e.preventDefault();
    attemptLogin(
        document.getElementById('adminLoginId').value.trim(),
        document.getElementById('adminPassword').value,
        'admin',
        document.getElementById('adminError'),
        e.submitter || document.querySelector('#adminForm button[type=submit]')
    );
});

document.getElementById('employeeForm').addEventListener('submit', (e) => {
    e.preventDefault();
    attemptLogin(
        document.getElementById('empLoginId').value.trim(),
        document.getElementById('empPassword').value,
        'employee',
        document.getElementById('empError'),
        e.submitter || document.querySelector('#employeeForm button[type=submit]')
    );
});

// Clear error on typing
['adminLoginId','adminPassword','empLoginId','empPassword'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => {
        const err = document.getElementById(id.includes('admin') ? 'adminError' : 'empError');
        if (err) err.innerText = '';
    });
});
