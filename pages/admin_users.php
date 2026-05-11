<?php include 'includes/sidebar_admin.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage System Admins</h1>
        </div>

        <div class="action-bar">
            <button class="btn btn-primary" onclick="openModal()"><i class="fa-solid fa-user-plus"></i> Add New User</button>
            <button class="btn btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
            <button class="btn btn-outline" onclick="exportUsersCSV()"><i class="fa-solid fa-file-excel"></i> Export Excel (CSV)</button>
        </div>

        <div class="table-container">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>

    <div class="modal-overlay" id="userModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add New User</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="userForm" novalidate>
                <input type="hidden" id="userId">
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" class="form-control" id="fn" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" class="form-control" id="ln" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <label>Email (Login ID) <span style="color:var(--danger)">*</span></label>
                        <input type="email" class="form-control" id="em" placeholder="admin@company.com" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" id="roleId" required>
                            <option value="1">Master Admin</option>
                            <option value="2">HR Manager</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="userStatus">
                            <option value="Y">🟢 Active</option>
                            <option value="N">🔴 Inactive</option>
                        </select>
                    </div>
                    <!-- Password: only for new users -->
                    <div class="form-group" id="userPwdRow" style="grid-column: 1 / -1;">
                        <label>Password <span style="color:var(--danger)">*</span><small style="font-weight:400;color:var(--text-secondary);margin-left:8px;">(min 8 chars, uppercase, number, special char)</small></label>
                        <div style="position:relative;">
                            <input type="password" class="form-control" id="userPwd" placeholder="e.g. Admin@2025" style="padding-right:45px;">
                            <button type="button" onclick="toggleUserPwd()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:1rem;"><i class="fa-solid fa-eye" id="eyeUserPwd"></i></button>
                        </div>
                        <div id="userPwdStrengthBar" style="height:4px;border-radius:4px;margin-top:6px;background:#e2e8f0;">
                            <div id="userPwdStrengthFill" style="height:100%;border-radius:4px;width:0;transition:width 0.4s,background 0.4s;"></div>
                        </div>
                        <span id="userPwdStrengthLabel" style="font-size:0.75rem;color:var(--text-secondary);"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:20px;"><i class="fa-solid fa-floppy-disk"></i> Save User</button>
            </form>
        </div>
    </div>

<script>
function toggleUserPwd() {
    const f = document.getElementById('userPwd'), i = document.getElementById('eyeUserPwd');
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
}
document.addEventListener('DOMContentLoaded', () => {
    const p = document.getElementById('userPwd');
    if (!p) return;
    p.addEventListener('input', () => {
        const v = p.value;
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[\W_]/.test(v)) score++;
        const fill  = document.getElementById('userPwdStrengthFill');
        const label = document.getElementById('userPwdStrengthLabel');
        const colors = ['#ef4444','#f59e0b','#3b82f6','#10b981'];
        const labels = ['Weak','Fair','Good','Strong ✓'];
        fill.style.width      = (score * 25) + '%';
        fill.style.background = colors[score - 1] || '#e2e8f0';
        label.innerText       = score > 0 ? labels[score - 1] : '';
    });
});
</script>
<script src='assets/js/admin_shared.js'></script>
<script src='assets/js/admin_users.js'></script>
