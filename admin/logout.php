<?php
/**
 * Bangladesh Students Community Contest Platform - Admin Logout
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log audit action if the admin was logged in
if (isset($_SESSION['admin_username'])) {
    log_action('logout', "Admin '" . $_SESSION['admin_username'] . "' logged out manually.");
}

// Clear and destroy session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to login page
header("Location: " . BASE_URL . "/admin/login.php");
exit();
