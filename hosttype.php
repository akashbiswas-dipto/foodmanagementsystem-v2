<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$server_name = $_SERVER['HTTP_HOST'];
if ($server_name === 'localhost') {
    // Local environment
    define('BASE_URL', $protocol . 'localhost/foodmanagementsystem/');
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/foodmanagementsystem/');
} else {
    // Live server (EC2 / Domain)
    define('BASE_URL', $protocol . $server_name . '/foodmanagementsystem/');
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/foodmanagementsystem/');
}
?>
