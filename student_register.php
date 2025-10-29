<?php
/**
 * Public Student Registration Form
 * National High School Enrollment System
 */

session_start();
require_once 'config/database.php';
require_once 'includes/simple_image_processor.php';

$db = Database::getInstance();
$success = '';
$error = '';

// Get available courses and sections for the current academic year
// Force fresh query with cache busting
$courses = $db->fetchAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_name");

// Debug: Log courses for troubleshooting
error_log("Courses loaded for registration: " . count($courses));
error_log("Course details: " . json_encode(array_column($courses, 'course_name')));
$currentPeriod = $db->fetchOne("SELECT * FROM enrollment_periods WHERE is_active = 1 ORDER BY start_date DESC LIMIT 1");
$currentAcademicYear = $currentPeriod['academic_year'] ?? date('Y') . '-' . (date('Y') + 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'city', 'state', 'zip_code', 'course_id', 'year_level'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields.");
            }
        }

        // Validate email if provided
        if (!empty($_POST['email']) && !validateEmail($_POST['email'])) {
            throw new Exception("Please enter a valid email address.");
        }

        // Check if student already exists with same email or phone
        if (!empty($_POST['email'])) {
            $existingStudent = $db->fetchOne("SELECT id FROM students WHERE email = ?", [$_POST['email']]);
            if ($existingStudent) {
                throw new Exception("A student with this email address already exists.");
            }
        }

        // Generate unique student ID
        $studentId = generateStudentId();

        // Insert student data
        $studentData = [
            'student_id' => $studentId,
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'middle_name' => sanitize($_POST['middle_name'] ?? ''),
            'date_of_birth' => $_POST['date_of_birth'],
            'gender' => $_POST['gender'],
            'address' => sanitize($_POST['address']),
            'city' => sanitize($_POST['city']),
            'state' => sanitize($_POST['state']),
            'zip_code' => sanitize($_POST['zip_code']),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'emergency_contact_name' => sanitize($_POST['emergency_contact_name'] ?? ''),
            'emergency_contact_phone' => sanitize($_POST['emergency_contact_phone'] ?? ''),
            'emergency_contact_relationship' => sanitize($_POST['emergency_contact_relationship'] ?? ''),
            'enrollment_date' => date('Y-m-d'),
            'status' => 'active'
        ];
        
        $studentDbId = $db->insert('students', $studentData);

        // Handle selfie photo
        if (!empty($_POST['selfie_data'])) {
            $uploadDir = getUploadDirectory('students');
            
            if (!is_writable($uploadDir)) {            
                chmod($uploadDir, 0755);
            }

            // Decode base64 image
            $selfieData = $_POST['selfie_data'];
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selfieData));
            
            // Generate filename for selfie
            $selfieFilename = $studentDbId . '_selfie_' . time() . '.jpg';
            $selfiePath = $uploadDir . $selfieFilename;
            
            // Save selfie image
            if (file_put_contents($selfiePath, $imageData)) {
                // Store relative path for cross-platform compatibility
                $relativePath = 'uploads/students/' . $selfieFilename;
                
                $selfieData = [
                    'student_id' => $studentDbId,
                    'document_type' => 'selfie',
                    'file_name' => $selfieFilename,
                    'file_path' => $relativePath,
                    'file_size' => strlen($imageData)
                ];
                $db->insert('student_documents', $selfieData);
            }
        }

        // Handle document uploads
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = getUploadDirectory('students');
            
            if (!is_writable($uploadDir)) {            
                chmod($uploadDir, 0755);
            }

            foreach ($_FILES['documents']['name'] as $key => $filename) {
                if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileType = $_POST['document_types'][$key] ?? 'other';
                    $fileSize = $_FILES['documents']['size'][$key];
                    $fileTmp = $_FILES['documents']['tmp_name'][$key];

                    if ($fileSize > MAX_FILE_SIZE) {
                        continue; 
                    }

                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    $newFilename = $studentDbId . '_' . $fileType . '_' . time() . '.' . $extension;
                    $filePath = $uploadDir . $newFilename;

                    if (move_uploaded_file($fileTmp, $filePath)) {
                        // Store relative path for cross-platform compatibility
                        $relativePath = 'uploads/students/' . $newFilename;
                        
                        $documentData = [
                            'student_id' => $studentDbId,
                            'document_type' => $fileType,
                            'file_name' => $filename,
                            'file_path' => $relativePath,
                            'file_size' => $fileSize
                        ];
                        $db->insert('student_documents', $documentData);
                    }
                }
            }
        }

        // Handle section assignment
        $sectionId = null;
        if (!empty($_POST['section_id'])) {
            // Use selected section
            $sectionId = $_POST['section_id'];
        } else {
            // Auto-assign to available section
            $courseId = $_POST['course_id'];
            $yearLevel = $_POST['year_level'];
            
            $availableSection = $db->fetchOne("
                SELECT id FROM class_sections 
                WHERE course_id = ? 
                AND year_level = ? 
                AND academic_year = ?
                AND is_active = 1 
                AND current_students < max_students
                ORDER BY current_students ASC
                LIMIT 1
            ", [$courseId, $yearLevel, $currentAcademicYear]);
            
            if ($availableSection) {
                $sectionId = $availableSection['id'];
            }
        }
        
        // Only create enrollment if we have a valid section
        if ($sectionId) {
            $enrollmentData = [
                'student_id' => $studentDbId,
                'section_id' => $sectionId,
                'academic_year' => $currentAcademicYear,
                'semester' => 1,
                'enrollment_type' => 'new',
                'enrollment_date' => date('Y-m-d'),
                'status' => 'pending'
            ];
            $db->insert('enrollments', $enrollmentData);
            
            // Update section student count
            $db->query("UPDATE class_sections SET current_students = current_students + 1 WHERE id = ?", [$sectionId]);
        } else {
            throw new Exception("No available sections found for the selected course and year level. Please contact the school administration.");
        }

        $success = "Registration successful! Your Student ID is: <strong>$studentId</strong><br>
                   Your enrollment is pending approval. You will be notified once approved.<br>
                   <small class='text-muted'>Your selfie photo has been captured and saved.</small>";

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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Student Registration - <?= APP_NAME ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .registration-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .section-title {
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        #successAlert {
            animation: none !important;
            transition: none !important;
        }
        #successAlert.fade {
            opacity: 1 !important;
        }
        .document-upload {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        .document-upload:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .camera-preview {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        .camera-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
        }
        .camera-controls {
            margin-top: 1rem;
        }
        .photo-preview {
            margin-top: 1rem;
        }
        #video, #canvas {
            border-radius: 8px;
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

    <div class="registration-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-user-plus me-3"></i>
                        Student Registration
                    </h1>
                    <p class="lead mb-0">
                        Join our school community! Fill out the form below to register for the <?= $currentAcademicYear ?> academic year.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-graduation-cap display-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="registrationForm">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-user me-2"></i>
                            Personal Information
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name" class="form-label required-field">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                       value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name" class="form-label required-field">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_of_birth" class="form-label required-field">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="<?= $_POST['date_of_birth'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label required-field">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                    <option value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                   placeholder="Enter your email address (optional)">
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Address Information
                        </h4>

                        <div class="mb-3">
                            <label for="address" class="form-label required-field">Complete Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label required-field">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label required-field">State/Province</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?= htmlspecialchars($_POST['state'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zip_code" class="form-label required-field">ZIP/Postal Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                       value="<?= htmlspecialchars($_POST['zip_code'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-phone me-2"></i>
                            Emergency Contact
                        </h4>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                                       value="<?= htmlspecialchars($_POST['emergency_contact_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" 
                                       value="<?= htmlspecialchars($_POST['emergency_contact_phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                                       value="<?= htmlspecialchars($_POST['emergency_contact_relationship'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Academic Information
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label required-field">Preferred Course</label>
                                <select class="form-select" id="course_id" name="course_id" required>
                                    <option value="">Loading courses...</option>
                                </select>
                                <div id="courseLoading" class="form-text">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Loading courses...
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="loadCourses()">
                                        <i class="fas fa-sync-alt me-1"></i> Refresh
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="year_level" class="form-label required-field">Year Level</label>
                                <select class="form-select" id="year_level" name="year_level" required>
                                    <option value="">Select Year Level</option>
                                    <option value="11" <?= ($_POST['year_level'] ?? '') == '11' ? 'selected' : '' ?>>Grade 11</option>
                                    <option value="12" <?= ($_POST['year_level'] ?? '') == '12' ? 'selected' : '' ?>>Grade 12</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="section_id" class="form-label">Preferred Section (Optional)</label>
                            <select class="form-select" id="section_id" name="section_id">
                                <option value="">Auto-assign</option>
                            </select>
                            <div class="form-text">If no section is selected, you will be assigned to an available section.</div>
                        </div>
                    </div>

                    <!-- Student Selfie -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-camera me-2"></i>
                            Student Photo (Selfie)
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="camera-container mb-3">
                                    <div id="cameraPreview" class="camera-preview">
                                        <video id="video" autoplay muted style="width: 100%; height: 300px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: none;"></video>
                                        <canvas id="canvas" style="width: 100%; height: 300px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: none;"></canvas>
                                        <div id="cameraPlaceholder" class="camera-placeholder">
                                            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Click "Start Camera" to take your photo</p>
                                        </div>
                                    </div>
                                    
                                    <div class="camera-controls text-center">
                                        <button type="button" id="startCamera" class="btn btn-primary me-2">
                                            <i class="fas fa-video me-2"></i>
                                            Start Camera
                                        </button>
                                        <button type="button" id="capturePhoto" class="btn btn-success me-2" style="display: none;">
                                            <i class="fas fa-camera me-2"></i>
                                            Capture Photo
                                        </button>
                                        <button type="button" id="retakePhoto" class="btn btn-warning" style="display: none;">
                                            <i class="fas fa-redo me-2"></i>
                                            Retake
                                        </button>
                                    </div>
                                    
                                    <input type="hidden" id="selfieData" name="selfie_data">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Photo Requirements:</h6>
                                    <ul class="mb-0">
                                        <li>Look directly at the camera</li>
                                        <li>Ensure good lighting</li>
                                        <li>Remove sunglasses or hats</li>
                                        <li>Keep a neutral expression</li>
                                        <li>Make sure your face is clearly visible</li>
                                    </ul>
                                </div>
                                
                                <div id="photoPreview" class="photo-preview" style="display: none;">
                                    <h6>Captured Photo:</h6>
                                    <img id="previewImage" src="" alt="Student Photo" style="width: 100%; max-height: 200px; border-radius: 8px; border: 2px solid #28a745;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Required Documents -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fas fa-file-upload me-2"></i>
                            Required Documents
                        </h4>

                        <div id="documentContainer">
                            <div class="document-upload mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Document Type</label>
                                        <select class="form-select" name="document_types[]">
                                            <option value="birth_certificate">Birth Certificate</option>
                                            <option value="report_card">Report Card</option>
                                            <option value="id_card">ID Card</option>
                                            <option value="medical_record">Medical Record</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Upload File</label>
                                        <input type="file" class="form-control" name="documents[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <div class="form-text">Max file size: 5MB. Allowed types: PDF, JPG, PNG, DOC</div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-danger btn-remove-field w-100">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary mb-3" id="addDocument">
                            <i class="fas fa-plus me-2"></i>
                            Add Another Document
                        </button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Submit Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    
    <script>
        // Add document functionality
        document.getElementById('addDocument').addEventListener('click', function() {
            const container = document.getElementById('documentContainer');
            const newDocument = document.querySelector('.document-upload').cloneNode(true);
            
            newDocument.querySelector('select').value = 'birth_certificate';
            newDocument.querySelector('input[type="file"]').value = '';
            
            container.appendChild(newDocument);
        });

        // Remove document functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-field')) {
                const documents = document.querySelectorAll('.document-upload');
                if (documents.length > 1) {
                    e.target.closest('.document-upload').remove();
                }
            }
        });

        // Load sections based on course and year level
        document.getElementById('course_id').addEventListener('change', loadSections);
        document.getElementById('year_level').addEventListener('change', loadSections);

        function loadSections() {
            const courseId = document.getElementById('course_id').value;
            const yearLevel = document.getElementById('year_level').value;
            const sectionSelect = document.getElementById('section_id');
            
            if (courseId && yearLevel) {
                fetch(`get_sections.php?course_id=${courseId}&year_level=${yearLevel}`)
                    .then(response => response.json())
                    .then(data => {
                        sectionSelect.innerHTML = '<option value="">Auto-assign</option>';
                        data.forEach(section => {
                            const option = document.createElement('option');
                            option.value = section.id;
                            option.textContent = `${section.section_name} (${section.current_students}/${section.max_students} students)`;
                            sectionSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading sections:', error));
            }
        }

        // Camera functionality
        let stream = null;
        let capturedPhoto = null;

        document.getElementById('startCamera').addEventListener('click', async function() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: 640, 
                        height: 480,
                        facingMode: 'user' // Front camera for selfie
                    } 
                });
                
                const video = document.getElementById('video');
                video.srcObject = stream;
                video.style.display = 'block';
                document.getElementById('cameraPlaceholder').style.display = 'none';
                document.getElementById('capturePhoto').style.display = 'inline-block';
                document.getElementById('startCamera').style.display = 'none';
                
            } catch (error) {
                console.error('Error accessing camera:', error);
                alert('Unable to access camera. Please ensure you have granted camera permissions and try again.');
            }
        });

        document.getElementById('capturePhoto').addEventListener('click', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');
            
            // Set canvas dimensions to match video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw the current video frame to canvas
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convert to base64
            capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById('selfieData').value = capturedPhoto;
            
            // Show preview
            document.getElementById('previewImage').src = capturedPhoto;
            document.getElementById('photoPreview').style.display = 'block';
            
            // Hide video, show canvas
            video.style.display = 'none';
            canvas.style.display = 'block';
            
            // Update button states
            document.getElementById('capturePhoto').style.display = 'none';
            document.getElementById('retakePhoto').style.display = 'inline-block';
            
            // Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        document.getElementById('retakePhoto').addEventListener('click', function() {
            // Reset everything
            document.getElementById('cameraPlaceholder').style.display = 'block';
            document.getElementById('video').style.display = 'none';
            document.getElementById('canvas').style.display = 'none';
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('startCamera').style.display = 'inline-block';
            document.getElementById('capturePhoto').style.display = 'none';
            document.getElementById('retakePhoto').style.display = 'none';
            document.getElementById('selfieData').value = '';
            capturedPhoto = null;
        });

        
        
        

        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            // Check required fields
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            
            // Check if selfie is captured
            if (!capturedPhoto) {
                alert('Please take a selfie photo to complete your registration.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                if (!capturedPhoto) {
                    alert('Please take a selfie photo to complete your registration.');
                } else {
                    alert('Please fill in all required fields.');
                }
            }
        });

        // Load courses dynamically
        function loadCourses() {
            const courseSelect = document.getElementById('course_id');
            const courseLoading = document.getElementById('courseLoading');
            
            fetch('api/get_courses.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear existing options
                        courseSelect.innerHTML = '<option value="">Select Course</option>';
                        
                        if (data.courses.length > 0) {
                            // Add courses to dropdown
                            data.courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.id;
                                option.textContent = `${course.course_name} (${course.course_code})`;
                                courseSelect.appendChild(option);
                            });
                            
                            // Hide loading message
                            courseLoading.style.display = 'none';
                        } else {
                            // No courses available
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No courses available. Please contact administrator.';
                            option.disabled = true;
                            courseSelect.appendChild(option);
                            courseLoading.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> No courses available';
                        }
                    } else {
                        console.error('Error loading courses:', data.error);
                        courseLoading.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Error loading courses';
                    }
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                    courseLoading.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Error loading courses';
                });
        }
        
        // Refresh courses every 30 seconds to catch admin changes
        function startCourseRefresh() {
            setInterval(loadCourses, 30000); // Refresh every 30 seconds
        }
        
        // Prevent success alert from auto-hiding and check email verification
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                // Remove any auto-hide functionality
                successAlert.classList.remove('fade');
                
                // Make sure it stays visible until manually closed
                successAlert.style.display = 'block';
                
                // Add a custom close handler to ensure it only closes when X is clicked
                const closeButton = successAlert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', function() {
                        successAlert.style.display = 'none';
                    });
                }
            }
            
            // Load courses on page load
            loadCourses();
            
            // Start auto-refresh
            startCourseRefresh();
        });
    </script>
</body>
</html>
