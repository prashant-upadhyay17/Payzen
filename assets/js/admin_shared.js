// admin_shared.js
const sessionUser = JSON.parse(localStorage.getItem('sessionUser'));
if(!sessionUser || (sessionUser.role_name !== 'Admin' && sessionUser.role_name !== 'HR Manager' && sessionUser.role_id > 2)) {
    window.location.href = 'index.php?page=login';
}

if(document.getElementById('adminName')) {
    document.getElementById('adminName').innerText = `${sessionUser.first_name} ${sessionUser.last_name}`;
}

// Role-based Sidebar Visibility
document.addEventListener('DOMContentLoaded', () => {
    // Hide System Users for HR Manager (Role ID 2)
    if (sessionUser && (sessionUser.role_id == 2 || sessionUser.role_name === 'HR Manager')) {
        const usersLink = document.querySelector('a[href*="page=admin_users"]');
        if (usersLink && usersLink.parentElement) {
            usersLink.parentElement.style.display = 'none';
        }
        // Redirect HR Managers away from the System Users page if they try to access it directly
        if (window.location.search.includes('page=admin_users')) {
            window.location.href = 'index.php?page=admin_dashboard';
        }
    }
});

function logout() {
    localStorage.removeItem('sessionUser');
    window.location.href = 'index.php?page=login';
}

async function fetchEmployees() {
    try {
        const res = await fetch('api.php?action=get_employees');
        const payload = await res.json();
        if(payload.status === 'success') return payload.data || [];
        return [];
    } catch(e) {
        console.error(e); return [];
    }
}
