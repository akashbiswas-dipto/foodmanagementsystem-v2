<?php
declare(strict_types=1);

require_once BASE_PATH . 'src/controller/taskController.php';
require_once BASE_PATH . 'patterns/proxy.php';
require_once BASE_PATH . 'patterns/chain.php';
require_once BASE_PATH . 'patterns/observer.php';
require_once BASE_PATH . 'patterns/decorator.php';

class AppFacade {
    private TaskController $controller;
    private TaskProxy $proxy; // You can rename to TaskProxy if you create one
    private ValidationChain $validator;
    private NotifierSubject $notifier;

    public function __construct($db, string $base_url) {
        $this->controller = new TaskController($db, $base_url);

        // Proxy for permission checks
        $this->proxy = new TaskProxy($this->controller, $_SESSION['role'] ?? null); // or TaskProxy

        // Validation chain
        $this->validator = new ValidationChain();

        // Observer pattern for notifications
        $this->notifier = new NotifierSubject();
        $this->notifier->attach(new \AppObservers\LogObserver());
    }

    // ---------------- SHARE TASK ----------------
    public function shareTask(array $data): void {
        $this->validator->validateShareTask($data);
        $decorated = new TaskPriorityDecorator($data);
        $payload = $decorated->getData();

        $this->proxy->shareTask($payload);
        $this->notifier->notifyAll("Task shared by " . ($_SESSION['user_name'] ?? 'unknown'));
    }

    // ---------------- UPDATE TASK ----------------
    public function updateTask(array $data): void {
        $this->validator->validateUpdateTask($data);
        $this->proxy->updateTask($data);
        $this->notifier->notifyAll("Task updated by " . ($_SESSION['user_name'] ?? 'unknown'));
    }

    // ---------------- DELETE TASK ----------------
    public function deleteTask(string $taskId): void {
        $this->proxy->deleteTask($taskId);
        $this->notifier->notifyAll("Task deleted: $taskId");
    }

    // ---------------- REQUEST TASK ----------------
    public function requestTask(string $taskId): void {
        $this->proxy->requestTask($taskId);
        $this->notifier->notifyAll("Task requested: $taskId by " . ($_SESSION['user_name'] ?? 'unknown'));
    }

    // ---------------- APPROVE TASK REQUEST ----------------
    public function approveTaskRequest(string $requestId): void {
        $this->proxy->approveTaskRequest($requestId);
        $this->notifier->notifyAll("Task request approved: $requestId");
    }

    // ---------------- DECLINE TASK REQUEST ----------------
    public function declineTaskRequest(string $requestId): void {
        $this->proxy->declineTaskRequest($requestId);
        $this->notifier->notifyAll("Task request declined: $requestId");
    }
}
