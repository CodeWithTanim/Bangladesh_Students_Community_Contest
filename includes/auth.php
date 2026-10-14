<?php
/**
 * Bangladesh Students Community Contest Platform - Authentication Guard
 */

require_once dirname(__DIR__) . '/config/config.php';

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
}

/**
 * Determine if current user is logged in as admin
 * @return bool
 */
function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    
    // Session Timeout Check
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS)) {
        // Session expired
        session_unset();
        session_destroy();
        return false;
    }
    
    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Guard HTML pages: redirect to login if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        // Clear potential expired session data
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        header("Location: /admin/login.php");
        exit();
    }
}

/**
 * Guard API endpoints: return JSON error if not logged in
 */
function require_login_api() {
    if (!is_logged_in()) {
        header('Content-Type: application/json');
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized access. Session expired or login required.'
        ]);
        exit();
    }
}
