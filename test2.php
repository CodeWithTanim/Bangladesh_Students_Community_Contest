<?php $_SERVER['REQUEST_METHOD'] = 'GET'; session_start(); $_SESSION['admin_logged_in'] = true; $_SESSION['last_activity'] = time(); require 'api/submissions.php'; ?>
