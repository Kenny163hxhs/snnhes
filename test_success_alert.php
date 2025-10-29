<?php
/**
 * Test Success Alert Persistence
 * National High School Enrollment System
 */

$success = "Registration successful! Your Student ID is: <strong>STU2025TEST123</strong><br>
           Your enrollment is pending approval. You will be notified once approved.<br>
           <small class='text-muted'>Your selfie photo has been captured and saved.</small>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Success Alert - <?= APP_NAME ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        #successAlert {
            animation: none !important;
            transition: none !important;
        }
        #successAlert.fade {
            opacity: 1 !important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-test-tube me-2"></i>
                            Success Alert Persistence Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            This page tests whether the success alert stays visible until manually closed.
                        </p>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <h5>Test Instructions:</h5>
                            <ol>
                                <li>The green success alert above should stay visible</li>
                                <li>It should NOT disappear automatically</li>
                                <li>Only clicking the "X" button should close it</li>
                                <li>Wait 10+ seconds to confirm it doesn't auto-hide</li>
                            </ol>
                        </div>
                        
                        <div class="mt-4">
                            <a href="student_register.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Registration
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Prevent success alert from auto-hiding
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
        });
    </script>
</body>
</html>
