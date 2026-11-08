<?php
require_once '../funcsion/functions.php';
$ip = get_client_ip();
$page = isset($_GET['p']) ? $_GET['p'] : 'unknown';
$view = isset($_GET['v']) ? $_GET['v'] : $page;

// Update heartbeat
update_heartbeat($ip, $view);

// Check for redirect command
$redirect = get_redirect_command($ip);

// Return 1x1 transparent GIF with redirect info in header
header('Content-Type: image/gif');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('X-R: ' . ($redirect ? $redirect : ''));

// 1x1 transparent GIF
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
