<?php
/**
 * Bangladesh Students Community Contest Platform - Submission API Endpoint
 */

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Enforce AJAX / Fetch request check (optional but recommended for API security)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Only POST requests are permitted.']);
    exit();
}

// 1. Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Security Error: CSRF token verification failed.']);
    exit();
}

// 2. Check if contest is active and deadline is not exceeded
$is_active = (int)get_setting('is_contest_active', '1');
$contest_deadline = get_setting('contest_deadline', '2026-06-30 23:59:59');
if (!$is_active || (time() > strtotime($contest_deadline))) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Submissions are currently closed.']);
    exit();
}

// 3. Enforce Rate Limiting (session-based)
// Commented out to allow multiple submissions during testing
/*
if (is_rate_limited()) {
    header('HTTP/1.1 429 Too Many Requests');
    echo json_encode([
        'success' => false, 
        'message' => 'You have already submitted an entry. Only one submission is allowed per session.'
    ]);
    exit();
}
*/

// 4. Validate and Sanitize Form Inputs
$student_name = isset($_POST['student_name']) ? trim($_POST['student_name']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';

// Validation checks
if (empty($student_name) || empty($department) || empty($semester)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'All fields (Name, Department, Semester) are required.']);
    exit();
}

if (strlen($student_name) > 100 || strlen($department) > 100) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Input exceeds maximum length limits.']);
    exit();
}

// Validate semester is exactly in range
$valid_semesters = [];
for ($i = 1; $i <= 8; $i++) {
    $valid_semesters[] = "Semester " . $i;
}
if (!in_array($semester, $valid_semesters)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid semester value.']);
    exit();
}

// 5. Check and Validate File Upload
if (!isset($_FILES['pdf_file'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'PDF file is required.']);
    exit();
}

$file_validation = validate_pdf_upload($_FILES['pdf_file']);
if (!$file_validation['success']) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => $file_validation['message']]);
    exit();
}

$secure_filename = $file_validation['filename'];
$upload_path = UPLOAD_DIR . $secure_filename;

// Ensure upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'message' => 'Server Error: Failed to initialize storage folder.']);
        exit();
    }
}

// 6. Move file to secure directory
if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $upload_path)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file. Please check folder permissions.']);
    exit();
}

// 7. Save to Database using PDO Prepared Statement (SQL Injection Protection)
$db = DB::connect();

try {
    // Start Transaction for logging integrity
    $db->beginTransaction();
    
    $stmt = $db->prepare("
        INSERT INTO submissions (student_name, department, semester, pdf_file) 
        VALUES (:name, :dept, :semester, :file)
    ");
    $stmt->execute([
        'name' => $student_name,
        'dept' => $department,
        'semester' => $semester,
        'file' => $secure_filename
    ]);
    
    // Get last insert ID
    $submission_id = $db->lastInsertId();
    
    // Log the successful upload in audit logs
    $log_stmt = $db->prepare("
        INSERT INTO logs (action, details, ip_address) 
        VALUES (:action, :details, :ip)
    ");
    $log_stmt->execute([
        'action' => 'submission_upload',
        'details' => "Submission #{$submission_id} uploaded by student: {$student_name} ({$department}, {$semester}). File: {$secure_filename}",
        'ip' => get_client_ip()
    ]);
    
    // Mark this session as having submitted to prevent duplicates
    $_SESSION['has_submitted'] = true;
    
    $db->commit();
    
    // Return Success
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your contest entry has been successfully submitted.',
        'submission_id' => $submission_id
    ]);
    
} catch (PDOException $e) {
    $db->rollBack();
    
    // Delete file from disk if DB fails to ensure sync
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    error_log("Database transaction failed during upload: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database Error: Could not log your submission details.']);
}
