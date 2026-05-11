<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Aadink Pharma - HR Portal Login'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css?v=1.3">
    <link rel="shortcut icon" href="assets/images/favicon.jpeg?v=1.3" type="image/jpeg">
</head>
<body>
    <div class="login-wrapper">
        <div class="brand-panel">
            <div class="brand-logo-area">
                <img src="assets/images/payzen_logo.jpg" alt="Payzen" class="brand-logo-img">
            </div>
            <div class="iso-badge">
                <img src="assets/images/iso_logo.png" alt="ISO 9001:2015" class="iso-logo-img">
            </div>
            <p class="brand-tagline">Welcome to the Enterprise Human Resource Management Portal</p>
            <div class="brand-address">
                <i class="fa-solid fa-location-dot" style="color:#4f46e5; margin-right:8px;"></i>
                Ground Floor abc Tower,<br>
                Sector 105, xyz (West),<br>
                Uttar Pradesh, INDIA
            </div>
        </div>

        <div class="form-panel">
            <h1 class="login-card-title">Welcome Back</h1>
            <p class="login-card-sub">Choose your portal and sign in to continue</p>

            <div class="role-tabs">
                <button class="role-tab active" id="btnAdmin" onclick="switchRole('admin')">
                    <i class="fa-solid fa-user-shield"></i> Admin / HR
                </button>
                <button class="role-tab" id="btnEmployee" onclick="switchRole('employee')">
                    <i class="fa-solid fa-user"></i> Employee
                </button>
            </div>

            <div id="adminSection" class="login-section active">
                <form id="adminForm">
                    <div class="form-group">
                        <label>Login ID (Email)</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="text" class="form-input" id="adminLoginId" placeholder="admin@company.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" class="form-input" id="adminPassword" placeholder="Password" required>
                            <button type="button" class="eye-btn" onclick="togglePwd('adminPassword','eyeAdminPwd')"><i class="fa-solid fa-eye" id="eyeAdminPwd"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In as Admin</button>
                    <div class="error-msg" id="adminError"></div>
                </form>
            </div>

            <div id="employeeSection" class="login-section">
                <form id="employeeForm">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="text" class="form-input" id="empLoginId" placeholder="emp@company.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" class="form-input" id="empPassword" placeholder="Password" required>
                            <button type="button" class="eye-btn" onclick="togglePwd('empPassword','eyeEmpPwd')"><i class="fa-solid fa-eye" id="eyeEmpPwd"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In as Employee</button>
                    <div class="error-msg" id="empError"></div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/login.js"></script>
</body>
</html>
