<?php
$visitor_file = __DIR__ . '/visitors.json';
file_put_contents($visitor_file, json_encode(array()), LOCK_EX);
echo 'OK';
?>

