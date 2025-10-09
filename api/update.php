<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../hosttype.php';

require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';

use MongoDB\BSON\ObjectId;

if (session_status() === PHP_SESSION_NONE) session_start();
require_once BASE_PATH . 'src/controller/taskController.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['task_id'])) {
    echo json_encode(['error' => 'Task ID and data are required']);
    exit();
}

$taskController = new TaskController($db->food);

try {
    $updated = $taskController->updateTask($data);
    if ($updated) {
        echo json_encode(['success' => true, 'message' => 'Task updated']);
    } else {
        echo json_encode(['error' => 'No task updated. Check task ID']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
