<?php
session_start();
require_once 'config.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$data = get_data();
if (isset($_POST['insert_course'])) {
    $course_name = $_POST['course_name'];
    $teacher_id = $_POST['teacher_id'] ?? 0;
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
    foreach ($data['courses'] as &$course) {
        if ($course['id'] == $course_id) {
            $course['course_name'] = $course_name;
        }
    }
    save_data($data);
}
if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    foreach ($data['courses'] as $index => $course) {
        if ($course['id'] == $course_id) {
            unset($data['courses'][$index]);
        }
    }
    $data['courses'] = array_values($data['courses']);
    save_data($data);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #212121;
            color: #fff;
            padding: 20px 30px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .header h2 {
            margin: 0;
            font-weight: 400;
        }
        .logout {
            position: absolute; top: 20px; right: 30px;
            background: #fff; color: #212121;
            padding: 8px 12px; text-decoration: none;
            border-radius: 5px;
        }
        .logout:hover {
            background: #ededed;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .box {
            background: #fff;
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .box h3 {
            margin-top: 0;
            font-weight: 600;
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
            background: #0078d7; color: #fff;
            border: none; cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        button:hover {
            background: #005a9e;
        }
        .course-list {
            margin: 0; padding: 0;
            list-style: none;
        }
        .course-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .course-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Admin Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="container">
        <div class="box">
            <h3>Insert Course / Section</h3>
            <form method="post">
                <input type="text" name="course_name" placeholder="Course/Section name" required>
                <select name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php
                    foreach ($data['users'] as $user) {
                        if (isset($user['role']) && $user['role'] === 'teacher') {
                            echo '<option value="' . $user['id'] . '">' . htmlentities($user['username']) . ' (ID: ' . $user['id'] . ')</option>';
                        }
                    }
                    ?>
                </select>
                <button type="submit" name="insert_course">Insert</button>
            </form>
        </div>

        <div class="box">
            <h3>Update Course</h3>
            <form method="post">
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    foreach ($data['courses'] as $course) {
                        echo '<option value="' . $course['id'] . '">' . htmlentities($course['course_name']) 
                             . ' (ID: ' . $course['id'] . ')</option>';
                    }
                    ?>
                </select>
                <input type="text" name="course_name" placeholder="New course name" required>
                <button type="submit" name="update_course">Update</button>
            </form>
        </div>

        <div class="box">
            <h3>Delete Course</h3>
            <form method="post">
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    foreach ($data['courses'] as $course) {
                        echo '<option value="' . $course['id'] . '">' . htmlentities($course['course_name']) 
                             . ' (ID: ' . $course['id'] . ')</option>';
                    }
                    ?>
                </select>
                <button type="submit" name="delete_course">Delete</button>
            </form>
        </div>

        <div class="box">
            <h3>Current Courses</h3>
            <ul class="course-list">
                <?php
                foreach ($data['courses'] as $course) {
                    echo '<li class="course-item">Course ID: ' . $course['id'] . ' — '
                         . htmlentities($course['course_name']) . ' (Teacher ID: ' . $course['teacher_id'] . ')</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</body>
</html>