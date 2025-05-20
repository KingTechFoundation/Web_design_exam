<?php
require_once '../config/db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // Admin or Employee
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    $salary = trim($_POST['salary']);
    $date_of_joining = trim($_POST['date_of_joining']);

    if (
        empty($first_name) || empty($last_name) || empty($email) || empty($phone) ||
        empty($username) || empty($password) || empty($role) || empty($position) ||
        empty($department) || empty($salary) || empty($date_of_joining)
    ) {
        $error = "Please fill in all fields.";
    } else {
        try {
           
            $stmt = $pdo->prepare("SELECT id FROM employees WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "Username or Email already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO employees 
                    (first_name, last_name, email, phone, username, password, role, position, department, salary, date_of_joining) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $first_name, $last_name, $email, $phone, $username, $hashed_password,
                    $role, $position, $department, $salary, $date_of_joining
                ]);

                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration | HR Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #4895ef;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --text-color: #2b2d42;
            --text-light: #8d99ae;
            --bg-color: #f8f9fa;
            --white: #ffffff;
            --success: #4caf50;
            --error: #f44336;
            --border-radius: 8px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            color: var(--text-color);
        }

        .register-container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 1000px;
            overflow: hidden;
            position: relative;
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--white);
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }

        .register-header h2 {
            margin-bottom: 5px;
            font-size: 28px;
            font-weight: 600;
        }

        .register-header p {
            opacity: 0.8;
            font-size: 16px;
        }

        .progress-bar {
            display: flex;
            margin: 15px 0;
            position: relative;
            justify-content: space-between;
            counter-reset: step;
            margin-bottom: 30px;
        }

        .progress-bar::before {
            content: "";
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 4px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.3);
            z-index: 1;
        }

        .progress-step {
            width: 35px;
            height: 35px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 2;
            font-weight: 500;
            transition: var(--transition);
        }

        .progress-step.active {
            background-color: var(--white);
            color: var(--primary-color);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .register-form {
            padding: 30px;
        }

        .form-step {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        .form-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
            color: var(--primary-color);
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 14px;
        }

        .form-group i {
            position: absolute;
            left: 12px;
            top: 39px;
            color: var(--text-light);
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .form-group.success .form-control {
            border-color: var(--success);
        }

        .form-group.error .form-control {
            border-color: var(--error);
        }

        .form-message {
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .form-message.error {
            color: var(--error);
            display: block;
        }

        .form-message.success {
            color: var(--success);
            display: block;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 12px 24px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background-color: #e9ecef;
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background-color: #dee2e6;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-prev i {
            margin-right: 8px;
        }

        .btn-next i {
            margin-left: 8px;
            margin-right: 0;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
            font-size: 14px;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Password strength meter */
        .password-strength {
            height: 5px;
            margin-top: 10px;
            background-color: #eee;
            border-radius: 3px;
            position: relative;
        }

        .password-strength-meter {
            height: 100%;
            border-radius: 3px;
            transition: var(--transition);
            width: 0;
        }

        .password-strength-text {
            font-size: 12px;
            margin-top: 5px;
        }

        .weak {
            background-color: #f44336;
            width: 25%;
        }

        .medium {
            background-color: #ffa000;
            width: 50%;
        }

        .strong {
            background-color: #4caf50;
            width: 100%;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(50px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .register-container {
                margin: 20px;
            }
            
            .btn {
                padding: 10px 16px;
                font-size: 14px;
            }
        }

        .helper-text {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 4px;
        }

        .tooltip {
            position: relative;
            display: inline-block;
            margin-left: 5px;
            color: var(--text-light);
            cursor: help;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            line-height: 1.4;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Highlight effect on focus */
        .form-group:focus-within label {
            color: var(--primary-color);
            transition: var(--transition);
        }

        .form-group:focus-within i {
            color: var(--primary-color);
            transition: var(--transition);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2>Employee Registration</h2>
            <p>Create your account to join our team</p>
            
            <div class="progress-bar">
                <div class="progress-step active">1</div>
                <div class="progress-step">2</div>
                <div class="progress-step">3</div>
            </div>
        </div>
        
        <form id="registrationForm" method="POST" class="register-form">
            <!-- Step 1: Personal Information -->
            <div class="form-step active" id="step1">
                <h3 class="form-title"><i class="fas fa-user-circle"></i> Personal Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <i class="fas fa-user"></i>
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter your first name">
                        <div class="form-message"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <i class="fas fa-user"></i>
                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter your last name">
                        <div class="form-message"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address">
                    <div class="form-message"></div>
                    <div class="helper-text">We'll never share your email with anyone else.</div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <i class="fas fa-phone"></i>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number">
                    <div class="form-message"></div>
                </div>
                
                <div class="form-buttons">
                    <div></div>
                    <button type="button" class="btn btn-next" onclick="nextStep(1)">Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            
            <!-- Step 2: Account Information -->
            <div class="form-step" id="step2">
                <h3 class="form-title"><i class="fas fa-lock"></i> Account Information</h3>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <i class="fas fa-user-tag"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username">
                    <div class="form-message"></div>
                    <div class="helper-text">Username must be between 5-20 characters and contain only letters, numbers, and underscores.</div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <i class="fas fa-key"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" onkeyup="checkPasswordStrength()">
                    <div class="password-strength">
                        <div class="password-strength-meter"></div>
                    </div>
                    <div class="password-strength-text"></div>
                    <div class="form-message"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" class="form-control" placeholder="Confirm your password">
                    <div class="form-message"></div>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn btn-prev btn-secondary" onclick="prevStep(2)"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(2)">Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            
            <!-- Step 3: Employment Details -->
            <div class="form-step" id="step3">
                <h3 class="form-title"><i class="fas fa-briefcase"></i> Employment Details</h3>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <i class="fas fa-user-tie"></i>
                    <select id="role" name="role" class="form-control">
                        <option value="">Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Employee">Employee</option>
                    </select>
                    <div class="form-message"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="position">Position</label>
                        <i class="fas fa-id-badge"></i>
                        <input type="text" id="position" name="position" class="form-control" placeholder="Enter position">
                        <div class="form-message"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="department">Department</label>
                        <i class="fas fa-building"></i>
                        <input type="text" id="department" name="department" class="form-control" placeholder="Enter department">
                        <div class="form-message"></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <i class="fas fa-dollar-sign"></i>
                        <input type="number" step="0.01" id="salary" name="salary" class="form-control" placeholder="Enter salary">
                        <div class="form-message"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_of_joining">Date of Joining</label>
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="date_of_joining" name="date_of_joining" class="form-control">
                        <div class="form-message"></div>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn btn-prev btn-secondary" onclick="prevStep(3)"><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Create Account</button>
                </div>
            </div>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </form>
    </div>

    <script>
        // Form navigation
        function nextStep(currentStep) {
            // Validate the current step
            if (!validateStep(currentStep)) {
                return false;
            }
            
            // Hide current step
            document.getElementById('step' + currentStep).classList.remove('active');
            
            // Show next step
            document.getElementById('step' + (currentStep + 1)).classList.add('active');
            
            // Update progress bar
            document.querySelectorAll('.progress-step')[currentStep].classList.add('active');
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            return true;
        }
        
        function prevStep(currentStep) {
            // Hide current step
            document.getElementById('step' + currentStep).classList.remove('active');
            
            // Show previous step
            document.getElementById('step' + (currentStep - 1)).classList.add('active');
            
            // Update progress bar
            document.querySelectorAll('.progress-step')[currentStep - 1].classList.remove('active');
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            return true;
        }
        
        // Form validation
        function validateStep(step) {
            let isValid = true;
            
            if (step === 1) {
                // Validate personal information
                isValid = validateField('first_name', 'First name is required') && isValid;
                isValid = validateField('last_name', 'Last name is required') && isValid;
                isValid = validateEmail() && isValid;
                isValid = validatePhone() && isValid;
            } else if (step === 2) {
                // Validate account information
                isValid = validateUsername() && isValid;
                isValid = validatePassword() && isValid;
                isValid = validateConfirmPassword() && isValid;
            }
            
            return isValid;
        }
        
        function validateField(fieldId, errorMessage) {
            const field = document.getElementById(fieldId);
            const formGroup = field.parentElement;
            const errorElement = formGroup.querySelector('.form-message');
            
            if (field.value.trim() === '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = errorMessage;
                errorElement.className = 'form-message error';
                return false;
            } else {
                formGroup.classList.remove('error');
                formGroup.classList.add('success');
                errorElement.textContent = '';
                errorElement.className = 'form-message';
                return true;
            }
        }
        
        function validateEmail() {
            const email = document.getElementById('email');
            const formGroup = email.parentElement;
            const errorElement = formGroup.querySelector('.form-message');
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.value.trim() === '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Email is required';
                errorElement.className = 'form-message error';
                return false;
            } else if (!regex.test(email.value)) {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Please enter a valid email address';
                errorElement.className = 'form-message error';
                return false;
            } else {
                formGroup.classList.remove('error');
                formGroup.classList.add('success');
                errorElement.textContent = '';
                errorElement.className = 'form-message';
                return true;
            }
        }
        
        function validatePhone() {
            const phone = document.getElementById('phone');
            const formGroup = phone.parentElement;
            const errorElement = formGroup.querySelector('.form-message');
            const regex = /^[0-9+\- ]{10,15}$/;
            
            if (phone.value.trim() === '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Phone number is required';
                errorElement.className = 'form-message error';
                return false;
            } else if (!regex.test(phone.value)) {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Please enter a valid phone number';
                errorElement.className = 'form-message error';
                return false;
            } else {
                formGroup.classList.remove('error');
                formGroup.classList.add('success');
                errorElement.textContent = '';
                errorElement.className = 'form-message';
                return true;
            }
        }
        
        function validateUsername() {
            const username = document.getElementById('username');
            const formGroup = username.parentElement;
            const errorElement = formGroup.querySelector('.form-message');
            const regex = /^[a-zA-Z0-9_]{5,20}$/;
            
            if (username.value.trim() === '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Username is required';
                errorElement.className = 'form-message error';
                return false;
            } else if (!regex.test(username.value)) {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Username must be 5-20 characters and contain only letters, numbers, and underscores';
                errorElement.className = 'form-message error';
                return false;
            } else {
                formGroup.classList.remove('error');
                formGroup.classList.add('success');
                errorElement.textContent = '';
                errorElement.className = 'form-message';
                return true;
            }
        }
        
        function validatePassword() {
            const password = document.getElementById('password');
            const formGroup = password.parentElement;
            const errorElement = formGroup.querySelector('.form-message');
            
            if (password.value.trim() === '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Password is required';
                errorElement.className = 'form-message error';
                return false;
            } else if (password.value.length < 8) {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Password must be at least 8 characters';
                errorElement.className = 'form-message error';
                return false;
            } else {
                formGroup.classList.remove('error');
                formGroup.classList.add('success');
                errorElement.textContent = '';
                errorElement.className = 'form-message';
                return true;
            }
        }
        
        function validateConfirmPassword() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const formGroup = confirmPassword.parentElement;
            const errorElement = formGroup.querySelector('.form-message');
            
            if (confirmPassword.value.trim() === '') {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Please confirm your password';
                errorElement.className = 'form-message error';
                return false;
            } else if (confirmPassword.value !== password.value) {
                formGroup.classList.add('error');
                formGroup.classList.remove('success');
                errorElement.textContent = 'Passwords do not match';
                errorElement.className = 'form-message error';
                return false;
            } else {
                formGroup.classList.remove('error');
                formGroup.classList.add('success');
                errorElement.textContent = '';
                errorElement.className = 'form-message';
                return true;
            }
        }
        
        // Password strength meter
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthMeter = document.querySelector('.password-strength-meter');
            const strengthText = document.querySelector('.password-strength-text');
            
            // Reset class
            strengthMeter.className = 'password-strength-meter';
            
            // Calculate strength
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength += 1;
            
            // Update meter
            if (password.length === 0) {
                strengthMeter.style.width = '0';
                strengthText.textContent = '';
            } else if (strength <= 2) {
                strengthMeter.classList.add('weak');
                strengthText.textContent = 'Weak - Please use a stronger password';
                strengthText.style.color = '#f44336';
            } else if (strength <= 4) {
                strengthMeter.classList.add('medium');
                strengthText.textContent = 'Medium - Add numbers and special characters';
                strengthText.style.color = '#ffa000';
            } else {
                strengthMeter.classList.add('strong');
                strengthText.textContent = 'Strong - Great password!';
                strengthText.style.color = '#4caf50';
            }
        }
         // Form submission validation
         document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Validate all fields before submission
            const isStep1Valid = validateField('first_name', 'First name is required') &&
                                validateField('last_name', 'Last name is required') &&
                                validateEmail() &&
                                validatePhone();
            
            const isStep2Valid = validateUsername() &&
                                validatePassword() &&
                                validateConfirmPassword();
            
            const isStep3Valid = validateField('role', 'Role is required') &&
                                validateField('position', 'Position is required') &&
                                validateField('department', 'Department is required') &&
                                validateField('salary', 'Salary is required') &&
                                validateField('date_of_joining', 'Date of joining is required');
            
            if (!(isStep1Valid && isStep2Valid && isStep3Valid)) {
                e.preventDefault();
                
                // Show the first step with errors
                document.getElementById('step1').classList.add('active');
                document.getElementById('step2').classList.remove('active');
                document.getElementById('step3').classList.remove('active');
                
                // Reset progress bar
                document.querySelectorAll('.progress-step').forEach((step, index) => {
                    if (index === 0) {
                        step.classList.add('active');
                    } else {
                        step.classList.remove('active');
                    }
                });
                
                // Show error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: 'Please fill in all required fields correctly.'
                });
            }
        });
        
        // Input validation on blur
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function() {
                switch(this.id) {
                    case 'first_name':
                    case 'last_name':
                    case 'position':
                    case 'department':
                    case 'salary':
                    case 'date_of_joining':
                    case 'role':
                        validateField(this.id, this.id.replace('_', ' ') + ' is required');
                        break;
                    case 'email':
                        validateEmail();
                        break;
                    case 'phone':
                        validatePhone();
                        break;
                    case 'username':
                        validateUsername();
                        break;
                    case 'password':
                        validatePassword();
                        break;
                    case 'confirm_password':
                        validateConfirmPassword();
                        break;
                }
            });
        });
        
        // Add focus effect
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>

    <?php if ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: '<?= htmlspecialchars($error) ?>',
            confirmButtonColor: '#4361ee'
        });
    </script>
    <?php elseif ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful',
            text: '<?= htmlspecialchars($success) ?>',
            confirmButtonColor: '#4361ee'
        });
    </script>
    <?php endif; ?>
</body>
</html>