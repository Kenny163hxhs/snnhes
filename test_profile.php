<?php
/**
 * Test Profile Management
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Profile Management - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-cog me-2"></i>
                            Profile Management Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isLoggedIn()): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Successfully logged in as: <strong><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="modules/admin/profile.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Open Profile Management
                                </a>
                                
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-2"></i>
                                    Go to Dashboard
                                </a>
                                
                                <a href="logout.php" class="btn btn-outline-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </a>
                            </div>
                            
                            <hr>
                            
                            <h5>Test Features:</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Profile information update (username, email, name)
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Password change with current password verification
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Form validation and error handling
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Responsive design with Bootstrap
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Navigation integration
                                </li>
                            </ul>
                            
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
