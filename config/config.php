<?php
/**
 * Bangladesh Students Community Contest Platform - Configuration
 */

// Prevent direct access to config files
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
}

// ----------------------------------------------------
// Database Credentials
// ----------------------------------------------------
// Change these settings when deploying to Hostinger
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'bsc_contest');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ----------------------------------------------------
// Dynamic Base URL Calculation for Portability
// ----------------------------------------------------
// Automatically detects if the website is in a subfolder or root
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dir_path = str_replace('\\', '/', dirname(__DIR__));
$base_url = str_replace($doc_root, '', $dir_path);
// Ensure it starts with / and does not end with /
$base_url = '/' . ltrim(rtrim($base_url, '/'), '/');
if ($base_url === '/') {
    $base_url = '';
}
define('BASE_URL', $base_url);

// ----------------------------------------------------
// Session Settings
// ----------------------------------------------------
// Enforce strong session security practices
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_use_only_cookies', 1);
    
    // Set cookie secure flag if HTTPS is active
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    // SameSite session cookie protection
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
}

// ----------------------------------------------------
// Security & Rate Limiting Configurations
// ----------------------------------------------------
// Submission limits per IP to prevent spam/denial of service
define('UPLOAD_LIMIT_PER_HOUR', 3); // Max submissions allowed per IP per hour
define('SESSION_TIMEOUT_SECONDS', 1800); // 30 minutes admin session idle timeout

// Upload directory configuration
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('MAX_FILE_SIZE_BYTES', 104857600); // 100MB limit

// Error Reporting (Turn off in production / Keep on for development)
define('ENVIRONMENT', 'development'); // 'development' or 'production'

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
