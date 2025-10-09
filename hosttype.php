<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$server_name = $_SERVER['HTTP_HOST'];
$base_url  = $protocol . ($server_name === 'localhost' ? 'localhost/foodmanagementsystem/' : $server_name . '/foodmanagementsystem/');
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/foodmanagementsystem/';

if (!defined('BASE_URL'))  define('BASE_URL', $base_url);
if (!defined('BASE_PATH')) define('BASE_PATH', $base_path);
?>
