<?php
// Application configuration
define('APP_NAME', 'Kasr Al Ainy Hospital');
define('APP_URL', '/OOP_PROJECT/hospital-management-system');
define('APP_PATH', dirname(dirname(__FILE__)));
define('TIMEZONE', 'Africa/Cairo');

// Database configuration (simplified)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hospital_management');
define('DB_CHARSET', 'utf8mb4');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set(TIMEZONE);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database class
require_once __DIR__ . '/../core/Database.php';
?>