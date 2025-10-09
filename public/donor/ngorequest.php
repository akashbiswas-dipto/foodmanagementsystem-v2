<?php
declare(strict_types=1);

session_start();

use MongoDB\BSON\ObjectId;
use AppObservers\NotifierSubject;
use AppObservers\ObserverInterface;
use AppObservers\LogObserver;

// Access control
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: ../login.php");
    exit();
}

include_once("navbar.php");
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'patterns/observer.php'; // Make sure this contains your namespaced classes

// Initialize collections
$donorId = $_SESSION['user_id'];
$foodCollection = $db->food;
$foodRequestsCollection = $db->foodRequestsCollection;

// Fetch all food items shared by this donor
$foodItemsCursor = $foodCollection->find(['donor_id' => $donorId]);
$foodItems = iterator_to_array($foodItemsCursor);

// Initialize notifier
$requestsSubject = new NotifierSubject();
$requestsSubject->attach(new LogObserver());

// Get IDs of donor's food items
$foodIds = array_map(fn($item) => $item['_id'], $foodItems);

// Fetch all requests made to these food items
$requests = [];
if (!empty($foodIds)) {
    $requestsCursor = $foodRequestsCollection->find([
        'food_id' => ['$in' => $foodIds]
    ]);
    $requests = iterator_to_array($requestsCursor);

    // Notify about new requests
    foreach ($requests as $req) {
        $foodItem = $foodCollection->findOne(['_id' => new ObjectId((string)$req['food_id'])]);
        $requestsSubject->notifyAll("New request for " . $foodItem['food_item']);
    }
}
