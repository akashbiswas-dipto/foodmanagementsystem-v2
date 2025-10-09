<?php
declare(strict_types=1);

require_once BASE_PATH . 'src/controller/TaskController.php';
require_once BASE_PATH . 'patterns/proxy.php';
require_once BASE_PATH . 'patterns/chain.php';
require_once BASE_PATH . 'patterns/decorator.php';
require_once BASE_PATH . 'patterns/observer.php';
require_once BASE_PATH.  'patterns/strategy.php';


use AppObservers\NotifierSubject;
use AppObservers\LogObserver;

class AppFacade {
    private TaskController $controller;
    private TaskProxy $proxy;
    private ValidationChain $validator;
    private NotifierSubject $notifier;

    public function __construct($db, string $base_url) {
        $this->controller = new TaskController();
        $this->proxy = new TaskProxy($this->controller, $_SESSION['role'] ?? null);
        $this->validator = new ValidationChain();
        $this->notifier = new NotifierSubject();
        $this->notifier->attach(new LogObserver());
    }

    public function shareTask(array $data): void {
        $this->validator->validateShareTask($data);
        $decorated = new TaskPriorityDecorator($data);
        $payload = $decorated->getData();
        $this->proxy->shareTask($payload);
        $this->notifier->notifyAll("Task shared by " . ($_SESSION['user_name'] ?? 'unknown'));
    }

    public function updateTask(array $data): void {
        $this->validator->validateUpdateTask($data);
        $this->proxy->updateTask($data);
        $this->notifier->notifyAll("Task updated by " . ($_SESSION['user_name'] ?? 'unknown'));
    }

    public function deleteTask(string $taskId): void {
        $this->proxy->deleteTask($taskId);
        $this->notifier->notifyAll("Task deleted: $taskId");
    }

    public function requestTask(string $taskId): void {
        $this->proxy->requestTask($taskId);
        $this->notifier->notifyAll("Task requested: $taskId by " . ($_SESSION['user_name'] ?? 'unknown'));
    }

    public function approveTaskRequest(string $requestId): void {
        $this->proxy->approveTaskRequest($requestId);
        $this->notifier->notifyAll("Task request approved: $requestId");
    }

    public function declineTaskRequest(string $requestId): void {
        $this->proxy->declineTaskRequest($requestId);
        $this->notifier->notifyAll("Task request declined: $requestId");
    }
}
