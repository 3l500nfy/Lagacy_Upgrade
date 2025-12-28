<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_POST['schid'])) {
    echo 5;
    exit();
}

$schid = clean($_POST['schid']);
$schedule_config = get_schedule_config_param_as_array($schid);

if (!is_test_administrator_of($schedule_config['testid'])) {
    echo 2;
    exit();
}

$now = new DateTime();
$schid_date = new DateTime($schedule_config['date'] . " " . $schedule_config['dailystarttime']);
$intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());

if ($intaval >= 0 && isset($_POST['safemode']) && $_POST['safemode']!="") {
    echo 4;
    exit();
}
if (!isset($_GET['displace'])) {
    $scheduledcount = get_candidate_scheduled_count($schid);
    if ($scheduledcount > 0) {
        echo 3;
        exit();
    }
}

if (!isset($_GET['displace']) || (isset($_GET['displace']) && clean($_GET['displace'])==0)) {
    $query1 = "delete from tblscheduling where schedulingid= ?";
}
else
{
    $query1 = "delete from tblscheduling where schedulingid= ?";
}
$query="delete from tblfaculty_schedule_mapping where schedulingid = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($schid));

$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($schid));

/*$query2 = mysql_query($sql2) or die(0);
$query = mysql_query($sql) or die(0);*/

if ($stmt1->rowCount() != 1) {
    echo 0;
    exit();
}
echo 1;
exit();
?>