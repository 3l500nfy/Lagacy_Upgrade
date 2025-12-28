<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
openConnection();
global $dbh;

// Validate required POST parameters
if (!isset($_POST['timespent']) || !isset($_POST['candid']) || !isset($_POST['examtyp'])) {
    echo 0;
    exit();
}

$timespent = $_POST['timespent'];
$timespent = ($timespent - 0) * 60; // Convert minutes to seconds
$candid = $_POST['candid'];
$examtype = $_POST['examtyp'];

// Validate that values are numeric
if (!is_numeric($timespent) || !is_numeric($candid) || !is_numeric($examtype)) {
    echo 0;
    exit();
}

$query = "SELECT starttime, elapsed, curenttime FROM tbltimecontrol WHERE candidateid=? && testid=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($candid, $examtype));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if record exists
if (!$row) {
    // Record doesn't exist - log for debugging
    error_log("Time adjustment: No record found for candidateid=$candid, testid=$examtype");
    echo 0;
    exit();
}

// Check if starttime exists and is not NULL
if (!isset($row['starttime']) || $row['starttime'] === null || $row['starttime'] === '') {
    error_log("Time adjustment: starttime is NULL or empty for candidateid=$candid, testid=$examtype");
    echo 0;
    exit();
}

$stime = $row['starttime'];

// Validate starttime is not empty
if (empty($stime)) {
    echo 0;
    exit();
}

try {
    $stimedt = new DateTime($stime);
    $ctime = clone $stimedt; // Clone to avoid modifying original
    $ctime->add(new DateInterval("PT" . $timespent . "S"));
    
    // curenttime is a datetime field, so format as full datetime
    $curenttime = $ctime->format("Y-m-d H:i:s");
    
    // Use prepared statement with bound parameters for all values
    $query1 = "UPDATE tbltimecontrol SET curenttime=?, elapsed=? WHERE candidateid=? && testid=?";
    $stmt1 = $dbh->prepare($query1);
    $exec = $stmt1->execute(array($curenttime, $timespent, $candid, $examtype));
    
    if ($exec) {
        // Check if any rows were actually updated
        if ($stmt1->rowCount() > 0) {
            echo 1;
            exit();
        } else {
            // No rows updated - record might not exist or values are the same
            error_log("Time adjustment: No rows updated. Candidate ID: $candid, Test ID: $examtype");
            echo 0;
            exit();
        }
    } else {
        // Execute failed
        $errorInfo = $stmt1->errorInfo();
        error_log("Time adjustment SQL error: " . print_r($errorInfo, true));
        echo 0;
        exit();
    }
} catch (Exception $e) {
    // Log error details
    error_log("Time adjustment error: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    echo 0;
    exit();
}

echo 0;
exit();
?>