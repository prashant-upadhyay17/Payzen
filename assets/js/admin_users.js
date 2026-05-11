let allUsers = [];

document.addEventListener('DOMContentLoaded', async () => {
    await loadTable();

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateUserForm()) return;

        const id  = document.getElementById('userId').value;
        const payload = {
            id:         id || null,
            first_name: document.getElementById('fn').value.trim(),
            last_name:  document.getElementById('ln').value.trim(),
            email:      document.getElementById('em').value.trim(),
            role_id:    document.getElementById('roleId').value,
            is_active:  document.getElementById('userStatus').value
        };
        if (!id) {
            payload.password = document.getElementById('userPwd').value;
        }

        try {
            const res  = await fetch('api.php?action=save_user', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.status === 'success') { closeModal(); await loadTable(); }
            else showUserFormError(data.message || 'Error saving user');
        } catch (err) { showUserFormError('Server error'); }
    });
});

// ── Validation ─────────────────────────────────────────────────────────────
function validateUserForm() {
    clearUserErrors();
    let ok = true;
    const fn    = document.getElementById('fn').value.trim();
    const ln    = document.getElementById('ln').value.trim();
    const email = document.getElementById('em').value.trim();
    const id    = document.getElementById('userId').value;

    if (!fn) { setUserFieldError('fn', 'First Name required'); ok = false; }
    if (!ln) { setUserFieldError('ln', 'Last Name required'); ok = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setUserFieldError('em', 'Valid email required'); ok = false; }

    if (!id) {
        const pwd = document.getElementById('userPwd').value;
        if (!pwd || pwd.length < 8)           { setUserFieldError('userPwd', 'Min 8 characters'); ok = false; }
        else if (!/[A-Z]/.test(pwd))          { setUserFieldError('userPwd', 'Must include uppercase letter'); ok = false; }
        else if (!/[0-9]/.test(pwd))          { setUserFieldError('userPwd', 'Must include a number'); ok = false; }
        else if (!/[\W_]/.test(pwd))          { setUserFieldError('userPwd', 'Must include a special character'); ok = false; }
    }
    return ok;
}

function setUserFieldError(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.borderColor = 'var(--danger)';
    let err = document.getElementById(id + '_uerr');
    if (!err) {
        err = document.createElement('span');
        err.id = id + '_uerr';
        err.style.cssText = 'color:var(--danger);font-size:0.78rem;display:block;margin-top:3px;';
        el.parentNode.appendChild(err);
    }
    err.innerText = msg;
}

function clearUserErrors() {
    document.querySelectorAll('#userForm .form-control').forEach(el => el.style.borderColor = '');
    document.querySelectorAll('[id$="_uerr"]').forEach(el => el.remove());
    const ge = document.getElementById('userGlobalErr');
    if (ge) ge.remove();
}

function showUserFormError(msg) {
    let err = document.getElementById('userGlobalErr');
    if (!err) {
        err = document.createElement('div');
        err.id = 'userGlobalErr';
        err.style.cssText = 'color:var(--danger);background:#fef2f2;padding:10px;border-radius:8px;margin-top:10px;font-size:0.9rem;';
        document.getElementById('userForm').appendChild(err);
    }
    err.innerText = '⚠ ' + msg;
}

// ── Table ──────────────────────────────────────────────────────────────────
async function loadTable() {
    try {
        const res  = await fetch('api.php?action=get_users');
        const p    = await res.json();
        allUsers   = p.data || [];
        const tbody = document.querySelector('#usersTable tbody');
        tbody.innerHTML = '';
        if (!allUsers.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;">No users found.</td></tr>';
            return;
        }
        allUsers.forEach(u => {
            const statusTag = u.is_active === 'Y' 
                ? '<span style="color:var(--success);font-weight:700;">● Active</span>' 
                : '<span style="color:var(--danger);font-weight:700;">● Inactive</span>';
            tbody.innerHTML += `<tr>
                <td>#${u.id}</td>
                <td><strong>${u.first_name} ${u.last_name}</strong></td>
                <td>${u.email}</td>
                <td><span class="iso-mark">${u.role_name}</span></td>
                <td>${statusTag}</td>
                <td>
                    <button class="btn btn-outline" style="padding:5px 10px;" onclick="editUser(${u.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-danger"  style="padding:5px 10px;" onclick="delUser(${u.id})"  title="Delete (Soft)"><i class="fa-solid fa-user-slash"></i></button>
                </td>
            </tr>`;
        });
    } catch (e) { console.error(e); }
}

function openModal() {
    clearUserErrors();
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('modalTitle').innerText = 'Add New User';
    document.getElementById('userPwdRow').style.display = '';
    document.getElementById('userStatus').value = 'Y';
    document.getElementById('userModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
    clearUserErrors();
}

function editUser(id) {
    clearUserErrors();
    const u = allUsers.find(x => x.id == id);
    if (!u) return;
    document.getElementById('userId').value  = u.id;
    document.getElementById('fn').value      = u.first_name;
    document.getElementById('ln').value      = u.last_name;
    document.getElementById('em').value      = u.email;
    document.getElementById('roleId').value  = u.role_id;
    document.getElementById('userStatus').value = u.is_active || 'Y';
    document.getElementById('modalTitle').innerText = 'Edit User';
    document.getElementById('userPwdRow').style.display = 'none';
    document.getElementById('userModal').style.display = 'flex';
}

async function delUser(id) {
    const u = allUsers.find(x => x.id == id);
    if (id == 1) { alert('The Master Admin account cannot be deleted.'); return; }
    if (!confirm(`Remove user "${u ? u.first_name + ' ' + u.last_name : id}"?\n\nThis will deactivate their account. Data is preserved in DB.`)) return;
    try {
        const res  = await fetch(`api.php?action=delete_user&id=${id}`);
        const data = await res.json();
        if (data.status === 'success') await loadTable();
        else alert('Error: ' + (data.message || 'Could not delete'));
    } catch (e) { alert('Server error'); }
}

function exportUsersCSV() {
    if (!allUsers.length) return;
    let csv = "ID,Name,Email,Role,Status\n";
    allUsers.forEach(u => { csv += `${u.id},${u.first_name} ${u.last_name},${u.email},${u.role_name},${u.is_active}\n`; });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
    a.download = 'system_users.csv'; a.click();
}
