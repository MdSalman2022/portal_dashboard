<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$data = get_data();
$student_id = $_SESSION['user_id'];

if (isset($_POST['enroll_course'])) {
    $course_id = $_POST['course_id'];
    $alreadyEnrolled = false;
    foreach ($data['enrollments'] as $enroll) {
        if ($enroll['course_id'] == $course_id && $enroll['student_id'] == $student_id) {
            $alreadyEnrolled = true;
            break;
        }
    }
    if (!$alreadyEnrolled) {
        $data['enrollments'][] = ['course_id' => $course_id, 'student_id' => $student_id];
        save_data($data);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #262626;
            color: #ffffff;
            padding: 20px 30px;
            position: relative;
        }
        .header h2 {
            margin: 0;
            font-weight: 400;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 30px;
            background: #ffffff;
            color: #262626;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout:hover {
            background: #efefef;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        h3 {
            margin-top: 0;
            font-weight: 600;
        }
        .course-card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .course-header {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .course-action {
            margin-top: 10px;
        }
        button {
            background: #0078d7;
            color: #ffffff;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        button:hover {
            background: #005a9e;
        }
        .enrolled-badge {
            color: green;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="container">
        <h3>Available Courses</h3>
        <?php
        foreach ($data['courses'] as $course) {
            echo '<div class="course-card">';
            echo '<div class="course-header">Course ID: ' . $course['id'] . ' — ' . $course['course_name'] . '</div>';
            $enrolled = false;
            foreach ($data['enrollments'] as $enroll) {
                if ($enroll['course_id'] == $course['id'] && $enroll['student_id'] == $student_id) {
                    $enrolled = true;
                    break;
                }
            }
            echo '<div class="course-action">';
            if (!$enrolled) {
                echo '<form method="post" style="display:inline-block; margin-right:10px;">
                        <input type="hidden" name="course_id" value="' . $course['id'] . '">
                        <button type="submit" name="enroll_course">Enroll</button>
                      </form>';
            } else {
                echo '<span class="enrolled-badge">[Enrolled]</span>';
            }
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>