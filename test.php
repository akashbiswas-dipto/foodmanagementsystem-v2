<?php
require __DIR__ . '/vendor/autoload.php';

$uri = 'mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/?retryWrites=true&w=majority&appName=foodmanagement';


try {
    $client = new MongoDB\Client($uri);
    $db = $client->selectDatabase('myDB');
    $result = $db->listCollections(); 
    echo "Connected — collections:\n";
    foreach ($result as $c) { echo $c->getName() . PHP_EOL; }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
