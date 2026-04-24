<?php

function env_value($key, $default = null) {
    $value = getenv($key);
    return $value !== false && $value !== '' ? $value : $default;
}

function parse_database_config() {
    $config = array(
        'host' => env_value('DB_HOST', '127.0.0.1'),
        'port' => (int) env_value('DB_PORT', '3306'),
        'user' => env_value('DB_USER', 'root'),
        'pass' => env_value('DB_PASS', 'SoH@1234'),
        'name' => env_value('DB_NAME', 'event_management'),
    );

    $databaseUrl = env_value('DATABASE_URL');
    if (!$databaseUrl) {
        return $config;
    }

    $parts = parse_url($databaseUrl);
    if ($parts === false) {
        return $config;
    }

    if (!empty($parts['host'])) {
        $config['host'] = $parts['host'];
    }
    if (!empty($parts['port'])) {
        $config['port'] = (int) $parts['port'];
    }
    if (!empty($parts['user'])) {
        $config['user'] = $parts['user'];
    }
    if (array_key_exists('pass', $parts)) {
        $config['pass'] = $parts['pass'];
    }
    if (!empty($parts['path'])) {
        $config['name'] = ltrim($parts['path'], '/');
    }

    return $config;
}

$db = parse_database_config();

// define('DB_HOST', $db['host']);
// define('DB_PORT', $db['port']);
// define('DB_USER', $db['user']);
// define('DB_PASS', $db['pass']);
// define('DB_NAME', $db['name']);

// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
// if ($conn->connect_error) {
//     die("Database connection failed: " . $conn->connect_error);
// }
// $conn->set_charset("utf8mb4");

define('DB_HOST', getenv('MYSQLHOST'));
define('DB_PORT', getenv('MYSQLPORT'));
define('DB_USER', getenv('MYSQLUSER'));
define('DB_PASS', getenv('MYSQLPASSWORD'));
define('DB_NAME', getenv('MYSQLDATABASE'));

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$configuredBaseUrl = env_value('APP_BASE_URL');
if ($configuredBaseUrl !== null) {
    define('BASE_URL', rtrim($configuredBaseUrl, '/'));
} else {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    $basePath = preg_replace('/(\/admin|\/user|\/pdf|\/includes|\/config)$/', '', $scriptDir);
    $basePath = rtrim($basePath, '/');
    if ($basePath === '' || $basePath === '/') {
        $basePath = '';
    }
    define('BASE_URL', $basePath);
}


