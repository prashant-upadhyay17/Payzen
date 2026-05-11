<?php include 'includes/sidebar_admin.php'; ?>
<style>
    .account-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 1200px; }
    @media (max-width: 992px) { .account-grid { grid-template-columns: 1fr; max-width: 100%; } }
</style>
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">My Account Settings</h1>
        </div>

        <div class="account-grid">
            <div class="chart-container">
                <h3 style="margin-bottom:20px; color:var(--text-primary);"><i class="fa-solid fa-id-card"></i> Profile Details</h3>
                <div class="emp-details-grid" style="grid-template-columns: 1fr;">
                    <div class="emp-detail-item"><div class="emp-detail-label">Name:</div><div class="emp-detail-value" id="accName">-</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">Login Email:</div><div class="emp-detail-value" id="accEmail">-</div></div>
                    <div class="emp-detail-item"><div class="emp-detail-label">Role Level:</div><div class="emp-detail-value iso-mark" style="border:none; padding:0; color:var(--primary-color)" id="accRole">-</div></div>
                </div>
            </div>

            <div class="chart-container">
                <h3 style="margin-bottom:20px; color:var(--danger);"><i class="fa-solid fa-lock"></i> Security &amp; Password</h3>
                <form id="pwdForm">
                    <div class="form-group">
                        <label>New Password</label>
                        <div style="position:relative;">
                            <input type="password" class="form-control" id="newPwd" required minlength="6" style="padding-right:45px;">
                            <button type="button" onclick="togglePwd('newPwd','eyeNew')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:1rem;"><i class="fa-solid fa-eye" id="eyeNew"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div style="position:relative;">
                            <input type="password" class="form-control" id="confirmPwd" required minlength="6" style="padding-right:45px;">
                            <button type="button" onclick="togglePwd('confirmPwd','eyeConfirm')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:1rem;"><i class="fa-solid fa-eye" id="eyeConfirm"></i></button>
                        </div>
                    </div>
                    <div id="pwdError" style="color:var(--danger); margin-bottom:15px; font-size:0.9rem;"></div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-key"></i> Update Password</button>
                </form>
            </div>
        </div>
    </main>
<script src="assets/js/admin_shared.js"></script>
<script>
    function togglePwd(fieldId, iconId) {
        const f = document.getElementById(fieldId);
        const i = document.getElementById(iconId);
        if (f.type === 'password') { f.type = 'text'; i.className = 'fa-solid fa-eye-slash'; }
        else { f.type = 'password'; i.className = 'fa-solid fa-eye'; }
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('accName').innerText = `${sessionUser.first_name} ${sessionUser.last_name}`;
        document.getElementById('accEmail').innerText = sessionUser.email;
        document.getElementById('accRole').innerText = sessionUser.role_name;

        document.getElementById('pwdForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const p1 = document.getElementById('newPwd').value;
            const p2 = document.getElementById('confirmPwd').value;
            const err = document.getElementById('pwdError');
            err.style.color = 'var(--danger)';
            if (p1 !== p2) { err.innerText = "Passwords do not match!"; return; }
            err.innerText = "Updating...";
            try {
                const res = await fetch('api.php?action=update_password', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: 'Admin', email: sessionUser.email, new_password: p1 })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    err.style.color = "var(--success)";
                    err.innerText = "✅ " + (data.message || "Password updated successfully!");
                    document.getElementById('pwdForm').reset();
                    setTimeout(() => err.innerText = '', 3000);
                } else { 
                    err.innerText = data.message || "Error updating password."; 
                }
            } catch (err2) { 
                err.innerText = "Server connection error."; 
            }
        });
    });
</script>
