<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

$data = get_data();
$teacher_id = $_SESSION['user_id'];

if (isset($_POST['assign_course'])) {
    $course_name = $_POST['course_name'];
    $newCourse = [
        'id' => count($data['courses']) + 1,
        'course_name' => $course_name,
        'teacher_id' => $teacher_id
    ];
    $data['courses'][] = $newCourse;
    save_data($data);
}

if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    foreach ($data['courses'] as &$c) {
        if ($c['id'] == $course_id && $c['teacher_id'] == $teacher_id) {
            $c['course_name'] = $course_name;
        }
    }
    save_data($data);
}

if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    foreach ($data['courses'] as $idx => $c) {
        if ($c['id'] == $course_id && $c['teacher_id'] == $teacher_id) {
            unset($data['courses'][$idx]);
        }
    }
    $data['courses'] = array_values($data['courses']);
    save_data($data);
}

if (isset($_POST['enroll_in_course'])) {
    $course_id  = $_POST['course_id'];
    $student_id = $_POST['student_id'];
    $found      = false;
    $alreadyEnrolled = false;
    foreach ($data['courses'] as $c) {
        if ($c['id'] == $course_id && $c['teacher_id'] == $teacher_id) {
            $found = true;
            break;
        }
    }
    if ($found) {
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
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
        .logout:hover { background: #efefef; }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .section {
            background: #ffffff;
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .section h3 {
            margin-top: 0;
            font-weight: 600;
        }
        form {
            margin-bottom: 20px;
        }
        input, select, button {
            display: block;
            width: 100%;
            margin: 8px 0;
            padding: 10px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
        button {
            background: #0078d7;
            color: #ffffff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        button:hover {
            background: #005a9e;
        }
        .course-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            position: relative;
        }
        .course-box strong {
            display: inline-block;
            margin-right: 5px;
            font-weight: 600;
        }
        .inline-form {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .inline-form input[type="text"] {
            width: 200px;
            display: inline-block;
            margin: 0 5px;
        }
        .inline-form button {
            display: inline-block;
            margin: 0;
        }
        .enrolled-students {
            margin-top: 10px;
        }
        .enrolled-list {
            margin: 5px 0 0 20px;
            padding: 0;
        }
        .enrolled-list li {
            margin: 3px 0;
            list-style: circle;
        }
        label {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Teacher Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container">
        <div class="section">
            <h3>Assign New Course</h3>
            <form method="post">
                <input type="text" name="course_name" placeholder="New course name" required />
                <button type="submit" name="assign_course">Assign Course</button>
            </form>
        </div>

        <div class="section">
            <h3>Your Courses</h3>
            <?php
            $foundAnyCourse = false;
            foreach ($data['courses'] as $course) {
                if ($course['teacher_id'] == $teacher_id) {
                    $foundAnyCourse = true;
                    echo '<div class="course-box">';
                    echo '<div><strong>Course ID:</strong> ' . $course['id'] . ' — ' . $course['course_name'] . '</div>';

                    echo '<form method="post" class="inline-form">
                            <input type="hidden" name="course_id" value="' . $course['id'] . '">
                            <input type="text" name="course_name" placeholder="Updated name" required>
                            <button type="submit" name="update_course">Update</button>
                          </form>';

                    echo '<form method="post" class="inline-form">
                            <input type="hidden" name="course_id" value="' . $course['id'] . '">
                            <button type="submit" name="delete_course">Delete</button>
                          </form>';

                    // Enrolled students
                    echo '<div class="enrolled-students">';
                    echo '<strong>Enrolled Students:</strong>';
                    echo '<ul class="enrolled-list">';
                    $hasEnrolled = false;
                    foreach ($data['enrollments'] as $enroll) {
                        if ($enroll['course_id'] == $course['id']) {
                            $hasEnrolled = true;
                            foreach ($data['users'] as $u) {
                                if ($u['id'] == $enroll['student_id']) {
                                    echo '<li>' . htmlentities($u['username']) . '</li>';
                                    break;
                                }
                            }
                        }
                    }
                    if (!$hasEnrolled) {
                        echo '<li>No students enrolled yet.</li>';
                    }
                    echo '</ul>';
                    echo '</div>';

                    // Enrollment form for any students not already in this course
                    echo '<form method="post" style="margin-top:10px;">
                            <input type="hidden" name="course_id" value="' . $course['id'] . '">
                            <label>Enroll a new student:</label>
                            <select name="student_id" required>';

                    foreach ($data['users'] as $u) {
                        if ($u['role'] === 'student') {
                            $alreadyInCourse = false;
                            foreach ($data['enrollments'] as $enroll) {
                                if ($enroll['course_id'] == $course['id'] && $enroll['student_id'] == $u['id']) {
                                    $alreadyInCourse = true;
                                    break;
                                }
                            }
                            if (!$alreadyInCourse) {
                                echo '<option value="' . $u['id'] . '">' . htmlentities($u['username']) . '</option>';
                            }
                        }
                    }

                    echo '  </select>
                            <button type="submit" name="enroll_in_course">Enroll Student</button>
                          </form>';

                    echo '</div>';
                }
            }
            if (!$foundAnyCourse) {
                echo '<p>No courses assigned yet.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>