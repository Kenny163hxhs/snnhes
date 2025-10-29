<?php
/**
 * Check Enrollment Status
 * National High School Enrollment System
 */

session_start();
require_once 'config/database.php';

$db = Database::getInstance();
$result = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $studentId = sanitize($_POST['student_id'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($studentId) && empty($email)) {
            throw new Exception("Please provide either Student ID or Email address.");
        }
        
        $whereClause = '';
        $params = [];
        
        if (!empty($studentId)) {
            $whereClause = "s.student_id = ?";
            $params[] = $studentId;
        } else {
            $whereClause = "s.email = ?";
            $params[] = $email;
        }
        
        $result = $db->fetchOne("
            SELECT s.*, e.status as enrollment_status, e.enrollment_date, 
                   cs.section_name, c.course_name, c.course_code,
                   sd.file_path as selfie_path
            FROM students s
            LEFT JOIN enrollments e ON s.id = e.student_id
            LEFT JOIN class_sections cs ON e.section_id = cs.id
            LEFT JOIN courses c ON cs.course_id = c.id
            LEFT JOIN student_documents sd ON s.id = sd.student_id AND sd.document_type = 'selfie'
            WHERE $whereClause
            ORDER BY e.created_at DESC
            LIMIT 1
        ", $params);
        
        if (!$result) {
            throw new Exception("No student found with the provided information.");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Enrollment Status - <?= APP_NAME ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .check-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .info-card {
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>
                <?= APP_NAME ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home me-1"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </nav>

    <div class="check-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-search me-3"></i>
                        Check Enrollment Status
                    </h1>
                    <p class="lead mb-0">
                        Enter your Student ID or Email address to check your enrollment status.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-clipboard-check display-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>
                            Enrollment Status Checker
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" 
                                           value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>" 
                                           placeholder="e.g., STU2025ABC123">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                           placeholder="your.email@example.com">
                                </div>
                            </div>
                            
                            <div class="text-center mb-3">
                                <small class="text-muted">Provide either Student ID or Email address</small>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>
                                    Check Status
                                </button>
                            </div>
                        </form>

                        <?php if ($result): ?>
                            <hr class="my-4">
                            
                            <div class="card info-card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Student Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> <?= htmlspecialchars($result['first_name'] . ' ' . $result['middle_name'] . ' ' . $result['last_name']) ?></p>
                                            <p><strong>Student ID:</strong> <?= htmlspecialchars($result['student_id']) ?></p>
                                            <p><strong>Email:</strong> <?= htmlspecialchars($result['email'] ?: 'Not provided') ?></p>
                                            <p><strong>Phone:</strong> <?= htmlspecialchars($result['phone'] ?: 'Not provided') ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Date of Birth:</strong> <?= formatDate($result['date_of_birth']) ?></p>
                                            <p><strong>Gender:</strong> <?= ucfirst($result['gender']) ?></p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge bg-<?= $result['status'] === 'active' ? 'success' : 'secondary' ?> status-badge">
                                                    <?= ucfirst($result['status']) ?>
                                                </span>
                                            </p>
                                            <?php if (!empty($result['selfie_path'])): ?>
                                                <div class="mt-3">
                                                    <strong>Student Photo:</strong><br>
                                                    <?php 
                                                    // Cross-platform file existence check using helper function
                                                    $fileExists = isFileAccessible($result['selfie_path']);
                                                    ?>
                                                    
                                                    <?php if ($fileExists): ?>
                                                        <img src="view_document_by_path.php?path=<?= urlencode($result['selfie_path']) ?>" 
                                                             alt="Student Photo" 
                                                             style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 2px solid #dee2e6;">
                                                    <?php else: ?>
                                                        <div class="alert alert-warning">
                                                            <small>Photo file not found: <?= htmlspecialchars($result['selfie_path']) ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($result['enrollment_status']): ?>
                                <div class="card info-card mt-3">
                                    <div class="card-header bg-<?= $result['enrollment_status'] === 'approved' ? 'success' : ($result['enrollment_status'] === 'pending' ? 'warning' : 'danger') ?> text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-graduation-cap me-2"></i>
                                            Enrollment Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Enrollment Status:</strong> 
                                                    <span class="badge bg-<?= $result['enrollment_status'] === 'approved' ? 'success' : ($result['enrollment_status'] === 'pending' ? 'warning' : 'danger') ?> status-badge">
                                                        <?= ucfirst($result['enrollment_status']) ?>
                                                    </span>
                                                </p>
                                                <p><strong>Course:</strong> <?= htmlspecialchars($result['course_name'] ?? 'Not assigned') ?></p>
                                                <p><strong>Section:</strong> <?= htmlspecialchars($result['section_name'] ?? 'Not assigned') ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Enrollment Date:</strong> <?= formatDate($result['enrollment_date']) ?></p>
                                                <p><strong>Course Code:</strong> <?= htmlspecialchars($result['course_code'] ?? 'N/A') ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if ($result['enrollment_status'] === 'pending'): ?>
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Your enrollment is currently pending approval. You will be notified once it's approved.
                                            </div>
                                        <?php elseif ($result['enrollment_status'] === 'approved'): ?>
                                            <div class="alert alert-success mt-3">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Congratulations! Your enrollment has been approved. Welcome to our school!
                                            </div>
                                        <?php elseif ($result['enrollment_status'] === 'rejected'): ?>
                                            <div class="alert alert-danger mt-3">
                                                <i class="fas fa-times-circle me-2"></i>
                                                Your enrollment application was not approved. Please contact the school administration for more information.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No enrollment record found. Please complete your registration first.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
