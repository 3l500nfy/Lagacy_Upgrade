<?php
/**
 * CSP Violation Reporting Endpoint
 * Phase B5.3 - Content Security Policy (REPORT-ONLY)
 * 
 * Receives and logs Content-Security-Policy violation reports
 * in REPORT-ONLY mode. Returns HTTP 204 No Content.
 */

// Ensure no output before processing
ob_start();

// Set content type
header('Content-Type: application/json');

// Get violation report from request body
$report = file_get_contents('php://input');

// Decode JSON report
$data = json_decode($report, true);

// Log directory (create if doesn't exist)
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Log file path
$log_file = $log_dir . '/csp-violations.log';

// Log violation if data is valid
if ($data && is_array($data)) {
    $log_entry = date('Y-m-d H:i:s') . " - " . json_encode($data) . "\n";
    @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Clear any output
ob_end_clean();

// Return HTTP 204 No Content
http_response_code(204);
exit;
