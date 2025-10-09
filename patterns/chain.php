<?php
declare(strict_types=1);

class ValidationChain {

    // ---------------- Task Validations ----------------
    public function validateShareTask(array $data): void {
        $required = ['title', 'description'];
        foreach ($required as $field) {
            $value = trim((string)($data[$field] ?? ''));
            if ($value === '') {
                throw new InvalidArgumentException("Field '$field' is required");
            }
        }

        if (isset($data['priority']) && (!is_numeric($data['priority']) || (int)$data['priority'] < 0)) {
            throw new InvalidArgumentException("Priority must be a non-negative number");
        }
    }

    public function validateUpdateTask(array $data): void {
        if (empty($data['task_id'])) {
            throw new InvalidArgumentException("Task ID is required for update");
        }
        $this->validateShareTask($data);
    }

    // ---------------- Food Validations ----------------
    public function validateFood(array $data): void {
        $required = ['food_item', 'food_category', 'quantity', 'pickup_time', 'location'];

        foreach ($required as $field) {
            $value = trim((string)($data[$field] ?? ''));
            if ($value === '') {
                throw new InvalidArgumentException("Field '$field' is required");
            }
        }

        if (!is_numeric($data['quantity']) || (int)$data['quantity'] <= 0) {
            throw new InvalidArgumentException("Quantity must be a positive number");
        }

        // Validate datetime
        if (strtotime($data['pickup_time']) === false) {
            throw new InvalidArgumentException("Pickup time is invalid");
        }

        // Optional: validate category
        $validCategories = ["Cooked Meals","Bakery","Produce","Dairy","Beverages","Packaged","Other"];
        if (!in_array($data['food_category'], $validCategories, true)) {
            throw new InvalidArgumentException("Food category is invalid");
        }
    }
}
