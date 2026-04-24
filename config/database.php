<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'SoH@1234');
define('DB_NAME', 'event_management');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Base URL — auto-detect install folder, or leave blank for root
// Get the base directory only, not including subdirectories
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
// Remove /admin, /user, or /pdf subdirectories from the path
$basePath = preg_replace('/(\/admin|\/user|\/pdf|\/includes|\/config)$/', '', $scriptDir);
$basePath = rtrim($basePath, '/');
if ($basePath === '' || $basePath === '/') {
    $basePath = '';
}
define('BASE_URL', $basePath);



