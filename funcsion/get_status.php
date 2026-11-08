<?php
require_once 'functions.php';

$ip = isset($_GET['ip']) ? $_GET['ip'] : '';

if (empty($ip)) {
    echo 'NO DATA';
    exit;
}

// Sanitize IP to prevent directory traversal
$ip = preg_replace('/[^a-zA-Z0-9.:\-_]/', '', $ip);
if (empty($ip)) {
    echo 'NO DATA';
    exit;
}

echo get_victim_status($ip);
?>

