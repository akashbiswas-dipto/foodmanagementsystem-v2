<?php
declare(strict_types=1);

if (session_status() == PHP_SESSION_NONE) session_start();

// Detect base folder dynamically
$scriptPath = str_replace('\\', '/', __DIR__); // handle Windows path
if (strpos($scriptPath, 'foodmanagementsystem-v2') !== false) {
    // EC2 server path
    define('BASE_PATH', __DIR__ . '/');
    define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') 
        . $_SERVER['HTTP_HOST'] . '/foodmanagementsystem-v2/foodmanagementsystem/');
} else {
    // Local XAMPP/MAMP path
    define('BASE_PATH', __DIR__ . '/');
    define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') 
        . $_SERVER['HTTP_HOST'] . '/foodmanagementsystem/');
}

// Load dependencies
require_once BASE_PATH . 'hosttype.php';
require_once BASE_PATH . 'vendor/autoload.php';

// MongoDB connection
use MongoDB\Client;

try {
    $mongoUri = 'mongodb+srv://<username>:<password>@<cluster>.mongodb.net/foodmanagement?retryWrites=true&w=majority';
    $client = new Client($mongoUri);
    $db = $client->foodmanagement;
} catch (\MongoDB\Driver\Exception\Exception $e) {
    die("MongoDB Connection Failed: " . $e->getMessage());
}
