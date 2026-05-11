        document.addEventListener('DOMContentLoaded', async () => {
            const emps = await fetchEmployees();
            document.getElementById('totalEmpCount').innerText = emps.length;
            
            let slipCount = 0;
            let salDisbursed = 0;
            let deptCounts = {};
            let empNames = [];
            let empSalaries = [];

            emps.forEach(e => {
                slipCount += (e.payslips_json ? e.payslips_json.length : 0);
                if(e.payslips_json) {
                    e.payslips_json.forEach(s => salDisbursed += parseFloat(s.net_salary.replace(/,/g, '')||0));
                }
                
                // For Dept Chart
                deptCounts[e.designation] = (deptCounts[e.designation] || 0) + 1;
                
                // For Salary Chart (Top 10)
                if(empNames.length < 10) {
                    empNames.push(e.first_name);
                    empSalaries.push(e.gross_monthly);
                }
            });
            document.getElementById('totalSlips').innerText = slipCount;
            document.getElementById('totalSalary').innerText = '₹' + salDisbursed.toLocaleString('en-IN', {minimumFractionDigits: 2});

            // Initialize Charts
            new Chart(document.getElementById('salaryChart'), {
                type: 'bar',
                data: {
                    labels: empNames,
                    datasets: [{
                        label: 'Gross Monthly Salary (₹)',
                        data: empSalaries,
                        backgroundColor: 'rgba(79, 70, 229, 0.7)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            new Chart(document.getElementById('deptChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(deptCounts),
                    datasets: [{
                        data: Object.values(deptCounts),
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, cutout: '70%' }
            });
        });

