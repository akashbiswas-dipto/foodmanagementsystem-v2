<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Paths
define('BASE_PATH', realpath(__DIR__) . '/');
$base_url = "http://13.210.70.74/foodmanagementsystem/";

// Composer
require_once BASE_PATH . 'vendor/autoload.php';
use MongoDB\Client;

// MongoDB Atlas Singleton
class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        $uri = "mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/foodmanagement?retryWrites=true&w=majority";
        try {
            $client = new Client($uri);
            $this->db = $client->foodmanagement;
        } catch (Exception $e) {
            die("MongoDB Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new Database();
        return self::$instance;
    }

    public function getDB() { return $this->db; }
}

// App Config
class AppConfig {
    private static $instance = null;
    public $settings;

    private function __construct() {
        $this->settings = ['app_name' => 'FoodManagementSystem'];
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new AppConfig();
        return self::$instance;
    }
}

$db = Database::getInstance()->getDB();
$config = AppConfig::getInstance();
