<?php
/**
 * Bangladesh Students Community Contest Platform - Common Functions
 */

require_once dirname(__DIR__) . '/config/db.php';

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
}

/**
 * Escape string for secure output in HTML (XSS Protection)
 */
function esc($str) {
    if ($str === null) return '';
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate cryptographically secure CSRF token and store in session
 */
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify if provided CSRF token matches the session token
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Securely fetch the client's IP address (handling proxies / Cloudflare)
 */
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($parts[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    // Validate IP
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    return '0.0.0.0';
}

/**
 * Check if the current session has already submitted an entry
 */
function is_rate_limited() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // One submission allowed per session
    return isset($_SESSION['has_submitted']) && $_SESSION['has_submitted'] === true;
}

/**
 * Write a system audit log to the database
 */
function log_action($action, $details = null) {
    $ip = get_client_ip();
    $db = DB::connect();
    try {
        $stmt = $db->prepare("
            INSERT INTO logs (action, details, ip_address) 
            VALUES (:action, :details, :ip)
        ");
        $stmt->execute([
            'action' => $action,
            'details' => $details,
            'ip' => $ip
        ]);
    } catch (PDOException $e) {
        error_log("Logging action failed: " . $e->getMessage());
    }
}

/**
 * Retrieve a setting value from the database
 */
function get_setting($key, $default = null) {
    $db = DB::connect();
    try {
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log("Get setting failed: " . $e->getMessage());
        return $default;
    }
}

/**
 * Update or insert a setting value in the database
 */
function set_setting($key, $value) {
    $db = DB::connect();
    try {
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value) 
            VALUES (:key, :value) 
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        return $stmt->execute([
            'key' => $key,
            'value' => $value
        ]);
    } catch (PDOException $e) {
        error_log("Set setting failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Comprehensive Validation of Uploaded PDF File
 * @param array $file The $_FILES['name'] array element
 * @return array Array with ['success' => bool, 'message' => string, 'filename' => string|null]
 */
function validate_pdf_upload($file) {
    // Check for upload errors
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload parameters.'];
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'message' => 'No file was uploaded.'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'message' => 'File size exceeds server upload limit.'];
        default:
            return ['success' => false, 'message' => 'An error occurred during file upload. Code: ' . $file['error']];
    }
    
    // Check filesize
    $max_size = (int)get_setting('max_upload_size', MAX_FILE_SIZE_BYTES);
    if ($file['size'] > $max_size) {
        $mb = round($max_size / (1024 * 1024), 1);
        return ['success' => false, 'message' => "File is too large. Maximum allowed size is {$mb}MB."];
    }
    
    // Check extension
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return ['success' => false, 'message' => 'Invalid file extension. Only PDF files are allowed.'];
    }
    
    // Check MIME type using finfo (Server-side analysis, not relying on user headers)
    if (!class_exists('finfo')) {
        // Fallback if finfo is not compiled in PHP (rare on modern hosting)
        $mime = mime_content_type($file['tmp_name']);
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
    }
    
    if ($mime !== 'application/pdf') {
        return ['success' => false, 'message' => 'Invalid file content. The file is not a valid PDF document.'];
    }
    
    // Extra guard: Check first bytes of file for PDF signature (%PDF)
    $handle = fopen($file['tmp_name'], 'r');
    if ($handle) {
        $header = fread($handle, 4);
        fclose($handle);
        if ($header !== '%PDF') {
            return ['success' => false, 'message' => 'Security Warning: Suspicious file content detected (Invalid PDF signature).'];
        }
    } else {
        return ['success' => false, 'message' => 'Could not inspect file contents.'];
    }
    
    // Generate secure randomized filename
    $secure_name = bin2hex(random_bytes(16)) . '_' . time() . '.pdf';
    
    return [
        'success' => true,
        'message' => 'File is valid.',
        'filename' => $secure_name
    ];
}
