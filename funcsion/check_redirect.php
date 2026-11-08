<?php
require_once 'functions.php';

header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Get redirect command for current visitor
$command = get_redirect_command();

echo $command;
?>
