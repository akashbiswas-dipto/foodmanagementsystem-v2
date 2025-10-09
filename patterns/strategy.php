<?php
declare(strict_types=1);

/**
 * Task Strategy Pattern
 * ---------------------
 * Provides interchangeable sorting strategies for tasks.
 * Usage example:
 * 
 *   $controller = new TaskController();
 *   $controller->setStrategy(new SortByPriority());
 *   $sortedTasks = $controller->execute($tasks);
 */

// Base interface for all task strategies


if (!interface_exists('TaskStrategy')) {
interface TaskStrategy {
    /**
     * Execute a specific algorithm on the provided tasks.
     * @param array $tasks
     * @return array
     */
    public function execute(array $tasks): array;
}

// ------------------------------------------------------
// Concrete Strategies
// ------------------------------------------------------

/**
 * Sort tasks by date in ascending order (earliest first)
 */
class SortByDate implements TaskStrategy {
    public function execute(array $tasks): array {
        usort($tasks, static fn($a, $b) =>
            strtotime($a['date'] ?? 'now') <=> strtotime($b['date'] ?? 'now')
        );
        return $tasks;
    }
}

/**
 * Sort tasks by priority in descending order (highest first)
 */
class SortByPriority implements TaskStrategy {
    public function execute(array $tasks): array {
        usort($tasks, static fn($a, $b) =>
            ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0)
        );
        return $tasks;
    }
}

/**
 * Sort tasks by title alphabetically (optional)
 */
class SortByTitle implements TaskStrategy {
    public function execute(array $tasks): array {
        usort($tasks, static fn($a, $b) =>
            strcmp(strtolower($a['title'] ?? ''), strtolower($b['title'] ?? ''))
        );
        return $tasks;
    }
}
}