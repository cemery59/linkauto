<?php
$visitor_file = __DIR__ . '/visitors.json';
$visitors = array();

if (file_exists($visitor_file)) {
    $data = @json_decode(@file_get_contents($visitor_file), true);
    if (is_array($data)) {
        $visitors = $data;
    }
}

if (empty($visitors)) {
    echo '<tr><td colspan="9" style="text-align: center; padding: 40px; color: #999;">No visitors yet</td></tr>';
} else {
    // Show last 200 visitors (most recent first)
    $visitors = array_reverse(array_slice($visitors, -200));
    foreach ($visitors as $visitor) {
        $botBadge = $visitor['bot'] === 'Yes' ? '<span class="badge badge-yes">Yes</span>' : '<span class="badge badge-no">No</span>';
        $proxyBadge = $visitor['proxy'] === 'Yes' ? '<span class="badge badge-yes">Yes</span>' : '<span class="badge badge-no">No</span>';
        $deviceBadge = $visitor['device'] === 'Mobile' ? '<span class="badge badge-mobile">Mobile</span>' : '<span class="badge badge-desktop">Desktop</span>';
        $userAgent = htmlspecialchars(substr($visitor['user_agent'], 0, 60));
        
        echo '<tr>';
        echo '<th scope="row">' . htmlspecialchars($visitor['ip']) . '</th>';
        echo '<td>' . htmlspecialchars($visitor['date']) . '</td>';
        echo '<td>' . htmlspecialchars($visitor['country']) . ' ' . $visitor['flag'] . '</td>';
        echo '<td>' . $botBadge . '</td>';
        echo '<td>' . $proxyBadge . '</td>';
        echo '<td>' . $deviceBadge . '</td>';
        echo '<td>' . htmlspecialchars($visitor['platform']) . '</td>';
        echo '<td>' . htmlspecialchars($visitor['browser']) . '</td>';
        echo '<td>' . $userAgent . '...</td>';
        echo '</tr>';
    }
}
?>

