const sessionUser = JSON.parse(localStorage.getItem('sessionUser'));
if(!sessionUser || (sessionUser.role_name !== 'Employee' && sessionUser.role_id != 3)) window.location.href = 'index.php?page=login';
function logout() { localStorage.removeItem('sessionUser'); window.location.href = 'index.php?page=login'; }

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('accName').innerText = `${sessionUser.first_name} ${sessionUser.last_name}`;
    document.getElementById('accCode').innerText = sessionUser.emp_code;
    document.getElementById('accDesig').innerText = sessionUser.designation;

    document.getElementById('pwdForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const p1 = document.getElementById('newPwd').value;
        const p2 = document.getElementById('confirmPwd').value;
        const err = document.getElementById('pwdError');
        
        if(p1 !== p2) { err.innerText = "Passwords do not match!"; return; }
        err.innerText = "Updating...";

        try {
            const res = await fetch('api.php?action=update_password', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ type: 'Employee', email: sessionUser.emp_code, new_password: p1 })
            });
            const data = await res.json();
            if(data.status === 'success') {
                err.style.color = "var(--success)";
                err.innerText = "✅ " + (data.message || "Password successfully updated!");
                document.getElementById('pwdForm').reset();
                setTimeout(() => err.innerText='', 3000);
            } else { 
                err.innerText = data.message || "Error updating password."; 
            }
        } catch(err2) { err.innerText = "Server connection error."; }
    });
});
