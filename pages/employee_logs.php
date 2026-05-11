<?php include 'includes/sidebar_employee.php'; ?>
<style>
    @media (max-width: 992px) {
        .page-header { align-items: flex-start; }
        .page-header .btn { align-self: flex-start; }
        #logsTable th:first-child { width: 140px !important; }
    }
</style>
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">My Recent Activity</h1>
            <button class="btn btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Logs</button>
        </div>

        <div class="table-container">
            <table class="data-table" id="logsTable">
                <thead>
                    <tr>
                        <th style="width: 200px;">Timestamp</th>
                        <th>Action Logged</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
<script>
        const sessionUser = JSON.parse(localStorage.getItem('sessionUser'));
        if(!sessionUser || (sessionUser.role_name !== 'Employee' && sessionUser.role_id != 3)) window.location.href = '../index.html';
        function logout() { localStorage.removeItem('sessionUser'); window.location.href = '../index.html'; }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch('api.php?action=get_logs');
                const payload = await res.json();
                if(payload.status === 'success') {
                    const tbody = document.querySelector('#logsTable tbody');
                    // Filter logs for this employee code
                    payload.data.filter(log => log.message.includes(sessionUser.emp_code)).forEach(log => {
                        tbody.innerHTML += `<tr><td>${log.time}</td><td><strong>${log.message}</strong></td></tr>`;
                    });
                }
            } catch(e) { console.error(e); }
        });
    </script>
<script src='assets/js/employee_logs.js'></script>

