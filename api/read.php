<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../hosttype.php';

require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';

use MongoDB\BSON\ObjectId;

if (session_status() === PHP_SESSION_NONE) session_start();
require_once BASE_PATH . 'src/controller/taskController.php';

$taskController = new TaskController($db->food);

try {
    if (isset($_GET['task_id'])) {
        $task = $taskController->getTaskById($_GET['task_id']);
        if ($task) {
            echo json_encode(['success' => true, 'task' => $task]);
        } else {
            echo json_encode(['error' => 'Task not found']);
        }
    } else {
        $tasks = $taskController->getAllTasks();
        echo json_encode(['success' => true, 'tasks' => $tasks]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
