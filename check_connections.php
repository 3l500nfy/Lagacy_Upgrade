<?php
/**
 * Check Current Database Connections
 * This script shows how many MySQL connections are currently active
 */

require_once('lib/globals.php');

echo "<h2>Current Database Connection Status</h2>";

try {
    // Open a connection to check status
    openConnection();
    global $dbh;
    
    // Get MySQL connection count
    $stmt = $dbh->query("SHOW STATUS LIKE 'Threads_connected'");
    $threads = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get max connections setting
    $stmt2 = $dbh->query("SHOW VARIABLES LIKE 'max_connections'");
    $max_conn = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    // Get current process list count
    $stmt3 = $dbh->query("SHOW PROCESSLIST");
    $processes = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Metric</th><th>Value</th><th>Status</th></tr>";
    
    $current_connections = $threads['Value'];
    $max_connections = $max_conn['Value'];
    $process_count = count($processes);
    $usage_percent = ($current_connections / $max_connections) * 100;
    
    // Status color
    $status_color = 'green';
    if ($usage_percent > 80) {
        $status_color = 'red';
    } elseif ($usage_percent > 60) {
        $status_color = 'orange';
    }
    
    echo "<tr>";
    echo "<td><strong>Current Active Connections</strong></td>";
    echo "<td><strong style='color: {$status_color}; font-size: 20px;'>{$current_connections}</strong></td>";
    echo "<td>" . ($current_connections < 50 ? "✅ Safe" : ($current_connections < 100 ? "⚠️ Warning" : "🔴 Critical")) . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td><strong>Maximum Allowed Connections</strong></td>";
    echo "<td>{$max_connections}</td>";
    echo "<td>" . ($max_connections >= 200 ? "✅ Good" : "⚠️ Low") . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td><strong>Connection Usage</strong></td>";
    echo "<td>" . number_format($usage_percent, 2) . "%</td>";
    echo "<td style='background-color: {$status_color}; color: white;'>" . 
         ($usage_percent < 50 ? "Low" : ($usage_percent < 80 ? "Medium" : "High")) . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td><strong>Active Processes</strong></td>";
    echo "<td>{$process_count}</td>";
    echo "<td>" . ($process_count < 50 ? "✅ Normal" : "⚠️ High") . "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    echo "<h3>Connection Details (Last 20 Processes)</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; font-size: 12px;'>";
    echo "<tr><th>ID</th><th>User</th><th>Host</th><th>Database</th><th>Command</th><th>Time</th><th>State</th><th>Info</th></tr>";
    
    $display_count = 0;
    foreach ($processes as $process) {
        if ($display_count++ >= 20) break;
        echo "<tr>";
        echo "<td>{$process['Id']}</td>";
        echo "<td>{$process['User']}</td>";
        echo "<td>{$process['Host']}</td>";
        echo "<td>{$process['db']}</td>";
        echo "<td>{$process['Command']}</td>";
        echo "<td>{$process['Time']}</td>";
        echo "<td>{$process['State']}</td>";
        echo "<td>" . substr($process['Info'] ?? '', 0, 50) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Analysis</h3>";
    echo "<ul>";
    echo "<li><strong>Current Load:</strong> {$current_connections} out of {$max_connections} connections</li>";
    echo "<li><strong>Capacity Remaining:</strong> " . ($max_connections - $current_connections) . " connections</li>";
    
    if ($current_connections > 100) {
        echo "<li style='color: red;'><strong>⚠️ WARNING:</strong> Connection count is high. System may struggle with more concurrent users.</li>";
    }
    
    if ($max_connections < 200) {
        echo "<li style='color: orange;'><strong>⚠️ RECOMMENDATION:</strong> Increase max_connections to at least 200 for better scalability.</li>";
    }
    
    echo "<li><strong>Estimated Capacity:</strong> " . floor($max_connections * 0.8) . " concurrent users (80% safety margin)</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

