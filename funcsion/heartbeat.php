<?php
require_once 'functions.php';

header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');

$page = isset($_GET['page']) ? $_GET['page'] : 'unknown';

// Sanitize page name
$page = preg_replace('/[^a-zA-Z0-9_]/', '', $page);

// Update tracking
update_tracking($page);

echo 'ok';
?>
