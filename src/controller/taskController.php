<?php
declare(strict_types=1);

// --------------------------
// Error Reporting
// --------------------------
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// --------------------------
// Start Session
// --------------------------
if (session_status() == PHP_SESSION_NONE) session_start();

// --------------------------
// Load Config & Dependencies
// --------------------------
require_once dirname(__DIR__, 2) . '/hosttype.php';
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;

class TaskController {
    private Collection $collection; // donor food collection
    private Collection $requestsCollection; // NGO requests collection

    public function __construct(Collection $collection, ?Collection $requestsCollection = null) {
        $this->collection = $collection;
        $this->requestsCollection = $requestsCollection ?? $collection; // fallback if not provided
    }

    // ---------------- Donor CRUD ----------------
    public function shareTask(array $data): ObjectId {
        $insertResult = $this->collection->insertOne([
            'donor_id'      => $_SESSION['user_id'],
            'user_name'     => $_SESSION['user_name'],
            'food_item'     => $data['food_item'] ?? '',
            'food_category' => $data['food_category'] ?? '',
            'quantity'      => (int)($data['quantity'] ?? 0),
            'pickup_time'   => $data['pickup_time'] ?? '',
            'location'      => $data['location'] ?? '',
            'status'        => $data['status'] ?? 1,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return $insertResult->getInsertedId();
    }

    public function updateTask(array $data): bool {
        if (!isset($data['task_id'])) return false;

        $updateResult = $this->collection->updateOne(
            ['_id' => new ObjectId($data['task_id'])],
            ['$set' => [
                'food_item'     => $data['food_item'] ?? '',
                'food_category' => $data['food_category'] ?? '',
                'quantity'      => (int)($data['quantity'] ?? 0),
                'pickup_time'   => $data['pickup_time'] ?? '',
                'location'      => $data['location'] ?? '',
                'status'        => $data['status'] ?? 1,
                'updated_at'    => date('Y-m-d H:i:s')
            ]]
        );

        return $updateResult->getModifiedCount() > 0;
    }

    public function deleteTask(string $taskId): bool {
        if (!preg_match('/^[a-f\d]{24}$/i', $taskId)) {
            throw new InvalidArgumentException("Invalid task ID: $taskId");
        }
        $deleteResult = $this->collection->deleteOne(['_id' => new ObjectId($taskId)]);
        return $deleteResult->getDeletedCount() > 0;
    }

    // ---------------- Fetch ----------------
    public function getTasksByUser(int $userId): array {
        return iterator_to_array($this->collection->find(['donor_id' => $userId]));
    }

    public function getTaskById(string $taskId): ?array {
        try {
            $doc = $this->collection->findOne(['_id' => new ObjectId($taskId)]);
            return $doc ? (array)$doc : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAllTasks(): array {
        return iterator_to_array($this->collection->find([]));
    }

    // ---------------- NGO Request ----------------
    public function requestFood(string $foodId): bool {
        if (!$this->requestsCollection) {
            throw new RuntimeException("Requests collection not set");
        }

        // Validate ObjectId
        if (!preg_match('/^[a-f\d]{24}$/i', $foodId)) {
            throw new InvalidArgumentException("Invalid food ID: $foodId");
        }

        $foodItem = $this->collection->findOne(['_id' => new ObjectId($foodId)]);
        if (!$foodItem) return false;

        // Check if this NGO already requested this food
        $existing = $this->requestsCollection->findOne([
            'food_id' => $foodItem['_id'],
            'requested_by_id' => $_SESSION['user_id']
        ]);

        if ($existing) return false;

        $this->requestsCollection->insertOne([
            'food_id' => $foodItem['_id'],
            'requested_by_id' => $_SESSION['user_id'],
            'requested_by_name' => $_SESSION['user_name'],
            'status' => 2, // 2 = requested
            'requested_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        return true;
    }
}

// ------------------ Handle POST requests ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_food'])) {
    $taskController = new TaskController($db->food, $db->food_requests);
    $foodId = $_POST['food_id'];
    $success = $taskController->requestFood($foodId);

    if ($success) {
        header("Location: " . BASE_URL . "public/ngo/dashboard.php?msg=requested");
    } else {
        header("Location: " . BASE_URL . "public/ngo/dashboard.php?msg=already_requested");
    }
    exit();
}
