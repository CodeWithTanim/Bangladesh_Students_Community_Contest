<?php
/**
 * Bangladesh Students Community Contest Platform - Settings API Endpoint
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// Enforce admin login check
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // For exports/backups, verify session via regular check
    if (!is_logged_in()) {
        header("HTTP/1.1 401 Unauthorized");
        exit("Unauthorized access.");
    }
} else {
    // For modifying endpoints, enforce JSON api check
    require_login_api();
}

$db = DB::connect();

// ----------------------------------------------------
// GET REQUESTS: Data Export and SQL Backup
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $csrf = $_GET['csrf_token'] ?? '';
    
    // Verify CSRF
    if (!verify_csrf_token($csrf)) {
        header("HTTP/1.1 403 Forbidden");
        exit("Security Error: CSRF token validation failed.");
    }
    
    // Action 1: Export Data as CSV
    if ($action === 'export_csv') {
        try {
            $stmt = $db->query("SELECT id, student_name, department, semester, pdf_file, created_at FROM submissions ORDER BY id ASC");
            $submissions = $stmt->fetchAll();
            
            // Set download headers
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="bsc_submissions_' . date('Ymd_His') . '.csv"');
            
            // Output UTF-8 BOM for Excel Bangla character rendering
            echo "\xEF\xBB\xBF";
            
            $output = fopen('php://output', 'w');
            
            // Output Headers
            fputcsv($output, ['ID', 'Student Name', 'Department', 'Semester', 'PDF File Filename', 'Upload Date Time']);
            
            // Output rows
            foreach ($submissions as $row) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            log_action('export_csv', "Admin '" . $_SESSION['admin_username'] . "' exported submission records to CSV.");
            exit();
        } catch (PDOException $e) {
            error_log("CSV Export error: " . $e->getMessage());
            exit("Database error compiling export file.");
        }
    }
    
    // Action 2: Database Backup (Pure PHP SQL dump)
    if ($action === 'backup_db') {
        try {
            $tables = ['admins', 'submissions', 'settings', 'logs'];
            $sql_backup = "-- Bangladesh Students Community Contest Platform Backup\n";
            $sql_backup .= "-- Host: " . DB_HOST . "\n";
            $sql_backup .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql_backup .= "-- Database Name: " . DB_NAME . "\n";
            $sql_backup .= "--------------------------------------------------------\n\n";
            $sql_backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            foreach ($tables as $table) {
                // Table structure
                $sql_backup .= "-- Table structure for table `$table`\n";
                $sql_backup .= "DROP TABLE IF EXISTS `$table`;\n";
                $struct_stmt = $db->query("SHOW CREATE TABLE `$table`");
                $struct_row = $struct_stmt->fetch();
                $sql_backup .= $struct_row['Create Table'] . ";\n\n";
                
                // Table rows
                $data_stmt = $db->query("SELECT * FROM `$table`");
                $rows = $data_stmt->fetchAll(PDO::FETCH_NUM);
                
                if (count($rows) > 0) {
                    $sql_backup .= "-- Dumping data for table `$table`\n";
                    $sql_backup .= "INSERT INTO `$table` VALUES \n";
                    $insert_values = [];
                    foreach ($rows as $row) {
                        $escaped = array_map(function($val) use ($db) {
                            if ($val === null) return 'NULL';
                            return $db->quote($val);
                        }, $row);
                        $insert_values[] = "(" . implode(', ', $escaped) . ")";
                    }
                    $sql_backup .= implode(",\n", $insert_values) . ";\n\n";
                }
                $sql_backup .= "--------------------------------------------------------\n\n";
            }
            
            $sql_backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Set download headers
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="bsc_db_backup_' . date('Ymd_His') . '.sql"');
            header('Content-Length: ' . strlen($sql_backup));
            
            echo $sql_backup;
            log_action('backup_db', "Admin '" . $_SESSION['admin_username'] . "' downloaded a database backup SQL file.");
            exit();
        } catch (PDOException $e) {
            error_log("Database backup compilation error: " . $e->getMessage());
            exit("Error generating SQL backup dump.");
        }
    }
    
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid request action.");
}

// ----------------------------------------------------
// POST REQUESTS: Modify configuration or reset
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF
    if (!verify_csrf_token($csrf)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['success' => false, 'message' => 'Security Error: CSRF token validation failed.']);
        exit();
    }
    
    // Action 3: Update Contest Settings
    if ($action === 'update_contest') {
        $title = trim($_POST['contest_title'] ?? '');
        $topic = trim($_POST['contest_topic'] ?? '');
        $deadline = trim($_POST['contest_deadline'] ?? '');
        $is_active = isset($_POST['is_contest_active']) ? '1' : '0';
        $max_size = (int)($_POST['max_upload_size'] ?? 10) * 1024 * 1024; // MB to bytes
        
        if (empty($title) || empty($topic) || empty($deadline)) {
            echo json_encode(['success' => false, 'message' => 'Contest Title, Topic, and Deadline are required.']);
            exit();
        }
        
        // Basic deadline timestamp validation
        if (!strtotime($deadline)) {
            echo json_encode(['success' => false, 'message' => 'Invalid date-time format for deadline.']);
            exit();
        }
        
        try {
            set_setting('contest_title', $title);
            set_setting('contest_topic', $topic);
            set_setting('contest_deadline', date('Y-m-d H:i:s', strtotime($deadline)));
            set_setting('is_contest_active', $is_active);
            set_setting('max_upload_size', $max_size);
            
            log_action('setting_update', "Contest configuration updated by admin: " . $_SESSION['admin_username']);
            
            echo json_encode(['success' => true, 'message' => 'Contest parameters updated successfully.']);
            exit();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'System error saving settings.']);
            exit();
        }
    }
    
    // Action 4: Change Credentials
    if ($action === 'change_credentials') {
        $new_user = trim($_POST['new_username'] ?? '');
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        
        if (empty($new_user) || empty($current_pass) || empty($new_pass)) {
            echo json_encode(['success' => false, 'message' => 'All credential fields are required.']);
            exit();
        }
        
        if (strlen($new_pass) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
            exit();
        }
        
        try {
            // Verify current password first
            $admin_id = $_SESSION['admin_id'];
            $stmt = $db->prepare("SELECT * FROM admins WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $admin_id]);
            $admin = $stmt->fetch();
            
            if (!$admin || !password_verify($current_pass, $admin['password'])) {
                echo json_encode(['success' => false, 'message' => 'Incorrect current password. Verification failed.']);
                exit();
            }
            
            // Hash new password and update
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_stmt = $db->prepare("UPDATE admins SET username = :username, password = :password WHERE id = :id");
            $update_stmt->execute([
                'username' => $new_user,
                'password' => $new_hash,
                'id' => $admin_id
            ]);
            
            // Update current active username in session
            $_SESSION['admin_username'] = $new_user;
            
            log_action('credential_change', "Admin credentials updated. New username: {$new_user}");
            
            echo json_encode(['success' => true, 'message' => 'Admin credentials changed successfully.']);
            exit();
        } catch (PDOException $e) {
            error_log("Credentials update failure: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database Error: Could not change credentials.']);
            exit();
        }
    }
    
    // Action 5: Import Data (JSON)
    if ($action === 'import_json') {
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'JSON file upload is required.']);
            exit();
        }
        
        $file_ext = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));
        if ($file_ext !== 'json') {
            echo json_encode(['success' => false, 'message' => 'Only JSON format files are supported for import.']);
            exit();
        }
        
        $json_data = file_get_contents($_FILES['import_file']['tmp_name']);
        $decoded = json_decode($json_data, true);
        
        if ($decoded === null || !isset($decoded['submissions']) || !is_array($decoded['submissions'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON structure. Root object must contain "submissions" array.']);
            exit();
        }
        
        try {
            $inserted = 0;
            $skipped = 0;
            
            $db->beginTransaction();
            
            $stmt = $db->prepare("
                INSERT INTO submissions (student_name, department, semester, pdf_file, created_at) 
                VALUES (:name, :dept, :semester, :file, :created)
            ");
            
            foreach ($decoded['submissions'] as $sub) {
                // Validate item
                if (empty($sub['student_name']) || empty($sub['department']) || empty($sub['semester']) || empty($sub['pdf_file'])) {
                    $skipped++;
                    continue;
                }
                
                $created_at = $sub['created_at'] ?? date('Y-m-d H:i:s');
                
                $stmt->execute([
                    'name' => trim($sub['student_name']),
                    'dept' => trim($sub['department']),
                    'semester' => trim($sub['semester']),
                    'file' => trim($sub['pdf_file']),
                    'created' => $created_at
                ]);
                $inserted++;
            }
            
            $db->commit();
            log_action('import_json', "Admin '" . $_SESSION['admin_username'] . "' imported {$inserted} submissions via JSON. Skipped: {$skipped}");
            
            echo json_encode([
                'success' => true, 
                'message' => "Import complete. Successfully inserted {$inserted} submissions (skipped: {$skipped})."
            ]);
            exit();
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Import failure: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database Error: Import aborted due to SQL constraints.']);
            exit();
        }
    }
    
    // Action 6: Clear All Submissions and Purge Disk Files (Dangerous)
    if ($action === 'clear_data') {
        $confirm = $_POST['confirm_delete_all'] ?? '';
        if ($confirm !== 'DELETE_ALL_SUBMISSIONS') {
            echo json_encode(['success' => false, 'message' => 'Confirmation mismatch: Enter the exact phrase to proceed.']);
            exit();
        }
        
        try {
            // Retrieve list of files to clean disk
            $stmt = $db->query("SELECT pdf_file FROM submissions");
            $files = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Truncate Table (implicitly commits, so no active transaction wrapper)
            $db->exec("TRUNCATE TABLE submissions");
            
            // Log Action
            $admin_user = $_SESSION['admin_username'];
            $log_stmt = $db->prepare("
                INSERT INTO logs (action, details, ip_address) 
                VALUES (:action, :details, :ip)
            ");
            $log_stmt->execute([
                'action' => 'purge_all',
                'details' => "Administrative reset: All submission records purged from database by: {$admin_user}",
                'ip' => get_client_ip()
            ]);
            
            // Delete disk files
            $deleted_files_count = 0;
            foreach ($files as $file) {
                if (!empty($file)) {
                    $file_path = UPLOAD_DIR . $file;
                    if (file_exists($file_path)) {
                        if (unlink($file_path)) {
                            $deleted_files_count++;
                        }
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Purge complete. Successfully truncated submissions database table and deleted {$deleted_files_count} files from disk."
            ]);
            exit();
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Reset database purge failed: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database Error: Could not clear submissions table.']);
            exit();
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'Settings action endpoint not matching requirements.']);
    exit();
}
