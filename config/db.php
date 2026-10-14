<?php
/**
 * Bangladesh Students Community Contest Platform - Database Connection
 */

require_once __DIR__ . '/config.php';

class DB {
    private static $pdo = null;

    /**
     * Get the active database connection instance
     * @return PDO
     */
    public static function connect() {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // Enforce true database-level prepared statements
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ];

                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log the exception locally or write to error log
                error_log("Database connection failure: " . $e->getMessage());

                // Graceful error display without exposing system internals
                header('HTTP/1.1 500 Internal Server Error');
                
                if (ENVIRONMENT === 'development') {
                    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
                } else {
                    die("System Error: We are currently experiencing technical difficulties. Please try again later.");
                }
            }
        }
        return self::$pdo;
    }
}
