<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require_once '../../hosttype.php';
include BASE_PATH . "public/ngo/navbar.php";
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'patterns/decorator.php';
require_once BASE_PATH . 'patterns/factory.php';
require_once BASE_PATH . 'src/controller/TaskController.php';

// Only allow NGO (role 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ".BASE_URL."public/login.php");
    exit();
}

// Initialize MongoDB collections
$foodCollection = $db->food;
$foodRequestsCollection = $db->food_requests;

$taskController = new TaskController($foodCollection, $foodRequestsCollection);

// Handle request submission
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_food'])) {
    $foodId = $_POST['food_id'];
    $success = $taskController->requestFood($foodId);

    if ($success) {
        $msg = "<div class='alert alert-success'>Request submitted successfully!</div>";
    } else {
        $msg = "<div class='alert alert-warning'>You already requested this food item.</div>";
    }
}

// Base dashboard with decorators
$dashboard = new Dashboard("Welcome NGO, User ID: " . $_SESSION['user_id']);

// Fetch all active shared meals
$foodItems = iterator_to_array($foodCollection->find(['status' => 1]));

// Fetch all requests made by this NGO
$ngoId = $_SESSION['user_id'];
$requestsCursor = $foodRequestsCollection->find(['requested_by_id' => $ngoId]);
$requests = [];
foreach ($requestsCursor as $req) {
    $requests[(string)$req['food_id']] = $req['status']; 
}

// Function to render request status
function renderStatusBadge($status) {
    return match($status) {
        2 => '<span class="badge bg-warning text-dark">Requested</span>',
        3 => '<span class="badge bg-success">Approved</span>',
        4 => '<span class="badge bg-danger">Declined</span>',
        default => '<span class="badge bg-secondary">Unknown</span>',
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome - <?= htmlspecialchars($_SESSION['user_name']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL."public/css/dashboard.css"; ?>">
</head>
<body>
<div class="box">
    <div class="container mt-4">
        <?= $msg; // Display alert message ?>

        <!-- Greeting -->
        <h1 class="mb-4">Hello, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>

        <!-- Active Meals Section -->
        <h3 class="mb-3">Active Shared Meals</h3>
        <div class="row">
        <?php if (empty($foodItems)): ?>
            <p>No active food items available.</p>
        <?php else: ?>
            <?php foreach ($foodItems as $item): 
                $foodIdStr = (string)$item['_id'];
                $statusBadge = isset($requests[$foodIdStr]) 
                    ? renderStatusBadge($requests[$foodIdStr]) 
                    : null;
            ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><strong>Shared by:</strong> <?= htmlspecialchars($item['user_name']); ?></h5>
                        <h5 class="card-title"><?= htmlspecialchars($item['food_item']); ?></h5>
                        <p class="card-text mb-2">
                            <strong>Category:</strong> <?= htmlspecialchars($item['food_category']); ?><br>
                            <strong>Quantity:</strong> <?= htmlspecialchars($item['quantity']); ?><br>
                            <strong>Pickup Time:</strong> <?= htmlspecialchars($item['pickup_time']); ?><br>
                            <strong>Location:</strong> <?= htmlspecialchars($item['location']); ?><br>
                            <small class="text-muted">Published on: <?= htmlspecialchars($item['created_at']); ?></small>
                        </p>
                        <div class="mt-auto">
                            <?php if ($statusBadge): ?>
                                <?= $statusBadge ?>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" name="food_id" value="<?= $item['_id']; ?>">
                                    <button type="submit" name="request_food" class="btn btn-primary btn-sm">Request</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
