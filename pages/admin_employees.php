<?php include 'includes/sidebar_admin.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Employees</h1>
        </div>

        <div class="action-bar">
            <button class="btn btn-primary" onclick="openModal()"><i class="fa-solid fa-plus"></i> Add Employee</button>
            <button class="btn btn-success" onclick="exportCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
            <button class="btn btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print List</button>
        </div>

        <div class="table-container">
            <table class="data-table" id="empTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Package (LPA)</th>
                        <th>Status</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>

    <!-- Add / Edit Employee Modal -->
    <div class="modal-overlay" id="empModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Employee</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="empForm" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Employee Code <span style="color:var(--danger)">*</span></label>
                        <input type="text" class="form-control" id="ec" placeholder="e.g. EMP010" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address <span style="color:var(--danger)">*</span></label>
                        <input type="email" class="form-control" id="em" placeholder="name@company.com" required>
                    </div>
                    <div class="form-group">
                        <label>First Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" class="form-control" id="fn" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" class="form-control" id="ln" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <label>Designation</label>
                        <select class="form-control" id="desig">
                            <option value="HR Manager">HR Manager</option>
                            <option value="Sales Officer">Sales Officer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Package (LPA) <span style="color:var(--danger)">*</span></label>
                        <input type="number" step="0.1" min="0.1" class="form-control" id="pkg" placeholder="e.g. 5.5" required>
                    </div>
                    <div class="form-group">
                        <label>Gross Monthly (₹) <span style="color:var(--danger)">*</span></label>
                        <input type="number" min="1" class="form-control" id="gross" placeholder="e.g. 45000" required>
                    </div>
                    <div class="form-group">
                        <label>Paid Leaves Taken</label>
                        <input type="number" min="0" step="0.5" class="form-control" id="lvs" value="0">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="isActive">
                            <option value="Y">🟢 Active</option>
                            <option value="N">🔴 Inactive</option>
                        </select>
                    </div>
                    <!-- Password row: shown only for new employees -->
                    <div class="form-group" id="pwdRow" style="grid-column: 1 / -1;">
                        <label>Initial Password <span style="color:var(--danger)">*</span><small style="font-weight:400;color:var(--text-secondary);margin-left:8px;">(min 8 chars, uppercase, number, special char)</small></label>
                        <div style="position:relative;">
                            <input type="password" class="form-control" id="empPwd" placeholder="Min 8 chars: Abc@1234" style="padding-right:45px;">
                            <button type="button" onclick="togglePwdField('empPwd','eyeEmp')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:1rem;"><i class="fa-solid fa-eye" id="eyeEmp"></i></button>
                        </div>
                        <span id="empPwdErr" style="color:var(--danger);font-size:0.78rem;display:block;margin-top:3px;"></span>
                        <div id="pwdStrengthBar" style="height:4px;border-radius:4px;margin-top:6px;background:#e2e8f0;transition:all 0.3s;">
                            <div id="pwdStrengthFill" style="height:100%;border-radius:4px;width:0;transition:width 0.4s,background 0.4s;"></div>
                        </div>
                        <span id="pwdStrengthLabel" style="font-size:0.75rem;color:var(--text-secondary);"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:20px;"><i class="fa-solid fa-floppy-disk"></i> Save Employee</button>
            </form>
        </div>
    </div>

<script src="assets/js/admin_shared.js"></script>
<script>
    function togglePwdField(fId, iId) {
        const f = document.getElementById(fId), i = document.getElementById(iId);
        f.type = f.type === 'password' ? 'text' : 'password';
        i.className = f.type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
    }

    // Password strength meter
    document.addEventListener('DOMContentLoaded', () => {
        const pwdInput = document.getElementById('empPwd');
        if (pwdInput) {
            pwdInput.addEventListener('input', () => {
                const v = pwdInput.value;
                let score = 0;
                if (v.length >= 8) score++;
                if (/[A-Z]/.test(v)) score++;
                if (/[0-9]/.test(v)) score++;
                if (/[\W_]/.test(v)) score++;
                const fill  = document.getElementById('pwdStrengthFill');
                const label = document.getElementById('pwdStrengthLabel');
                const colors = ['#ef4444','#f59e0b','#3b82f6','#10b981'];
                const labels = ['Weak','Fair','Good','Strong'];
                fill.style.width = (score * 25) + '%';
                fill.style.background = colors[score - 1] || '#e2e8f0';
                label.innerText = score > 0 ? labels[score - 1] : '';
            });
        }
    });
</script>
<script src="assets/js/admin_employees.js"></script>
