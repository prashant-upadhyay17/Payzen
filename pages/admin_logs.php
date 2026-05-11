<?php include 'includes/sidebar_admin.php'; ?>
<style>
    @media (max-width: 992px) {
        .page-header .action-bar { flex-direction: row; flex-wrap: wrap; padding: 0; background: none; box-shadow: none; }
        #logsTable th:first-child { width: 140px !important; }
    }
</style>
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">System Activity Logs</h1>
            <div class="action-bar" style="margin-bottom:0; background:none; box-shadow:none; padding:0;">
                <button class="btn btn-outline" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
                <button class="btn btn-outline" onclick="exportLogsCSV()"><i class="fa-solid fa-file-excel"></i> Export Excel (CSV)</button>
            </div>
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
<script src="assets/js/admin_shared.js"></script>
    <script>
        let allLogs = [];
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch('api.php?action=get_logs');
                const payload = await res.json();
                if(payload.status === 'success') {
                    allLogs = payload.data || [];
                    const tbody = document.querySelector('#logsTable tbody');
                    allLogs.forEach(log => {
                        tbody.innerHTML += `<tr><td>${log.time}</td><td><strong>${log.message}</strong></td></tr>`;
                    });
                }
            } catch(e) { console.error(e); }
        });

        function exportLogsCSV() {
            if(!allLogs.length) return;
            let csv = "Timestamp,Action Logged\n";
            allLogs.forEach(log => {
                csv += `"${log.time}","${log.message.replace(/"/g, '""')}"\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'activity_logs.csv'; a.click();
        }
    </script>
<script src='assets/js/admin_shared.js'></script><script src='assets/js/admin_logs.js'></script>

