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
