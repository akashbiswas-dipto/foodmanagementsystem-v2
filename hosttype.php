<?php
declare(strict_types=1);

if (session_status() == PHP_SESSION_NONE) session_start();

// Determine protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443
    ? 'https://' : 'http://';

$server_name = $_SERVER['HTTP_HOST'];

// Detect local or EC2 based on folder name or hostname
$scriptPath = str_replace('\\', '/', __DIR__);

if ($server_name === 'localhost' || strpos($scriptPath, 'foodmanagementsystem') !== false) {
    // Local XAMPP/MAMP
    define('BASE_PATH', __DIR__ . '/');
    define('BASE_URL', $protocol . 'localhost/foodmanagementsystem/');
} else {
    // Live EC2 server
    define('BASE_PATH', __DIR__ . '/');
    define('BASE_URL', $protocol . $server_name . '/foodmanagementsystem-v2/foodmanagementsystem/');
}
