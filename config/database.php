<?php

function env_value($key, $default = null) {
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : $default;
}

/*
|--------------------------------------------------------------------------
| DATABASE CONFIG (Railway)
|--------------------------------------------------------------------------
*/

define('DB_HOST', env_value('MYSQLHOST'));
define('DB_PORT', (int) env_value('MYSQLPORT', 3306));
define('DB_USER', env_value('MYSQLUSER'));
define('DB_PASS', env_value('MYSQLPASSWORD'));
define('DB_NAME', env_value('MYSQLDATABASE'));

/*
|--------------------------------------------------------------------------
| DEBUG (temporary - remove later)
|--------------------------------------------------------------------------
*/
// echo "HOST: " . DB_HOST . "<br>";
// echo "PORT: " . DB_PORT . "<br>";
// echo "USER: " . DB_USER . "<br>";

/*
|--------------------------------------------------------------------------
| CONNECT DATABASE
|--------------------------------------------------------------------------
*/

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/*
|--------------------------------------------------------------------------
| BASE URL CONFIG
|--------------------------------------------------------------------------
*/

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