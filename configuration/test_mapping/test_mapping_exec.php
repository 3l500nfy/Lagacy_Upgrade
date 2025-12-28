<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_POST['tschedule'])) {
    echo 2;
    exit();
}

$schid = clean($_POST['tschedule']);
$facs = $_POST['fac'];
$schedule_config = get_schedule_config_param_as_array($schid);

if (!is_test_administrator_of($schedule_config['testid'])) {
    echo 7;
    exit();
}


$now = new DateTime();
$schid_date = new DateTime($schedule_config['date'] . " " . $schedule_config['dailystarttime']);
$intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());

if ($intaval >= 0 && isset($_POST['safemode'])) {
    echo 6;
    exit();
}

$scheduledcount = get_candidate_scheduled_count($schid);
if ($scheduledcount > 0) {
    echo 3;
    exit();
}

$query = "delete from tblfaculty_schedule_mapping where schedulingid= ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($schid));

if (count($facs)) { 
    foreach ($facs as $fac) {
        $query1 = "insert into tblfaculty_schedule_mapping values (?, ?)";
        $stmt1=$dbh->prepare($query1);
        $stmt1->execute(array($fac,$schid));
    }
}

echo 1;
exit();
?>