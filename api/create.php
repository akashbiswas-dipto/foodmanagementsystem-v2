<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../hosttype.php';
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';

use MongoDB\BSON\ObjectId;

// Start session if not started
if (session_status() === PHP_SESSION_NONE) session_start();

require_once BASE_PATH . 'src/controller/taskController.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'No data provided']);
    exit();
}

$taskController = new TaskController($db->food);

try {
    $taskId = $taskController->shareTask($data);
    echo json_encode(['success' => true, 'task_id' => (string)$taskId]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
