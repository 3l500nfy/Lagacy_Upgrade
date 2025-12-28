<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();

$tid = ((isset($_POST['tid'])) ? (intval(clean($_POST['tid']))) : (0));

if (!is_test_administrator_of($tid)) {
    echo 7;
    exit();
}

$vid = ((isset($_POST['tvenue'])) ? (intval(clean($_POST['tvenue']))) : (0));
$dst = ((isset($_POST['dailystarttime'])) ? (clean($_POST['dailystarttime'])) : ("00:00"));
$det = ((isset($_POST['dailyendtime'])) ? (clean($_POST['dailyendtime'])) : ("23:59"));
$tdt = ((isset($_POST['tdate'])) ? (clean($_POST['tdate'])) : ('0000-00-00'));

// Extract only time portion if timepicker outputs date+time format
// Handle formats like "2024-01-01 00:00" or "00:00" or "00:00:00"
if (preg_match("/(\d{2}:\d{2})/", $dst, $matches)) {
    $dst = $matches[1]; // Extract HH:mm format
}
if (preg_match("/(\d{2}:\d{2})/", $det, $matches)) {
    $det = $matches[1]; // Extract HH:mm format
}
$now = new DateTime();
$schid_date = new DateTime($tdt . " " . $dst);
$intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
if ($intaval > 0 && isset($_POST['safemode']) /*-180*/) {
    echo 6;
    exit();
}


if (!preg_match("~^((([0][0-9])|([1][0-9])|([2][0-3])):[0-5][0-9])$~", $dst) || !preg_match("~^((([0][0-9])|([1][0-9])|([2][0-3])):[0-5][0-9])$~", $det) || !preg_match("~^[1-9][0-9]{3}-(([0][1-9])|([1][0-2]))-(([0][1-9])|([1][0-9])|([2][0-9])|([3][0-1]))$~", $tdt)) {
    echo 2;
    exit(); //2 for invalid input
}

if ($vid == 0) {
    echo 2;
    exit(); //2 for invalid input
}
$venuecapacity = get_venue_capacity($vid);

$nb = ((isset($_POST['batchcount'])) ? (clean($_POST['batchcount'])) : (-1)); // no of batches
$npb = ((isset($_POST['noperbatch']) && clean($_POST['noperbatch']) != 0) ? (clean($_POST['noperbatch'])) : ($venuecapacity)); // no per batch
//check to make sure that the test range is at least 1hr greater than the test duration
$tparam = get_test_config_param_as_array($tid);
$dur = $tparam['duration'];
if ($nb == -1) {
    $totaldur = $dur;
} else {
    $totaldur = $dur * $nb;
}

$dst_arr = explode(":", $dst);
$det_arr = explode(":", $det);
$dst_token = ($dst_arr[0] * 60) + $dst_arr[1];
$det_token = ($det_arr[0] * 60) + $det_arr[1];
if (($det_token - $dst_token) <= 0) {
    echo 3;
    exit(); // 3 for invalid date range
}
if (($det_token - $dst_token) - $totaldur < 0) {
    echo 4;
    exit(); // 4 for invalid date duration
}


//check for possible clashes
$query = "select * from tblscheduling where (((dailystarttime < ? || dailystarttime = ?) && (dailyendtime > ?|| dailyendtime = ?)) || ((dailystarttime < ? || dailystarttime = ?) && (dailyendtime > ? || dailyendtime = ?))) && date=? && venueid= ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($dst,$dst,$dst,$dst,$det,$det,$det,$det,$tdt,$vid));

if ($stmt->rowCount() > 0) {
    echo 5;
    exit(); // 5 for possible clash
}

//get all mapping
//Make db query
// Note: starttime and endtime are REQUIRED fields (NOT NULL), so we set them to '00:00:00' as defaults
// dailystarttime and dailyendtime are the actual schedule times

$query1 = "insert into tblscheduling (testid, venueid, date, starttime, endtime, maximumBatch, noPerschedule, dailystarttime, dailyendtime) values (?,?,?,?,?,?,?,?,?)";
$stmt=$dbh->prepare($query1);
$result = $stmt->execute(array($tid,$vid,$tdt,'00:00:00','00:00:00',$nb,$npb,$dst,$det));

// INSERT returns affected rows, not row count - check if execute was successful
if ($result && $stmt->rowCount() >= 0) {
    echo 1;
    exit();
} else {
    // Log error for debugging
    error_log("Schedule insert failed: " . print_r($stmt->errorInfo(), true));
    echo 0;
    exit();
}
?>