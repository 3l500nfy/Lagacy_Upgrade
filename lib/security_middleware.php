<?php
// Security middleware for exam.pqdevs.com

class SecurityMiddleware {
    
    public static function init() {
        // Start session if not started
        if (!isset($_SESSION)) {
            session_start();
        }
        
        // Set security headers
        self::setSecurityHeaders();
        
        // Validate request
        self::validateRequest();
        
        // Check for suspicious activity
        self::detectSuspiciousActivity();
    }
    
    private static function setSecurityHeaders() {
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
    
    private static function validateRequest() {
        // Block directory traversal
        $requestUri = $_SERVER['REQUEST_URI'];
        if (preg_match('/\.\./', $requestUri)) {
            self::logSecurityEvent('DIRECTORY_TRAVERSAL', $requestUri);
            header("HTTP/1.0 403 Forbidden");
            die("Access denied");
        }
        
        // Block access to sensitive files
        $sensitiveFiles = ['globals.php', 'security.php', 'db.php', 'cbt_func.php'];
        foreach ($sensitiveFiles as $file) {
            if (strpos($requestUri, $file) !== false) {
                self::logSecurityEvent('SENSITIVE_FILE_ACCESS', $requestUri);
                header("HTTP/1.0 403 Forbidden");
                die("Access denied");
            }
        }
        
        // Block SQL injection attempts in query string
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        $sqlPatterns = ['union', 'select', 'insert', 'update', 'delete', 'drop', 'create', 'alter'];
        foreach ($sqlPatterns as $pattern) {
            if (stripos($queryString, $pattern) !== false) {
                self::logSecurityEvent('SQL_INJECTION_ATTEMPT', $queryString);
                header("HTTP/1.0 403 Forbidden");
                die("Access denied");
            }
        }
    }
    
    private static function detectSuspiciousActivity() {
        // Block suspicious user agents
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $suspiciousPatterns = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget'];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                self::logSecurityEvent('SUSPICIOUS_USER_AGENT', $userAgent);
                header("HTTP/1.0 403 Forbidden");
                die("Access denied");
            }
        }
        
        // Rate limiting (basic implementation)
        $ip = $_SERVER['REMOTE_ADDR'];
        $currentTime = time();
        
        if (!isset($_SESSION['rate_limit'][$ip])) {
            $_SESSION['rate_limit'][$ip] = ['count' => 0, 'time' => $currentTime];
        }
        
        if ($currentTime - $_SESSION['rate_limit'][$ip]['time'] > 60) {
            $_SESSION['rate_limit'][$ip] = ['count' => 1, 'time' => $currentTime];
        } else {
            $_SESSION['rate_limit'][$ip]['count']++;
            
            if ($_SESSION['rate_limit'][$ip]['count'] > 100) {
                self::logSecurityEvent('RATE_LIMIT_EXCEEDED', $ip);
                header("HTTP/1.0 429 Too Many Requests");
                die("Rate limit exceeded");
            }
        }
    }
    
    private static function logSecurityEvent($event, $details) {
        $logFile = dirname(__FILE__) . '/../logs/security.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        $logEntry = "[$timestamp] [$ip] [$event] [$details] [$userAgent] [$requestUri]\n";
        
        // Create logs directory if it doesn't exist
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Initialize security middleware
SecurityMiddleware::init();
?> 