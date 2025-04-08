<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$isLoggedIn = isset($_SESSION['user_id']);
$userName = "";
$userRole = "";

if ($isLoggedIn && isset($_SESSION['role'])) {
    
    if (isset($student_name)) {
        $userName = $student_name;
    } else if (isset($teacher_name)) {
        $userName = $teacher_name;
    } else if (isset($admin_name)) {
        $userName = $admin_name;
    } else {
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'];
        
        
        if (!isset($data) || !isset($data['users'])) {
            require_once 'config.php';
            $data = get_data();
        }
        
        
        foreach ($data['users'] as $user) {
            if ($user['id'] == $userId) {
                $userName = $user['username'];
                break;
            }
        }
    }
    
    if (empty($userRole) && isset($_SESSION['role'])) {
        $userRole = $_SESSION['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https:
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --dark: #212529;
            --light: #f8f9fa;
            --border: #e9ecef;
            --shadow: rgba(0, 0, 0, 0.05);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(130deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 100;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: white;
        }
        
        .brand-icon {
            font-size: 1.75rem;
        }
        
        .user-welcome {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-left: auto;
            margin-right: 1rem;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 500;
        }
        
        .user-role {
            font-size: 0.8rem;
            opacity: 0.9;
            text-transform: capitalize;
        }
        
        .nav-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-logout {
            padding: 0.5rem 1rem;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        .btn-login {
            padding: 0.5rem 1rem;
            background-color: white;
            color: var(--primary);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn-login:hover {
            background-color: var(--light);
            transform: translateY(-2px);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .user-welcome {
                margin-right: 0.5rem;
            }
            
            .brand-text {
                display: none;
            }
            
            .user-name {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .avatar {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
            
            .user-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">
            <i class="fas fa-graduation-cap brand-icon"></i>
            <span class="brand-text">Learning Portal</span>
        </a>
        
        <?php if ($isLoggedIn): ?>
        <div class="user-welcome">
            <div class="avatar">
                <?php echo !empty($userName) ? substr(htmlspecialchars($userName), 0, 1) : 'U'; ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($userRole); ?></div>
            </div>
        </div>
        
        <div class="nav-actions">
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        <?php else: ?>
        <div class="nav-actions">
            <a href="index.php" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
        <?php endif; ?>
    </nav>
</body>
</html>