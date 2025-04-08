<?php
session_start();
require_once 'config.php';
require_once 'header.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$data = get_data();
$student_id = $_SESSION['user_id'];


$student_name = "Student";
foreach ($data['users'] as $user) {
    if ($user['id'] == $student_id) {
        $student_name = $user['username'];
        break;
    }
}

if (isset($_POST['enroll_course'])) {
    $course_id = $_POST['course_id'];
    
    
    $courses = db_select("SELECT * FROM courses WHERE id = $course_id");
    
    if (count($courses) > 0) {
        $course = $courses[0];
        $enrolled_students = empty($course['enrolled_students']) ? [] : 
                             explode(',', $course['enrolled_students']);
        
        
        if (!in_array($student_id, $enrolled_students)) {
            $enrolled_students[] = $student_id;
            $new_enrolled = implode(',', $enrolled_students);
            
            $query = "UPDATE courses SET enrolled_students = '$new_enrolled' WHERE id = $course_id";
            db_query($query);
        }
    }
    
    
    $data = get_data();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #a5a6f6;
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
            background: linear-gradient(135deg, var(--primary), var(--info));
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .user-welcome {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
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
        }
        
        .nav-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-logout {
            padding: 0.5rem 1rem;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        
        .course-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .course-header {
            background: linear-gradient(to right, var(--primary), var(--info));
            color: white;
            padding: 1.5rem;
            position: relative;
        }
        
        .course-id {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }
        
        .course-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .teacher-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .teacher-name {
            opacity: 0.9;
        }
        
        .course-content {
            padding: 1.5rem;
        }
        
        .course-description {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: #666;
        }
        
        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .meta-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .meta-label {
            font-size: 0.8rem;
            color: #888;
        }
        
        .course-action {
            display: flex;
            justify-content: center;
        }
        
        .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
        }
        
        .enrolled-badge {
            background-color: var(--success);
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .course-status {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .empty-courses {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .empty-text {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .nav-actions {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-graduation-cap"></i> Available Courses
        </h1>
        
        <?php if (empty($data['courses'])): ?>
            <div class="empty-courses">
                <div class="empty-icon">
                    <i class="fas fa-book"></i>
                </div>
                <p class="empty-text">No courses are available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="courses-grid">
                <?php foreach ($data['courses'] as $course): 
                    $enrolled = in_array($student_id, $course['enrolled_students']);
                    
                    
                    $teacher_name = "";
                    foreach ($data['users'] as $user) {
                        if ($user['id'] == $course['teacher_id']) {
                            $teacher_name = $user['username'];
                            break;
                        }
                    }
                    
                    
                    $student_count = !empty($course['enrolled_students']) ? count($course['enrolled_students']) : 0;
                ?>
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-id">Course ID: <?= $course['id']; ?></div>
                        <div class="course-name"><?= htmlentities($course['course_name']); ?></div>
                        <div class="teacher-info">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span class="teacher-name">Instructor: <?= htmlentities($teacher_name); ?></span>
                        </div>
                        
                        <?php if ($enrolled): ?>
                        <div class="course-status">
                            <i class="fas fa-check-circle"></i> Enrolled
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-content">
                        <div class="course-meta">
                            <div class="meta-item">
                                <div class="meta-value"><?= $student_count; ?></div>
                                <div class="meta-label">Students</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-value"><i class="fas fa-book"></i></div>
                                <div class="meta-label">Resources</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-value"><i class="fas fa-tasks"></i></div>
                                <div class="meta-label">Activities</div>
                            </div>
                        </div>
                        
                        <div class="course-action">
                            <?php if (!$enrolled): ?>
                            <form method="post">
                                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                <button type="submit" name="enroll_course" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Enroll Now
                                </button>
                            </form>
                            <?php else: ?>
                            <div class="enrolled-badge">
                                <i class="fas fa-check-circle"></i> Enrolled in Course
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>