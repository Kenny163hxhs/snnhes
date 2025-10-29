<?php
/**
 * Test Permanent Student Deletion
 * Creates a test student and then permanently deletes them to verify complete removal
 */

require_once 'config/database.php';

$db = Database::getInstance();
$success = '';
$error = '';

echo "<h1>🧪 Testing Permanent Student Deletion</h1>";
echo "==========================================\n\n";

try {
    // Step 1: Create a test student
    echo "1. Creating test student...\n";
    $testStudentId = $db->insert('students', [
        'student_id' => 'TEST-' . time(),
        'first_name' => 'Test',
        'last_name' => 'Student',
        'middle_name' => 'Permanent',
        'email' => 'test.permanent@example.com',
        'phone' => '1234567890',
        'date_of_birth' => '2000-01-01',
        'gender' => 'male',
        'address' => 'Test Address',
        'city' => 'Test City',
        'state' => 'Test State',
        'zip_code' => '12345',
        'emergency_contact_name' => 'Test Guardian',
        'emergency_contact_phone' => '0987654321',
        'emergency_contact_relationship' => 'Parent',
        'status' => 'active'
    ]);
    echo "   ✅ Test student created with ID: {$testStudentId}\n";
    
    // Step 2: Create test documents
    echo "\n2. Creating test documents...\n";
    $testDocId = $db->insert('student_documents', [
        'student_id' => $testStudentId,
        'document_type' => 'birth_certificate',
        'file_path' => 'uploads/students/test_birth_cert.jpg',
        'uploaded_at' => date('Y-m-d H:i:s')
    ]);
    echo "   ✅ Test document created with ID: {$testDocId}\n";
    
    // Step 3: Create test enrollment
    echo "\n3. Creating test enrollment...\n";
    $testEnrollmentId = $db->insert('enrollments', [
        'student_id' => $testStudentId,
        'section_id' => 1, // Assuming section 1 exists
        'academic_year' => date('Y') . '-' . (date('Y') + 1),
        'status' => 'pending',
        'enrolled_at' => date('Y-m-d H:i:s')
    ]);
    echo "   ✅ Test enrollment created with ID: {$testEnrollmentId}\n";
    
    // Step 4: Create test email verification
    echo "\n4. Creating test email verification...\n";
    $testEmailId = $db->insert('email_verifications', [
        'email' => 'test.permanent@example.com',
        'otp_code' => '123456',
        'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        'is_verified' => 0
    ]);
    echo "   ✅ Test email verification created with ID: {$testEmailId}\n";
    
    // Step 5: Create test files
    echo "\n5. Creating test files...\n";
    if (!is_dir('uploads/students')) {
        mkdir('uploads/students', 0777, true);
    }
    file_put_contents('uploads/students/test_birth_cert.jpg', 'fake image data');
    echo "   ✅ Test file created: uploads/students/test_birth_cert.jpg\n";
    
    // Step 6: Verify all data exists
    echo "\n6. Verifying test data exists...\n";
    $student = $db->fetchOne("SELECT * FROM students WHERE id = ?", [$testStudentId]);
    $documents = $db->fetchAll("SELECT * FROM student_documents WHERE student_id = ?", [$testStudentId]);
    $enrollments = $db->fetchAll("SELECT * FROM enrollments WHERE student_id = ?", [$testStudentId]);
    $emailVerifications = $db->fetchAll("SELECT * FROM email_verifications WHERE email = ?", ['test.permanent@example.com']);
    
    echo "   ✅ Student record: " . ($student ? 'exists' : 'missing') . "\n";
    echo "   ✅ Documents: " . count($documents) . " records\n";
    echo "   ✅ Enrollments: " . count($enrollments) . " records\n";
    echo "   ✅ Email verifications: " . count($emailVerifications) . " records\n";
    echo "   ✅ Test file: " . (file_exists('uploads/students/test_birth_cert.jpg') ? 'exists' : 'missing') . "\n";
    
    // Step 7: Perform permanent deletion
    echo "\n7. Performing permanent deletion...\n";
    
    // Delete all uploaded files
    $documents = $db->fetchAll("SELECT * FROM student_documents WHERE student_id = ?", [$testStudentId]);
    foreach ($documents as $document) {
        $filePath = $document['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
            echo "   ✅ Deleted file: {$filePath}\n";
        }
    }
    
    // Delete from all related tables
    $db->query("DELETE FROM student_documents WHERE student_id = ?", [$testStudentId]);
    $db->query("DELETE FROM enrollments WHERE student_id = ?", [$testStudentId]);
    $db->query("DELETE FROM student_transfers WHERE student_id = ?", [$testStudentId]);
    $db->query("DELETE FROM email_verifications WHERE email = ?", ['test.permanent@example.com']);
    $db->query("DELETE FROM academic_records WHERE student_id = ?", [$testStudentId]);
    $db->query("DELETE FROM enrollment_documents WHERE student_id = ?", [$testStudentId]);
    
    // Finally delete the student
    $db->delete('students', 'id = ?', [$testStudentId]);
    echo "   ✅ Student record deleted\n";
    
    // Step 8: Verify complete deletion
    echo "\n8. Verifying complete deletion...\n";
    $remainingStudent = $db->fetchOne("SELECT * FROM students WHERE id = ?", [$testStudentId]);
    $remainingDocs = $db->fetchAll("SELECT * FROM student_documents WHERE student_id = ?", [$testStudentId]);
    $remainingEnrollments = $db->fetchAll("SELECT * FROM enrollments WHERE student_id = ?", [$testStudentId]);
    $remainingEmails = $db->fetchAll("SELECT * FROM email_verifications WHERE email = ?", ['test.permanent@example.com']);
    $remainingAcademic = $db->fetchAll("SELECT * FROM academic_records WHERE student_id = ?", [$testStudentId]);
    $remainingEnrollmentDocs = $db->fetchAll("SELECT * FROM enrollment_documents WHERE student_id = ?", [$testStudentId]);
    $fileExists = file_exists('uploads/students/test_birth_cert.jpg');
    
    echo "   Student record: " . ($remainingStudent ? '❌ STILL EXISTS' : '✅ DELETED') . "\n";
    echo "   Documents: " . (count($remainingDocs) > 0 ? '❌ ' . count($remainingDocs) . ' REMAIN' : '✅ DELETED') . "\n";
    echo "   Enrollments: " . (count($remainingEnrollments) > 0 ? '❌ ' . count($remainingEnrollments) . ' REMAIN' : '✅ DELETED') . "\n";
    echo "   Email verifications: " . (count($remainingEmails) > 0 ? '❌ ' . count($remainingEmails) . ' REMAIN' : '✅ DELETED') . "\n";
    echo "   Academic records: " . (count($remainingAcademic) > 0 ? '❌ ' . count($remainingAcademic) . ' REMAIN' : '✅ DELETED') . "\n";
    echo "   Enrollment documents: " . (count($remainingEnrollmentDocs) > 0 ? '❌ ' . count($remainingEnrollmentDocs) . ' REMAIN' : '✅ DELETED') . "\n";
    echo "   Test file: " . ($fileExists ? '❌ STILL EXISTS' : '✅ DELETED') . "\n";
    
    $totalRemaining = ($remainingStudent ? 1 : 0) + count($remainingDocs) + count($remainingEnrollments) + count($remainingEmails) + count($remainingAcademic) + count($remainingEnrollmentDocs) + ($fileExists ? 1 : 0);
    
    if ($totalRemaining == 0) {
        echo "\n🎉 PERMANENT DELETION TEST PASSED!\n";
        echo "✅ All traces of test student have been completely removed\n";
        echo "✅ No records remain in any table\n";
        echo "✅ All test files deleted\n";
        echo "✅ Student ID {$testStudentId} is completely gone\n\n";
        
        $success = "Permanent deletion test passed! All traces completely removed.";
    } else {
        echo "\n❌ PERMANENT DELETION TEST FAILED!\n";
        echo "⚠️ {$totalRemaining} traces still remain\n";
        echo "❌ Deletion was not complete\n\n";
        
        $error = "Permanent deletion test failed! Some traces remain.";
    }
    
} catch (Exception $e) {
    $error = "Test failed: " . $e->getMessage();
    echo "\n❌ ERROR: " . $error . "\n";
}

echo "\n<p><a href='modules/students/list.php'>Go to Student List</a></p>";
echo "<p><a href='cleanup_orphaned_data.php'>Run Cleanup Script</a></p>";
echo "<p><a href='index.php'>Go to Dashboard</a></p>";
?>
