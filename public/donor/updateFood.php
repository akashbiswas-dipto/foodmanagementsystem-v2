<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: ../login.php");
    exit();
}

include_once("navbar.php");
require_once BASE_PATH . 'src/controller/TaskController.php';
require_once BASE_PATH . 'patterns/proxy.php';

// Initialize TaskController
$taskController = new TaskController($db->food); 
$taskProxy = new TaskProxy($taskController, $_SESSION['role']);

$errorMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share_food'])) {
    try {
        $taskProxy->shareTask($_POST);
        header("Location: " . BASE_URL . "public/donor/dashboard.php?new_food=1");
        exit();
    } catch (Exception $e) {
        $errorMsg = "Error sharing food: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Share Food - <?= htmlspecialchars($_SESSION['user_name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/sharefood.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-3">Share a Meal</h2>
            <?php if ($errorMsg): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>
            <form method="post" class="row g-3">
                <div class="col-12">
                    <label class="form-label">Food Item Name</label>
                    <input type="text" class="form-control" name="food_item" placeholder="Enter food item name" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Food Category</label>
                    <select class="form-select" name="food_category" required>
                        <option value="" disabled selected>Select Food Category</option>
                        <?php
                        $categories = ["Cooked Meals","Bakery","Produce","Dairy","Beverages","Packaged","Other"];
                        foreach ($categories as $cat) {
                            echo "<option value='$cat'>$cat</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quantity (in servings)</label>
                    <input type="number" class="form-control" name="quantity" placeholder="Enter quantity" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pickup Time</label>
                    <input type="datetime-local" class="form-control" name="pickup_time" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Pickup Location</label>
                    <input type="text" class="form-control" name="location" placeholder="Enter pickup location" required>
                </div>
                <input type="hidden" name="status" value="open">
                <div class="col-12 mt-3">
                    <button type="submit" name="share_food" class="btn btn-success w-100">Share Food</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
