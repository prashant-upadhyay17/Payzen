const sessionUser = JSON.parse(localStorage.getItem('sessionUser'));
if(!sessionUser || (sessionUser.role_name !== 'Employee' && sessionUser.role_id != 3)) window.location.href = 'index.php?page=login';
function logout() { localStorage.removeItem('sessionUser'); window.location.href = 'index.php?page=login'; }

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('api.php?action=get_logs');
        const payload = await res.json();
        if(payload.status === 'success') {
            const tbody = document.querySelector('#logsTable tbody');
            payload.data.filter(log => log.message.includes(sessionUser.emp_code)).forEach(log => {
                tbody.innerHTML += `<tr><td>${log.time}</td><td><strong>${log.message}</strong></td></tr>`;
            });
        }
    } catch(e) { console.error(e); }
});
