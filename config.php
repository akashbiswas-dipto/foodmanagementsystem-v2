<?php
if (session_status() == PHP_SESSION_NONE) session_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/');
}

require_once 'hosttype.php';
require_once BASE_PATH . 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class Database {
    private static ?Database $instance = null;
    private $db;

    private function __construct() {
        $uri = "mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/foodmanagement?retryWrites=true&w=majority";
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
            'mongodb_uri' => 'mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/foodmanagement'
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
