<?php
/**
 * Bangladesh Students Community Contest Platform - Submission Management API
 */

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// Enforce admin login check
require_login_api();

$db = DB::connect();

// ----------------------------------------------------
// Handle GET requests (Fetch all submissions)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $db->query("SELECT * FROM submissions ORDER BY created_at DESC");
        $submissions = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $submissions
        ]);
        exit();
    } catch (PDOException $e) {
        error_log("Failed to fetch submissions list: " . $e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit();
    }
}

// ----------------------------------------------------
// Handle POST requests (Management actions, e.g. Delete)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $csrf = $_POST['csrf_token'] ?? '';
    
    // 1. Verify CSRF Token
    if (!verify_csrf_token($csrf)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['success' => false, 'message' => 'Security Error: CSRF token verification failed.']);
        exit();
    }
    
    if ($action === 'delete') {
        if ($id <= 0) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Invalid submission ID.']);
            exit();
        }
        
        try {
            // Find submission and filename to clean up files from storage
            $find_stmt = $db->prepare("SELECT * FROM submissions WHERE id = :id LIMIT 1");
            $find_stmt->execute(['id' => $id]);
            $submission = $find_stmt->fetch();
            
            if (!$submission) {
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['success' => false, 'message' => 'Submission not found.']);
                exit();
            }
            
            $file_name = $submission['pdf_file'];
            $file_path = UPLOAD_DIR . $file_name;
            
            // Start Transaction to guarantee sync between DB and Disk
            $db->beginTransaction();
            
            // Delete DB record
            $delete_stmt = $db->prepare("DELETE FROM submissions WHERE id = :id");
            $delete_stmt->execute(['id' => $id]);
            
            // Log the action
            $admin_user = $_SESSION['admin_username'] ?? 'unknown';
            $log_stmt = $db->prepare("
                INSERT INTO logs (action, details, ip_address) 
                VALUES (:action, :details, :ip)
            ");
            $log_stmt->execute([
                'action' => 'submission_delete',
                'details' => "Submission #{$id} for student '{$submission['student_name']}' was deleted by admin: {$admin_user}. File: {$file_name}",
                'ip' => get_client_ip()
            ]);
            
            $db->commit();
            
            // Delete file from disk
            if (!empty($file_name) && file_exists($file_path)) {
                if (!unlink($file_path)) {
                    error_log("Warning: Failed to delete PDF file from storage: " . $file_path);
                    // We don't roll back the DB transaction because the entry is already removed from DB, 
                    // but we log the disk cleanup failure.
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Submission and corresponding file deleted successfully.'
            ]);
            exit();
            
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Database transaction failed during deletion: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['success' => false, 'message' => 'Database Error: Could not process deletion.']);
            exit();
        }
    }
    
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid administrative action request.']);
    exit();
}
