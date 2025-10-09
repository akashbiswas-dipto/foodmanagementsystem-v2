<?php
declare(strict_types=1);


if (!interface_exists('TaskStrategy')) {
interface TaskStrategy {
    /**
     * Execute a specific algorithm on the provided tasks.
     * @param array $tasks
     * @return array
     */
    public function execute(array $tasks): array;
}


class SortByDate implements TaskStrategy {
    public function execute(array $tasks): array {
        usort($tasks, static fn($a, $b) =>
            strtotime($a['date'] ?? 'now') <=> strtotime($b['date'] ?? 'now')
        );
        return $tasks;
    }
}

class SortByPriority implements TaskStrategy {
    public function execute(array $tasks): array {
        usort($tasks, static fn($a, $b) =>
            ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0)
        );
        return $tasks;
    }
}

class SortByTitle implements TaskStrategy {
    public function execute(array $tasks): array {
        usort($tasks, static fn($a, $b) =>
            strcmp(strtolower($a['title'] ?? ''), strtolower($b['title'] ?? ''))
        );
        return $tasks;
    }
}
}