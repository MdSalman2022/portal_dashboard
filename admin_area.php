<?php
session_start();
require_once 'config.php';
require_once 'header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$data = get_data();

if (isset($_POST['insert_course'])) {
    $course_name = $_POST['course_name'];
    $teacher_id = $_POST['teacher_id'];
    $query = "INSERT INTO courses (course_name, teacher_id) VALUES ('$course_name', $teacher_id)";
    db_query($query);
    $data = get_data();
}

if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $query = "UPDATE courses SET course_name = '$course_name' WHERE id = $course_id";
    db_query($query);
    $data = get_data();
}

if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $query = "DELETE FROM courses WHERE id = $course_id";
    db_query($query);
    $data = get_data();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            background: linear-gradient(to right, #252525, #444444);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
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
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px var(--shadow);
            overflow: hidden;
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
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
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
            width: 100%;
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
        
        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .course-table th {
            background-color: var(--light);
            padding: 0.8rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border);
        }
        
        .course-table td {
            padding: 0.8rem;
            border-bottom: 1px solid var(--border);
        }
        
        .course-table tr:last-child td {
            border-bottom: none;
        }
        
        .course-table tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            color: white;
        }
        
        .badge-primary {
            background-color: var(--primary);
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
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
            <i class="fas fa-cog"></i> Course Management
        </h1>
        
        <div class="dashboard-grid">
            <!-- Insert Course Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-plus-circle"></i> Add New Course
                    </h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="course_name" class="form-label">Course Name</label>
                            <input type="text" id="course_name" name="course_name" class="form-control" placeholder="Enter course name" required>
                        </div>
                        <div class="form-group">
                            <label for="teacher_id" class="form-label">Assign Teacher</label>
                            <select id="teacher_id" name="teacher_id" class="form-control" required>
                                <option value="">-- Select Teacher --</option>
                                <?php
                                foreach ($data['users'] as $user) {
                                    if (isset($user['role']) && $user['role'] === 'teacher') {
                                        echo '<option value="' . $user['id'] . '">' . htmlentities($user['username']) . ' (ID: ' . $user['id'] . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="insert_course" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Course
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Update Course Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-edit"></i> Update Course
                    </h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="update_course_id" class="form-label">Select Course</label>
                            <select id="update_course_id" name="course_id" class="form-control" required>
                                <option value="">-- Select Course --</option>
                                <?php
                                foreach ($data['courses'] as $course) {
                                    echo '<option value="' . $course['id'] . '">' . htmlentities($course['course_name']) 
                                         . ' (ID: ' . $course['id'] . ')</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="update_course_name" class="form-label">New Course Name</label>
                            <input type="text" id="update_course_name" name="course_name" class="form-control" placeholder="Enter new name" required>
                        </div>
                        <button type="submit" name="update_course" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Course
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Delete Course Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-trash-alt"></i> Delete Course
                    </h2>
                </div>
                <div class="card-body">
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this course?');">
                        <div class="form-group">
                            <label for="delete_course_id" class="form-label">Select Course</label>
                            <select id="delete_course_id" name="course_id" class="form-control" required>
                                <option value="">-- Select Course --</option>
                                <?php
                                foreach ($data['courses'] as $course) {
                                    echo '<option value="' . $course['id'] . '">' . htmlentities($course['course_name']) 
                                         . ' (ID: ' . $course['id'] . ')</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="delete_course" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Course Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Current Courses
                </h2>
            </div>
            <div class="card-body">
                <?php if (empty($data['courses'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open fa-3x" style="color:#dee2e6; margin-bottom:1rem;"></i>
                        <p>No courses available. Create your first course using the form above.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="course-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Course Name</th>
                                    <th>Teacher</th>
                                    <th>Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['courses'] as $course): 
                             
                                    $teacher_name = "Unknown";
                                    foreach ($data['users'] as $user) {
                                        if ($user['id'] == $course['teacher_id']) {
                                            $teacher_name = $user['username'];
                                            break;
                                        }
                                    }
                                    
                                    $enrolled_count = !empty($course['enrolled_students']) ? 
                                                     (is_array($course['enrolled_students']) ? 
                                                      count($course['enrolled_students']) : 0) : 0;
                                ?>
                                <tr>
                                    <td><?= $course['id']; ?></td>
                                    <td><?= htmlentities($course['course_name']); ?></td>
                                    <td><?= htmlentities($teacher_name); ?></td>
                                    <td>
                                        <span class="badge badge-primary">
                                            <?= $enrolled_count; ?> students
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>