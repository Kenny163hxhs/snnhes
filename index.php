<?php
/**
 * National High School Enrollment System
 */

session_start();
require_once 'config/database.php';
$flash = getFlashMessage();
$isLoggedIn = isLoggedIn();
$userRole = getUserRole();
$page = $_GET['page'] ?? 'home';


$db = Database::getInstance();
$currentPeriod = $db->fetchOne("SELECT * FROM enrollment_periods WHERE is_active = 1 ORDER BY start_date DESC LIMIT 1");
$currentAcademicYear = $currentPeriod['academic_year'] ?? date('Y') . '-' . (date('Y') + 1);
$currentSemester = $currentPeriod['semester'] ?? 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - National High School Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: scale(1.02);
        }
        .quick-action-btn {
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .academic-year-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php 
$baseUrl = '';
include 'includes/navbar.php'; 
?>

    <?php if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($page === 'home'): ?>
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="fas fa-graduation-cap me-3"></i>
                            Welcome to <?= APP_NAME ?>
                        </h1>
                        <p class="lead mb-4">
                            Comprehensive enrollment management system designed to streamline student registration, 
                            academic record-keeping, and class scheduling for high schools nationwide.
                        </p>
                        <?php if (!$isLoggedIn): ?>
                            <div class="d-flex gap-3">
                                <a href="login.php" class="btn btn-light btn-lg quick-action-btn">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Access System
                                </a>
                                <a href="student_register.php" class="btn btn-outline-light btn-lg quick-action-btn">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Student Registration
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 text-center">
                        <i class="fas fa-school display-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </section>

        <main class="container my-5">
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-chart-bar me-2"></i>System Overview
                    </h2>
                </div>
                
                <?php
                try {
                    $stats = [
                        'students' => [
                            'total' => 0,
                            'new_this_year' => 0,
                            'icon' => 'fas fa-user-graduate',
                            'color' => 'primary',
                            'label' => 'Active Students'
                        ],
                        'teachers' => [
                            'total' => 0,
                            'specializations' => 0,
                            'icon' => 'fas fa-chalkboard-teacher',
                            'color' => 'success',
                            'label' => 'Active Teachers'
                        ],
                        'courses' => [
                            'total' => 0,
                            'subjects' => 0,
                            'icon' => 'fas fa-book-open',
                            'color' => 'info',
                            'label' => 'Available Courses'
                        ],
                        'sections' => [
                            'total' => 0,
                            'capacity' => 0,
                            'icon' => 'fas fa-layer-group',
                            'color' => 'warning',
                            'label' => 'Active Sections'
                        ],
                        'enrollments' => [
                            'pending' => 0,
                            'approved' => 0,
                            'icon' => 'fas fa-clipboard-list',
                            'color' => 'danger',
                            'label' => 'Pending Enrollments'
                        ]
                    ];
                    $tables = $db->fetchAll("SHOW TABLES LIKE 'students'");
                    if (!empty($tables)) {
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
                        $stats['students']['total'] = $result['count'] ?? 0;
                        
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM students WHERE status = 'active' AND YEAR(enrollment_date) = YEAR(CURDATE())");
                        $stats['students']['new_this_year'] = $result['count'] ?? 0;
                    }

                    $tables = $db->fetchAll("SHOW TABLES LIKE 'teachers'");
                    if (!empty($tables)) {
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM teachers WHERE is_active = 1");
                        $stats['teachers']['total'] = $result['count'] ?? 0;
                        
                        $result = $db->fetchOne("SELECT COUNT(DISTINCT specialization) as count FROM teachers WHERE is_active = 1");
                        $stats['teachers']['specializations'] = $result['count'] ?? 0;
                    }

                    $tables = $db->fetchAll("SHOW TABLES LIKE 'courses'");
                    if (!empty($tables)) {
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM courses WHERE is_active = 1");
                        $stats['courses']['total'] = $result['count'] ?? 0;
                    }

                    $tables = $db->fetchAll("SHOW TABLES LIKE 'subjects'");
                    if (!empty($tables)) {
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM subjects WHERE is_active = 1");
                        $stats['courses']['subjects'] = $result['count'] ?? 0;
                    }

                    $tables = $db->fetchAll("SHOW TABLES LIKE 'class_sections'");
                    if (!empty($tables)) {
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM class_sections WHERE is_active = 1 AND academic_year = ?", [$currentAcademicYear]);
                        $stats['sections']['total'] = $result['count'] ?? 0;
                        
                        $result = $db->fetchOne("SELECT SUM(max_students) as total FROM class_sections WHERE is_active = 1 AND academic_year = ?", [$currentAcademicYear]);
                        $stats['sections']['capacity'] = $result['total'] ?? 0;
                    }

                    $tables = $db->fetchAll("SHOW TABLES LIKE 'enrollments'");
                    if (!empty($tables)) {
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM enrollments WHERE status = 'pending' AND academic_year = ?", [$currentAcademicYear]);
                        $stats['enrollments']['pending'] = $result['count'] ?? 0;
                        
                        $result = $db->fetchOne("SELECT COUNT(*) as count FROM enrollments WHERE status = 'approved' AND academic_year = ?", [$currentAcademicYear]);
                        $stats['enrollments']['approved'] = $result['count'] ?? 0;
                    }

                } catch (Exception $e) {
                    error_log("Database error in index.php: " . $e->getMessage());
                }
                ?>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="card stat-card h-100" style="border-left-color: var(--bs-primary);">
                        <div class="card-body text-center">
                            <i class="<?= $stats['students']['icon'] ?> fa-2x text-primary mb-3"></i>
                            <h4 class="fw-bold text-primary"><?= number_format($stats['students']['total']) ?></h4>
                            <p class="card-text mb-1"><?= $stats['students']['label'] ?></p>
                            <small class="text-muted">+<?= $stats['students']['new_this_year'] ?> this year</small>
                        </div>
                    </div>
                </div>
                
               
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="card stat-card h-100" style="border-left-color: var(--bs-info);">
                        <div class="card-body text-center">
                            <i class="<?= $stats['courses']['icon'] ?> fa-2x text-info mb-3"></i>
                            <h4 class="fw-bold text-info"><?= number_format($stats['courses']['total']) ?></h4>
                            <p class="card-text mb-1"><?= $stats['courses']['label'] ?></p>
                            
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="card stat-card h-100" style="border-left-color: var(--bs-warning);">
                        <div class="card-body text-center">
                            <i class="<?= $stats['sections']['icon'] ?> fa-2x text-warning mb-3"></i>
                            <h4 class="fw-bold text-warning"><?= number_format($stats['sections']['total']) ?></h4>
                            <p class="card-text mb-1"><?= $stats['sections']['label'] ?></p>
            
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="card stat-card h-100" style="border-left-color: var(--bs-danger);">
                        <div class="card-body text-center">
                            <i class="<?= $stats['enrollments']['icon'] ?> fa-2x text-danger mb-3"></i>
                            <h4 class="fw-bold text-danger"><?= number_format($stats['enrollments']['pending']) ?></h4>
                            <p class="card-text mb-1"><?= $stats['enrollments']['label'] ?></p>
                            <small class="text-muted"><?= number_format($stats['enrollments']['approved']) ?> approved</small>
                        </div>
                    </div>
                </div>

                
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <h3 class="mb-4">
                        <i class="fas fa-star me-2"></i>System Features
                    </h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card feature-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Student Management</h5>
                                    <p class="card-text">Complete student registration system with document management and status monitoring.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card feature-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Academic Programs</h5>
                                    <p class="card-text">Manage STEM, ABM, HUMSS, and GAS programs.</p>
                                </div>
                            </div>
                        </div>
                       
                       
                        <div class="col-md-6 mb-4">
                            <div class="card feature-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Enrollment Management</h5>
                                    <p class="card-text">Streamlined enrollment process.</p>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>


                <div class="col-lg-4">
                    <?php if ($isLoggedIn): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (in_array($userRole, ['admin', 'registrar', 'teacher'])): ?>
                                    <a href="modules/students/list.php" class="btn btn-primary w-100 mb-3 quick-action-btn">
                                        <i class="fas fa-users me-2"></i>
                                        View All Students
                                    </a>
                                    <a href="modules/students/register.php" class="btn btn-success w-100 mb-3 quick-action-btn">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Register New Student
                                    </a>
                                    <a href="modules/enrollment/manage.php" class="btn btn-info w-100 mb-3 quick-action-btn">
                                        <i class="fas fa-clipboard-list me-2"></i>
                                        Process Enrollments
                                    </a>
                                    <a href="modules/sections/manage.php" class="btn btn-warning w-100 mb-3 quick-action-btn">
                                        <i class="fas fa-layer-group me-2"></i>
                                        Manage Class Sections
                                    </a>
                                <?php endif; ?>
                                
                               
                             
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-rocket me-2"></i>Get Started
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Access the system to manage your school's enrollment and academic records.</p>
                                <a href="login.php" class="btn btn-success w-100 mb-3 quick-action-btn">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Staff/Admin Login
                                </a>
                                <a href="student_register.php" class="btn btn-outline-primary w-100 mb-3 quick-action-btn">
                                    <i class="fas fa-user-plus me-2"></i>
                                    New Student Registration
                                </a>
                                <a href="check_enrollment.php" class="btn btn-outline-info w-100 quick-action-btn">
                                    <i class="fas fa-search me-2"></i>
                                    Check Enrollment Status
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                  

                </div>
            </div>
        </main>
    <?php else: ?>
        <main class="container my-4">
            <?php
            $pageFile = "modules/{$page}.php";
            if (file_exists($pageFile)) {
                include $pageFile;
            } else {
                ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Page Not Found!</strong> The requested page could not be found.
                        <a href="index.php" class="alert-link">Return to Home</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </main>
    <?php endif; ?>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.transition = 'all 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                this.disabled = true;
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        });
    </script>
</body>
</html>