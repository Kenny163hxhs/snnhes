<?php
/**
 * Test Student Persistence System
 * National High School Enrollment System
 */

session_start();
require_once 'config/database.php';

// Test login (you can change these credentials)
$testUsername = 'admin';
$testPassword = 'admin123'; // Change this to your actual admin password

// Auto-login for testing
if (!isLoggedIn()) {
    $db = Database::getInstance();
    $user = $db->fetchOne("SELECT * FROM users WHERE username = ?", [$testUsername]);
    
    if ($user && password_verify($testPassword, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['user_role'] = $user['role'];
    }
}

// Get student statistics
$db = Database::getInstance();
$stats = $db->fetchOne("SELECT 
    COUNT(CASE WHEN status != 'canceled' THEN 1 END) as total,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive,
    COUNT(CASE WHEN status = 'graduated' THEN 1 END) as graduated,
    COUNT(CASE WHEN status = 'transferred' THEN 1 END) as transferred,
    COUNT(CASE WHEN status = 'dropped' THEN 1 END) as dropped,
    COUNT(CASE WHEN status = 'canceled' THEN 1 END) as canceled
    FROM students");

// Get recent students (including canceled ones for testing)
$recentStudents = $db->fetchAll("SELECT * FROM students ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Student Persistence - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Student Persistence System Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isLoggedIn()): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Successfully logged in as: <strong><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></strong>
                            </div>
                            
                            <!-- Statistics -->
                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-primary"><?= $stats['total'] ?></h4>
                                            <small>Total Active</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-success"><?= $stats['active'] ?></h4>
                                            <small>Active</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-info"><?= $stats['graduated'] ?></h4>
                                            <small>Graduated</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-warning"><?= $stats['transferred'] ?></h4>
                                            <small>Transferred</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-danger"><?= $stats['dropped'] ?></h4>
                                            <small>Dropped</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-dark"><?= $stats['canceled'] ?></h4>
                                            <small>Canceled</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Test Features -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>✅ Student Persistence Features:</h5>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Students remain visible after registration
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success me-2"></i>
                                            "Cancel Enrollment" instead of "Delete"
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Canceled students are hidden by default
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Students can retrieve their Student ID anytime
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Pending enrollments are rejected when canceled
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5>🧪 Test Actions:</h5>
                                    <div class="d-grid gap-2">
                                        <a href="modules/students/list.php" class="btn btn-primary">
                                            <i class="fas fa-list me-2"></i>
                                            View Student List
                                        </a>
                                        
                                        <a href="student_register.php" class="btn btn-success">
                                            <i class="fas fa-user-plus me-2"></i>
                                            Register New Student
                                        </a>
                                        
                                        <a href="check_enrollment.php" class="btn btn-info">
                                            <i class="fas fa-search me-2"></i>
                                            Check Enrollment Status
                                        </a>
                                        
                                        <a href="index.php" class="btn btn-outline-primary">
                                            <i class="fas fa-home me-2"></i>
                                            Go to Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Students -->
                            <h5>Recent Students (Last 10):</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Student ID</th>
                                            <th>Status</th>
                                            <th>Enrollment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentStudents as $student): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                                <td><?= htmlspecialchars($student['student_id']) ?></td>
                                                <td>
                                                    <?php
                                                    $statusColors = [
                                                        'active' => 'success',
                                                        'inactive' => 'secondary',
                                                        'graduated' => 'info',
                                                        'transferred' => 'warning',
                                                        'dropped' => 'danger',
                                                        'canceled' => 'dark'
                                                    ];
                                                    $statusColor = $statusColors[$student['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($student['status']) ?></span>
                                                </td>
                                                <td><?= formatDate($student['enrollment_date']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Not logged in. Please check your admin credentials.
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Go to Login
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
