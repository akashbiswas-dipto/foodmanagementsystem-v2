<?php
declare(strict_types=1);

require_once BASE_PATH . 'src/controller/taskController.php';

class TaskProxy {
    private TaskController $controller;
    private ?int $role;

    public function __construct(TaskController $controller, ?int $role = null) {
        $this->controller = $controller;
        $this->role = $role;
    }

    /**
     * Ensure the current role is allowed to perform an action
     *
     * @param array<int> $allowedRoles
     * @throws RuntimeException
     */
    private function ensureRole(array $allowedRoles): void {
        if (!in_array($this->role, $allowedRoles, true)) {
            throw new RuntimeException("Permission denied for role {$this->role}");
        }
    }

   
    public function shareTask(array $data): string {
        $this->ensureRole([1, 2]); // roles 1 & 2 = donors
        return (string) $this->controller->shareTask($data);
    }

    public function updateTask(array $data): bool {
        $this->ensureRole([1, 2]);
        return $this->controller->updateTask($data);
    }

    public function deleteTask(string $taskId): bool {
        $this->ensureRole([1, 2]);
        return $this->controller->deleteTask($taskId);
    }

    public function requestTask(string $taskId): bool {
        $this->ensureRole([3]); // role 3 = NGO
        return $this->controller->requestTask($taskId);
    }

    public function approveTaskRequest(string $requestId): bool {
        $this->ensureRole([1, 2]);
        return $this->controller->approveTaskRequest($requestId);
    }

    public function declineTaskRequest(string $requestId): bool {
        $this->ensureRole([1, 2]);
        return $this->controller->declineTaskRequest($requestId);
    }
}
