<?php
session_start();
require_once 'config.php';
require_once 'header.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
 
if (isset($_POST['assign_course'])) {
    $course_name = $_POST['course_name'];
    $query = "INSERT INTO courses (course_name, teacher_id) VALUES ('$course_name', $teacher_id)";
    db_query($query);
}

if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $query = "UPDATE courses SET course_name = '$course_name' 
              WHERE id = $course_id AND teacher_id = $teacher_id";
    db_query($query);
}

if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $query = "DELETE FROM courses WHERE id = $course_id AND teacher_id = $teacher_id";
    db_query($query);
}

if (isset($_POST['enroll_in_course'])) {
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];
    
    $courses = db_select("SELECT * FROM courses WHERE id = $course_id AND teacher_id = $teacher_id");
    
    if (count($courses) > 0) {
        $course = $courses[0];
        $enrolled_students = empty($course['enrolled_students']) ? [] : 
                             explode(',', $course['enrolled_students']);
        
        if (!in_array($student_id, $enrolled_students)) {
            $enrolled_students[] = $student_id;
            $new_enrolled = implode(',', $enrolled_students);
            
            $query = "UPDATE courses SET enrolled_students = '$new_enrolled' 
                     WHERE id = $course_id AND teacher_id = $teacher_id";
            db_query($query);
        }
    }
}

$data = get_data();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
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
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px var(--shadow);
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
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--light);
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .btn {
            padding: 0.8rem 1.2rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #d30f70;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        
        .course-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .course-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px var(--shadow);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .course-header {
            padding: 1.2rem;
            background: linear-gradient(to right, var(--primary-light), var(--success));
            color: white;
        }
        
        .course-id {
            opacity: 0.7;
            font-size: 0.875rem;
            margin-bottom: 0.3rem;
        }
        
        .course-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .course-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.8rem;
        }
        
        .course-body {
            padding: 1.2rem;
        }
        
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .student-list {
            list-style: none;
            margin-bottom: 1.5rem;
        }
        
        .student-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .student-item:last-child {
            border-bottom: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .edit-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            background-color: #e9ecef;
        }
        
        .badge-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .badge-success {
            background-color: var(--success);
            color: white;
        }
        
        .flex-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .course-list {
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
        <h1 class="page-title">Teacher Dashboard</h1>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Create New Course</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="form-group">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" id="course_name" name="course_name" class="form-control" placeholder="Enter course name" required />
                    </div>
                    <button type="submit" name="assign_course" class="btn btn-primary">  Create Course
                    </button>
                </form>
            </div>
        </div>

        <h2 class="page-title">Your Courses</h2>
        
        <?php if (empty(array_filter($data['courses'], function($c) use ($teacher_id) { return $c['teacher_id'] == $teacher_id; }))): ?>
            <div class="empty-state">
                
                <p>You don't have any courses yet. Create your first course above!</p>
            </div>
        <?php else: ?>
            <div class="course-list">
                <?php
                foreach ($data['courses'] as $course) {
                    if ($course['teacher_id'] == $teacher_id) {
                        $student_count = !empty($course['enrolled_students']) ? count($course['enrolled_students']) : 0;
                ?>
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-id">Course ID: <?= $course['id']; ?></div>
                        <div class="course-name"><?= htmlentities($course['course_name']); ?></div>
                        <div class="flex-row">
                            <span class="badge badge-primary">
                              <?= $student_count; ?> Students
                            </span>
                        </div>
                        <div class="course-actions">
                            <button type="button" class="btn btn-sm btn-primary" onclick="toggleEditForm(<?= $course['id']; ?>)">
                             Edit
                            </button>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                <button type="submit" name="delete_course" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">
                                   Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="course-body">
                        <!-- Edit form (initially hidden) -->
                        <div id="edit-form-<?= $course['id']; ?>" style="display:none; margin-bottom:1rem; padding:1rem; background-color:#f8f9fa; border-radius:6px;">
                            <form method="post" class="edit-form">
                                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                <input type="text" name="course_name" class="form-control" placeholder="Updated name" value="<?= htmlentities($course['course_name']); ?>" required>
                                <button type="submit" name="update_course" class="btn btn-primary">
                                    Save
                                </button>
                            </form>
                        </div>

                        <div class="section-title">
                           Enrolled Students
                        </div>
                        
                        <?php if (empty($course['enrolled_students'])): ?>
                            <p style="color:#6c757d; margin-bottom:1rem;"> No students enrolled yet.</p>
                        <?php else: ?>
                            <ul class="student-list">
                                <?php 
                                foreach ($course['enrolled_students'] as $student_id) {
                                    foreach ($data['users'] as $u) {
                                        if ($u['id'] == $student_id) {
                                            echo '<li class="student-item"> ' . htmlentities($u['username']) . '</li>';
                                            break;
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        <?php endif; ?>

                        <!-- Enrollment form -->
                        <?php
                        $available_students = array_filter($data['users'], function($u) use ($course) {
                            return $u['role'] === 'student' && !in_array($u['id'], $course['enrolled_students']);
                        });
                        
                        if (!empty($available_students)):
                        ?>
                        <div class="section-title"> Add New Student</div>
                        <form method="post">
                            <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                            <div class="form-group">
                                <select name="student_id" class="form-control" required>
                                    <option value="">-- Select Student --</option>
                                    <?php 
                                    foreach ($data['users'] as $u) {
                                        if ($u['role'] === 'student') {
                                            $alreadyInCourse = in_array($u['id'], $course['enrolled_students']);
                                            if (!$alreadyInCourse) {
                                                echo '<option value="' . $u['id'] . '">' . htmlentities($u['username']) . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="enroll_in_course" class="btn btn-primary">
                                Enroll Student
                            </button>
                        </form>
                        <?php else: ?>
                            <p style="color:#6c757d; margin-top:1rem;"> All available students are enrolled</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleEditForm(courseId) {
            const form = document.getElementById(`edit-form-${courseId}`);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>
</html>