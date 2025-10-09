<?php
declare(strict_types=1);

// ---------------- TASK DECORATORS ----------------
class TaskDecorator {
    protected array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function getData(): array {
        return $this->data;
    }
}

class TaskPriorityDecorator extends TaskDecorator {
    public function __construct(array $data) {
        parent::__construct($data);
        $this->data['priority'] = isset($data['priority']) && $data['priority'] ? 1 : 0;
    }
}

// ---------------- DASHBOARD ----------------
class Dashboard {
    protected string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }

    public function display(): string {
        return "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Donor Dashboard</title>
            <link rel='stylesheet' href='" . BASE_URL . "public/css/dashboard.css'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
            <div class='box'>
            {$this->content}
            </div>
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>";
            }
        }

// Optional dashboard decorator
abstract class DashboardDecorator {
    protected Dashboard $dashboard;

    public function __construct(Dashboard $dashboard) {
        $this->dashboard = $dashboard;
    }

    abstract public function display(): string;
}

// Example role-based decorator
class RoleDashboardDecorator extends DashboardDecorator {
    private int $role;

    public function __construct(Dashboard $dashboard, int $role) {
        parent::__construct($dashboard);
        $this->role = $role;
    }

    public function display(): string {
        $prefix = "<div class='role-info'>Role: {$this->role}</div>";
        return $prefix . $this->dashboard->display();
    }
}
