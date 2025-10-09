<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Define base path only once
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/');
}

// Load Composer autoloader
require_once BASE_PATH . 'vendor/autoload.php';

// Load environment variables from .env if available
if (file_exists(BASE_PATH . '.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class Database {
    private static ?Database $instance = null;
    private $db;

    private function __construct() {
        // Get MongoDB URI from environment variable or fallback
        $uri = $_ENV['MONGO_URI'] ?? getenv('MONGO_URI') ?? 
               'mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/foodmanagement?retryWrites=true&w=majority';

        try {
            $client = new Client($uri);
            $this->db = $client->foodmanagement; // Database name
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            die("MongoDB Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getDB() {
        return $this->db;
    }
}

$db = Database::getInstance()->getDB();

class AppConfig {
    private static ?AppConfig $instance = null;
    public array $settings;

    private function __construct() {
        $this->settings = [
            'app_name' => 'FoodManagementSystem',
            'mongodb_uri' => $_ENV['MONGO_URI'] ?? getenv('MONGO_URI') ?? 
                             'mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/foodmanagement',
            'jwt_secret' => $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? 'default_secret',
            'port' => $_ENV['PORT'] ?? getenv('PORT') ?? '5001'
        ];
    }

    public static function getInstance(): AppConfig {
        if (!self::$instance) {
            self::$instance = new AppConfig();
        }
        return self::$instance;
    }
}

$config = AppConfig::getInstance();
