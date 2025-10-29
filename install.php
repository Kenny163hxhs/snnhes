<?php
/**
 * Installation Script
 * National High School Enrollment System
 */

if (file_exists('config/installed.txt')) {
    die('System is already installed. Remove config/installed.txt to reinstall.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            $dbHost = $_POST['db_host'] ?? 'localhost';
            $dbName = $_POST['db_name'] ?? 'snnhes_db';
            $dbUser = $_POST['db_user'] ?? 'root';
            $dbPass = $_POST['db_pass'] ?? '';
            try {
                $dsn = "mysql:host=$dbHost;charset=utf8mb4";
                $pdo = new PDO($dsn, $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $configContent = "<?php
// Database configuration
define('DB_HOST', '$dbHost');
define('DB_NAME', '$dbName');
define('DB_USER', '$dbUser');
define('DB_PASS', '$dbPass');
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('APP_NAME', 'SNNHES');
define('APP_VERSION', '1.0.0');
define('UPLOAD_PATH', '../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

class Database {
    private \$connection;
    private static \$instance = null;

    private function __construct() {
        try {
            \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;
            \$this->connection = new PDO(\$dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException \$e) {
            die(\"Connection failed: \" . \$e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::\$instance === null) {
            self::\$instance = new self();
        }
        return self::\$instance;
    }

    public function getConnection() {
        return \$this->connection;
    }

    public function query(\$sql, \$params = []) {
        try {
            \$stmt = \$this->connection->prepare(\$sql);
            \$stmt->execute(\$params);
            return \$stmt;
        } catch (PDOException \$e) {
            die(\"Query failed: \" . \$e->getMessage());
        }
    }

    public function fetchAll(\$sql, \$params = []) {
        return \$this->query(\$sql, \$params)->fetchAll();
    }

    public function fetchOne(\$sql, \$params = []) {
        return \$this->query(\$sql, \$params)->fetch();
    }

    public function insert(\$table, \$data) {
        \$columns = implode(', ', array_keys(\$data));
        \$placeholders = ':' . implode(', :', array_keys(\$data));
        
        \$sql = \"INSERT INTO \$table (\$columns) VALUES (\$placeholders)\";
        \$this->query(\$sql, \$data);
        
        return \$this->connection->lastInsertId();
    }

    public function update(\$table, \$data, \$where, \$whereParams = []) {
        \$setClause = [];
        foreach (array_keys(\$data) as \$column) {
            \$setClause[] = \"\$column = :\$column\";
        }
        \$setClause = implode(', ', \$setClause);
        
        \$sql = \"UPDATE \$table SET \$setClause WHERE \$where\";
        \$params = array_merge(\$data, \$whereParams);
        
        return \$this->query(\$sql, \$params)->rowCount();
    }

    public function delete(\$table, \$where, \$params = []) {
        \$sql = \"DELETE FROM \$table WHERE \$where\";
        return \$this->query(\$sql, \$params)->rowCount();
    }
}

// Helper functions
function sanitize(\$input) {
    return htmlspecialchars(trim(\$input), ENT_QUOTES, 'UTF-8');
}

function validateEmail(\$email) {
    return filter_var(\$email, FILTER_VALIDATE_EMAIL);
}

function generateStudentId() {
    \$year = date('Y');
    \$random = strtoupper(substr(md5(uniqid()), 0, 6));
    return \"STU{\$year}{\$random}\";
}

function formatDate(\$date) {
    return date('F j, Y', strtotime(\$date));
}

function formatDateTime(\$datetime) {
    return date('F j, Y g:i A', strtotime(\$datetime));
}

function isLoggedIn() {
    return isset(\$_SESSION['user_id']);
}

function getUserRole() {
    return \$_SESSION['user_role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireRole(\$role) {
    requireLogin();
    if (getUserRole() !== \$role && getUserRole() !== 'admin') {
        header('Location: unauthorized.php');
        exit();
    }
}

function redirect(\$url) {
    header(\"Location: \$url\");
    exit();
}

function setFlashMessage(\$type, \$message) {
    \$_SESSION['flash'] = [
        'type' => \$type,
        'message' => \$message
    ];
}

function getFlashMessage() {
    if (isset(\$_SESSION['flash'])) {
        \$flash = \$_SESSION['flash'];
        unset(\$_SESSION['flash']);
        return \$flash;
    }
    return null;
}
?>";

                file_put_contents('config/database.php', $configContent);
                $sqlFile = file_get_contents('database/snnhes_db.sql');
                $pdo->exec("USE `$dbName`");
                $statements = array_filter(array_map('trim', explode(';', $sqlFile)));
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $pdo->exec($statement);
                    }
                }
                
                $success = 'Database configured successfully!';
                $step = 2;
                
            } catch (Exception $e) {
                $error = 'Database connection failed: ' . $e->getMessage();
            }
            break;
            
        case 2:
            $adminUsername = $_POST['admin_username'] ?? 'admin';
            $adminPassword = $_POST['admin_password'] ?? '';
            $adminEmail = $_POST['admin_email'] ?? '';
            
            if (empty($adminPassword)) {
                $error = 'Admin password is required.';
            } else {
                try {
                    require_once 'config/database.php';
                    $db = Database::getInstance();
                    
                    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
                    $db->update('users', [
                        'username' => $adminUsername,
                        'password' => $hashedPassword,
                        'email' => $adminEmail
                    ], 'role = ?', ['admin']);
                    
                    $success = 'Admin user configured successfully!';
                    $step = 3;
                    
                } catch (Exception $e) {
                    $error = 'Failed to configure admin user: ' . $e->getMessage();
                }
            }
            break;
            
        case 3:
            $directories = [
                'uploads',
                'uploads/students',
                'uploads/documents'
            ];
            
            foreach ($directories as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
            }
            
            // Mark as installed
            file_put_contents('config/installed.txt', date('Y-m-d H:i:s'));
            
            $success = 'Installation completed successfully!';
            $step = 4;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - <?= APP_NAME ?? 'SNNHES' ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                            <h2 class="text-primary">SNNHES Installation</h2>
                            <p class="text-muted">National High School Enrollment System</p>
                        </div>
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= ($step / 4) * 100 ?>%"></div>
                        </div>
                        <div class="row text-center mb-4">
                            <div class="col-3">
                                <div class="step-indicator <?= $step >= 1 ? 'active' : '' ?>">
                                    <i class="fas fa-database"></i>
                                    <small>Database</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="step-indicator <?= $step >= 2 ? 'active' : '' ?>">
                                    <i class="fas fa-user-shield"></i>
                                    <small>Admin</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="step-indicator <?= $step >= 3 ? 'active' : '' ?>">
                                    <i class="fas fa-cogs"></i>
                                    <small>Setup</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="step-indicator <?= $step >= 4 ? 'active' : '' ?>">
                                    <i class="fas fa-check"></i>
                                    <small>Complete</small>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step == 1): ?>
                            <h5 class="mb-3">Database Configuration</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" 
                                           value="snnhes_db" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" 
                                           value="root" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Continue
                                </button>
                            </form>
                            
                        <?php elseif ($step == 2): ?>
                            <h5 class="mb-3">Admin User Configuration</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="admin_username" class="form-label">Admin Username</label>
                                    <input type="text" class="form-control" id="admin_username" name="admin_username" 
                                           value="admin" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                           required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                           value="admin@snnhes.edu" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Continue
                                </button>
                            </form>
                            
                        <?php elseif ($step == 3): ?>
                            <h5 class="mb-3">Final Setup</h5>
                            <p class="text-muted">The system will now create necessary directories and complete the installation.</p>
                            <form method="POST">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-cogs me-2"></i>
                                    Complete Installation
                                </button>
                            </form>
                            
                        <?php elseif ($step == 4): ?>
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">Installation Complete!</h5>
                                <p class="text-muted">Your National High School Enrollment System has been successfully installed.</p>
                                
                                <div class="alert alert-info">
                                    <strong>Default Login Credentials:</strong><br>
                                    Username: <code>admin</code><br>
                                    Password: <code>The password you set</code>
                                </div>
                                
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>
                                    Go to System
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .step-indicator {
            padding: 10px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .step-indicator.active {
            background-color: #0d6efd;
            color: white;
        }
        
        .step-indicator i {
            font-size: 1.5rem;
        }
    </style>
</body>
</html> 