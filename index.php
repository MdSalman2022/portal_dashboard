<?php
session_start();
require_once 'config.php';
$data = get_data();

$registerError = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    
    $users = db_select("SELECT * FROM users WHERE username = '$username'");
    
    if (count($users) > 0) {
        $registerError = "Email already registered. Please use a different email.";
    } else {
        
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        db_query($query);
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $users = db_select($query);
    
    if (count($users) > 0) {
        $user = $users[0];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];
        
        if ($user['role'] === 'teacher') {
            header("Location: teacher_area.php");
        } elseif ($user['role'] === 'student') {
            header("Location: student_area.php");
        } else {
            header("Location: admin_area.php");
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management Portal - Login & Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --dark: #212529;
            --light: #f8f9fa;
            --border: #e9ecef;
            --background: #f5f7fa;
            --shadow: rgba(0, 0, 0, 0.05);
            --text: #495057;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .container {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }
        
        .portal-logo {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary);
        }
        
        .logo-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .logo-tagline {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 400;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .card-header {
            display: flex;
            border-bottom: 1px solid var(--border);
        }
        
        .tab {
            flex: 1;
            padding: 1.2rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            color: #6c757d;
        }
        
        .tab:hover {
            color: var(--primary);
        }
        
        .active-tab {
            color: var(--primary);
        }
        
        .active-tab::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            background-color: var(--primary);
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            font-size: 1rem;
            border: 1px solid var(--border);
            border-radius: 5px;
            transition: all 0.3s ease;
            background-color: var(--light);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            background-color: white;
        }
        
        .form-icon {
            position: absolute;
            left: 0.8rem;
            top: 0.8rem;
            color: #6c757d;
        }
        
        .select-role {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
        }
        
        .btn {
            width: 100%;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            padding: 0.8rem;
            margin-bottom: 1.2rem;
            background-color: rgba(247, 37, 133, 0.1);
            border-left: 3px solid var(--danger);
            color: var(--danger);
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .login-form, .register-form {
            transition: all 0.3s ease;
        }
        
        .footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        @media (max-width: 500px) {
            .container {
                width: 100%;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="portal-logo">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo-text">Class Management Portal</div>
            <div class="logo-tagline">Education for everyone</div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div id="tab-register" class="tab active-tab" onclick="showTab('register')">
                    <i class="fas fa-user-plus"></i> Register
                </div>
                <div id="tab-login" class="tab" onclick="showTab('login')">
                    <i class="fas fa-sign-in-alt"></i> Login
                </div>
            </div>
            
            <div class="card-body">
                <!-- Register Form -->
                <div id="registerTab" class="register-form">
                    <h2 class="form-title">Create Account</h2>
                    
                    <?php if (!empty($registerError)) : ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $registerError; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="form-group">
                            <i class="fas fa-user form-icon"></i>
                            <input type="text" name="username" class="form-control" placeholder="Username (email)" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-users-cog form-icon"></i>
                            <select name="role" class="form-control select-role" required>
                                <option value="" disabled selected>Select Role</option>
                                <option value="teacher">Teacher</option>
                                <option value="student">Student</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="register" class="btn">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>
                </div>

                <!-- Login Form -->
                <div id="loginTab" class="login-form" style="display:none;">
                    <h2 class="form-title">Welcome Back</h2>
                    
                    <form method="post">
                        <div class="form-group">
                            <i class="fas fa-user form-icon"></i>
                            <input type="text" name="username" class="form-control" placeholder="Username (email)" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <button type="submit" name="login" class="btn">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="footer">
            &copy; <?php echo date('Y'); ?> Class Management Portal. All rights reserved.
        </div>
    </div>

    <script>
           document.addEventListener('DOMContentLoaded', function() {
            showTab('login');
        });
        function showTab(tabName) {
            
            document.getElementById('registerTab').style.display = (tabName === 'register') ? 'block' : 'none';
            document.getElementById('loginTab').style.display = (tabName === 'login') ? 'block' : 'none';
            
            
            document.getElementById('tab-register').classList.toggle('active-tab', tabName === 'register');
            document.getElementById('tab-login').classList.toggle('active-tab', tabName === 'login');
        }
    </script>
</body>
</html>