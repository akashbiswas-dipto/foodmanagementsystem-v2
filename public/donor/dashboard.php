<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require_once '../../hosttype.php';
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'patterns/decorator.php';
require_once BASE_PATH . 'patterns/factory.php';
require_once BASE_PATH . 'src/controller/TaskController.php';

use MongoDB\BSON\ObjectId;

// ------------------------
// Helper function to escape all output
// ------------------------
function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// ------------------------
// Check access
// ------------------------
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: " . BASE_URL . "public/login.php");
    exit();
}

// ------------------------
// Initialize TaskController
// ------------------------
$taskController = new TaskController($db->food);

// ------------------------
// Handle deletion safely
// ------------------------
if (isset($_GET['Delete'])) {
    $foodId = $_GET['Delete'];

    // Validate ObjectId format
    if (!preg_match('/^[a-f\d]{24}$/i', $foodId)) {
        die("Invalid food ID format");
    }

    if ($taskController->deleteTask($foodId)) {
        header("Location: " . BASE_URL . "public/donor/dashboard.php?deleted_food=" . h($foodId));
        exit();
    } else {
        die("Failed to delete food item or item not found");
    }
}

// ------------------------
// Include navbar and fetch food
// ------------------------
include_once(BASE_PATH . "public/donor/navbar.php");

// Fetch donor's food items
$userId = $_SESSION['user_id'];
$foodItemsCursor = $db->food->find(['donor_id' => $userId]);
$foodItems = iterator_to_array($foodItemsCursor);

// ------------------------
// Prepare dashboard content
// ------------------------
$donorContent = "<div class='container mt-4'>";
$donorContent .= "<h1 class='mb-4'>Hello, " . h($_SESSION['user_name']) . "!<br>Here are your shared meals:</h1>";
$donorContent .= "<div class='row'>";

if (count($foodItems) === 0) {
    $donorContent .= "<p>No food shared yet. <a href='shareFood.php'>Share your first meal!</a></p>";
} else {
    foreach ($foodItems as $item) {
        $donorContent .= "<div class='col-md-4 mb-3'>
            <div class='card shadow-sm'>
                <div class='card-body'>
                    <h5 class='card-title'>" . h($item['food_item']) . "</h5>
                    <p class='card-text'>
                        <strong>Category:</strong> " . h($item['food_category']) . "<br>
                        <strong>Quantity:</strong> " . h($item['quantity']) . "<br>
                        <strong>Pickup Time:</strong> " . h($item['pickup_time']) . "<br>
                        <strong>Location:</strong> " . h($item['location']) . "<br>
                        <small class='text-muted'>Published on: " . h($item['created_at']) . "</small><br><br>
                        <a href='updateFood.php?id=" . h($item['_id']) . "' class='btn btn-sm btn-warning'>Update</a>
                        <a href='" . BASE_URL . "public/donor/dashboard.php?Delete=" . h($item['_id']) . "' class='btn btn-sm btn-danger'
                           onclick=\"return confirm('Are you sure you want to delete this food item?');\">Delete</a>
                    </p>
                </div>
            </div>
        </div>";
    }
}

$donorContent .= "</div></div>";

// ------------------------
// Wrap with Dashboard Decorator
// ------------------------
$dashboard = new Dashboard($donorContent);
echo $dashboard->display();
?>

<link rel="stylesheet" type="text/css" href="<?= h($base_url) ?>public/css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
