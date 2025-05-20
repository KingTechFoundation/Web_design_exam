<?php
session_start();
require_once '../config/db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['employee_id'] = $user['id'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'Admin') {
                header('Location: ../dashboard/admin_dashboard.php');
            } else {
                header('Location: ../dashboard/user_dashboard.php');
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }

        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--dark-color);
            color: white;
            padding: 1rem 5%;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 0.5rem 5%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a.btn {
            background-color: var(--primary-color);
            color: white;
        }

        .nav-links a.btn:hover {
            background-color: var(--secondary-color);
        }

        .mobile-menu {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            width: 100%;
            background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.7)), url('../images/office.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            color: white;
            max-width: 700px;
            margin-left: 10%;
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards 0.5s;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards 0.8s;
        }

        .hero-btns {
            display: flex;
            gap: 1rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards 1.1s;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-outline {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background-color: white;
            color: var(--dark-color);
        }

        /* Sliding Overlay */
        .hero-overlay {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background-color: rgba(52, 152, 219, 0.4);
            z-index: 1;
            animation: slideInOverlay 1.5s forwards 0.2s;
        }

        /* Features Section */
        .features {
            padding: 5rem 10%;
            background-color: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .section-title p {
            color: #777;
            max-width: 700px;
            margin: 0 auto;
        }

        .features-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .feature-card p {
            color: #777;
            line-height: 1.6;
        }

        /* Login Modal */
        .login-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            position: relative;
            animation: fadeIn 0.5s;
        }

        .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            color: var(--danger-color);
        }

        .login-container h2 {
            text-align: center;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .login-container button {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-container button:hover {
            background-color: var(--secondary-color);
        }

        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 10%;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .footer-column p {
            margin-bottom: 1rem;
            line-height: 1.6;
            color: #ccc;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: var(--primary-color);
            transform: translateY(-5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #ccc;
        }

        /* Animations */
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInOverlay {
            to {
                left: 0;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .hero-content {
                margin: 0 1rem;
                text-align: center;
            }
            
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-btns {
                justify-content: center;
            }
            
            .nav-links {
                display: none;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background-color: var(--dark-color);
                padding: 1rem;
            }
            
            .nav-links.active li {
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="#" class="logo"><i class="fas fa-users-cog"></i> EMS Pro</a>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#" class="btn" id="login-btn">Login</a></li>
            <li><a href="register.php" class="btn">Register</a></li>
        </ul>
        <div class="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Employee Management Simplified</h1>
            <p>Streamline your HR operations with our comprehensive employee management system. Track performance, manage attendance, and boost productivity.</p>
            <div class="hero-btns">
                <a href="#" class="btn btn-primary" id="hero-login-btn">Get Started</a>
                <a href="#features" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-title">
            <h2>Powerful Features</h2>
            <p>Our employee management system comes packed with tools designed to streamline your HR processes</p>
        </div>
        <div class="features-container">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3>Attendance Tracking</h3>
                <p>Monitor employee attendance with an intuitive interface. Generate reports and identify patterns to improve productivity.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Performance Analytics</h3>
                <p>Track employee performance with customizable metrics. Set goals and monitor progress with visual dashboards.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Leave Management</h3>
                <p>Streamline leave requests and approvals. Keep track of employee time off and plan resources accordingly.</p>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section class="features" id="about" style="background-color: #f8f9fa;">
        <div class="section-title">
            <h2>About EMS Pro</h2>
            <p>We're dedicated to making employee management simple and efficient</p>
        </div>
        <div class="features-container">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3>Our Mission</h3>
                <p>To provide businesses with intuitive tools that simplify HR tasks and improve overall workplace efficiency.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Our Journey</h3>
                <p>Founded in 2022, we've been helping businesses optimize their HR operations with innovative solutions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Our Team</h3>
                <p>We're a team of HR specialists and developers dedicated to creating the best employee management system.</p>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="features" id="contact">
        <div class="section-title">
            <h2>Get In Touch</h2>
            <p>Have questions about our system? We're here to help!</p>
        </div>
        <div class="features-container">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Visit Us</h3>
                <p>123 Business Park, Suite 456<br>Tech City, TC 12345</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Email Us</h3>
                <p>muhirehillary720@gmail.com<br>muhirehillary720@gmail.com</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h3>Call Us</h3>
                <p>+250<br>0792397681</p>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="login-modal" id="login-modal">
        <div class="login-container">
            <span class="close-btn" id="close-login"><i class="fas fa-times"></i></span>
            <h2>Login to Your Account</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register here</a></p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <h3>About EMS Pro</h3>
                <p>We provide comprehensive employee management solutions to help businesses streamline their HR operations and boost productivity.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Support</h3>
                <ul class="footer-links">
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for updates and tips on employee management.</p>
                <form style="display: flex; margin-top: 1rem;">
                    <input type="email" placeholder="Enter your email" style="padding: 0.8rem; border: none; border-radius: 4px 0 0 4px; width: 70%;">
                    <button style="background-color: var(--primary-color); color: white; border: none; border-radius: 0 4px 4px 0; padding: 0 1rem; cursor: pointer;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EMS Pro. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        const mobileMenu = document.querySelector('.mobile-menu');
        const navLinks = document.querySelector('.nav-links');
        
        mobileMenu.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });

        // Login modal functionality
        const loginModal = document.getElementById('login-modal');
        const loginBtn = document.getElementById('login-btn');
        const heroLoginBtn = document.getElementById('hero-login-btn');
        const closeLogin = document.getElementById('close-login');

        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.style.display = 'flex';
        });

        heroLoginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.style.display = 'flex';
        });

        closeLogin.addEventListener('click', function() {
            loginModal.style.display = 'none';
        });

        window.addEventListener('click', function(e) {
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                if (this.getAttribute('href') !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    </script>

    <?php if ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: '<?= htmlspecialchars($error) ?>'
        });
    </script>
    <?php endif; ?>
</body>
</html>