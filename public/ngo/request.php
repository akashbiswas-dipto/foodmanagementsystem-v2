<?php
declare(strict_types=1);

use MongoDB\BSON\ObjectId;
session_start();

require_once '../../hosttype.php';
require_once BASE_PATH . 'config.php';
include BASE_PATH . "public/ngo/navbar.php";
require_once BASE_PATH . 'src/controller/TaskController.php';
require_once BASE_PATH . 'patterns/proxy.php';

// --------------------------
// Ensure NGO login
// --------------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ".$base_url."public/login.php");
    exit();
}

// --------------------------
// MongoDB Collections
// --------------------------
$foodCollection = $db->food;
$foodRequestsCollection = $db->food_requests;
$usersCollection = $db->users;

// --------------------------
// TaskController + Proxy
// --------------------------
$taskController = new TaskController($foodCollection, $foodRequestsCollection);
$taskProxy = new TaskProxy($taskController, $_SESSION['role']);

// --------------------------
// Handle Food Request Submission
// --------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_food'])) {
    $foodId = $_POST['food_id'];
    try {
        $success = $taskProxy->requestTask($foodId);
        $msg = $success ? 'requested' : 'already_requested';
    } catch (RuntimeException $e) {
        $msg = 'error';
    }
    header("Location: ".$base_url."public/ngo/request.php?msg=$msg");
    exit();
}

// --------------------------
// Fetch all requests made by this NGO
// --------------------------
$ngoId = $_SESSION['user_id'];
$requestsCursor = $foodRequestsCollection->find(['requested_by_id' => $ngoId]);
$requestedFoodIds = [];
$requestStatusMap = [];

foreach ($requestsCursor as $req) {
    if (!empty($req['food_id'])) {
        $foodIdStr = (string)$req['food_id'];
        $requestedFoodIds[] = new ObjectId($foodIdStr);
        $requestStatusMap[$foodIdStr] = $req['status'] ?? 0;
    }
}

// --------------------------
// Fetch corresponding food items
// --------------------------
$foodItems = [];
$donors = [];

if (!empty($requestedFoodIds)) {
    $foodItemsCursor = $foodCollection->find(['_id' => ['$in' => $requestedFoodIds]]);
    $foodItems = iterator_to_array($foodItemsCursor);

    // Collect donor info
    $donorIds = [];
    foreach ($foodItems as $item) {
        if (!empty($item['donor_id'])) {
            $donorIds[(string)$item['donor_id']] = true;
        }
    }
    $donorIds = array_keys($donorIds);

    if (!empty($donorIds)) {
        $donorsCursor = $usersCollection->find([
            '_id' => ['$in' => array_map(fn($id) => new ObjectId($id), $donorIds)]
        ]);
        foreach ($donorsCursor as $donor) {
            $donors[(string)$donor['_id']] = $donor;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/dashboard.css">
<title>Requested Meals - <?= htmlspecialchars($_SESSION['user_name']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="box">
    <div class="container mt-4">
        <h1 class="mb-4">
            Hello, <?= htmlspecialchars($_SESSION['user_name']); ?>!<br>
            Here are the meals you requested
        </h1>

        <div class="row">
        <?php if (empty($foodItems)): ?>
            <p>You have not requested any meals yet.</p>
        <?php else: ?>
            <?php foreach ($foodItems as $item): 
                $foodIdStr = (string)$item['_id'];
                $requestStatus = $requestStatusMap[$foodIdStr] ?? 0;
                $donorIdStr = isset($item['donor_id']) ? (string)$item['donor_id'] : null;
                $donor = $donorIdStr && isset($donors[$donorIdStr]) ? $donors[$donorIdStr] : null;
            ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><strong>Shared by:</strong> <?= htmlspecialchars($item['user_name'] ?? 'Unknown'); ?></h5>
                        <h5 class="card-title"><?= htmlspecialchars($item['food_item'] ?? 'N/A'); ?></h5>
                        <p class="card-text">
                            <strong>Category:</strong> <?= htmlspecialchars($item['food_category'] ?? 'N/A'); ?><br>
                            <strong>Quantity:</strong> <?= htmlspecialchars((string)($item['quantity'] ?? 'N/A')); ?><br>
                            <strong>Pickup Time:</strong> <?= htmlspecialchars($item['pickup_time'] ?? 'N/A'); ?><br>
                            <strong>Location:</strong> <?= htmlspecialchars($item['location'] ?? 'N/A'); ?><br>
                            <small class="text-muted">Published on: <?= htmlspecialchars($item['created_at'] ?? ''); ?></small><br>
                            <strong>Request Status:</strong><br>
                            <?php
                                echo match($requestStatus) {
                                    2 => "<span class='badge bg-warning text-dark'>Pending</span>",
                                    3 => "<span class='badge bg-success'>Approved</span>" .
                                         ($donor ? "<br><strong>Donor Contact:</strong> " . htmlspecialchars($donor['phone'] ?? '') : ""),
                                    4 => "<span class='badge bg-danger'>Declined</span>",
                                    default => "<span class='badge bg-secondary'>Unknown/Deleted</span>",
                                };
                            ?>
                        </p>
                        <?php if ($requestStatus === 0 || $requestStatus === 4): ?>
                        <!-- Form to request food again -->
                        <form method="POST" class="mt-auto">
                            <input type="hidden" name="food_id" value="<?= $foodIdStr ?>">
                            <button type="submit" name="request_food" class="btn btn-primary btn-sm w-100">Request Food</button>
                        </form>
                        <?php endif; ?>
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
