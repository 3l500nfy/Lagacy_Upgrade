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
    echo 7;
    exit();
}

$schid = clean($_POST['schid']);
$schedule_config=get_schedule_config_param_as_array($schid);
$tid = ((isset($_POST['tid'])) ? (intval(clean($_POST['tid']))) : (0));

if (!is_test_administrator_of($tid)) {
    echo 8;
    exit();
}

$vid = ((isset($_POST['tvenue'])) ? (intval(clean($_POST['tvenue']))) : (0));
$dst = ((isset($_POST['dailystarttime'])) ? (clean($_POST['dailystarttime'])) : ("00:00"));
$det = ((isset($_POST['dailyendtime'])) ? (clean($_POST['dailyendtime'])) : ("23:59"));
$tdt = ((isset($_POST['tdate'])) ? (clean($_POST['tdate'])) : ('0000-00-00'));
$now = new DateTime();
//check date of initial schedule
$schid_date = new DateTime($schedule_config['date'] . " " . $schedule_config['dailystarttime']);
$intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
if ($intaval > 0 && isset($_POST['safemode'])) {
    echo 9;
    exit();
}
//chedk date of new schedule
$schid_date2 = new DateTime($tdt . " " . $dst);
$intaval = ($now->getTimestamp()) - ($schid_date2->getTimestamp());
if ($intaval > 0 && isset($_POST['safemode'])) {
    echo 9;
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

$nb = ((isset($_POST['batchcount']) && clean($_POST['batchcount'])!="" && clean($_POST['batchcount'])!=0) ? (clean($_POST['batchcount'])) : (-1)); // no of batches
$npb = ((isset($_POST['noperbatch']) && clean($_POST['noperbatch']) != 0) ? (clean($_POST['noperbatch'])) : ($venuecapacity)); // no per batch
//check to make sure that the test range is at least 1hr greater than the test duration
$tparam = get_test_config_param_as_array($tid);
$dur = $tparam['duration'];

if ($nb == -1) {
    $totaldur = $dur;
} else {

    $candidcapacity = $nb * $npb;
    $candidstudent = get_candidate_scheduled_count($schid); //echo $candidstudent; exit;
    $displaced = $candidstudent - $candidcapacity;
    if (!isset($_GET['displace']) && $displaced > 0) {
        echo 6;
        exit(); //6 for possible displacement
    }

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
$query = "select * from tblscheduling where schedulingid<>? && (((dailystarttime < ? || dailystarttime = ?) && (dailyendtime > ? || dailyendtime = ?)) || ((dailystarttime < ? || dailystarttime = ?) && (dailyendtime > ? || dailyendtime = ?))) && date=? && venueid= ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($schid,$dst,$dst,$dst,$dst,$det,$det,$det,$det,$tdt,$vid));

if ($stmt->rowCount() > 0) {
    echo 5;
    exit(); // 5 for possible clash
}

//get all mapping
//Make db query

$query1 = "update tblscheduling set venueid=?, date=?, maximumBatch=?, noPerschedule=?, dailystarttime=?, dailyendtime=? where schedulingid=?";
$stmt1=$dbh->prepare($query1);
$exec=$stmt1->execute(array($vid,$tdt,$nb,$npb,$dst,$det,$schid));

if (!$exec && $stmt->rowCount() == 0) {
    echo 0;
    exit();
}

if (isset($_GET['displace'])) {
    $lim=$candidstudent-1;
    $query2 = "select * from tblcandidatestudent where scheduleid=? limit ?, ? ";
    $stmt2=$dbh->prepare($query2);
    $stmt2->execute(array($schid,$lim,$displaced));

    while ($row3 = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sid = $row3['scheduleid'];
        $cid = $row3['candidateid'];
        $subid = $row3['subjectid'];
        $query3 = "delete from tblcandidatestudent where scheduleid=? && candidateid=? && subjectid=?";
        $stmt3=$dbh->prepare($query3);
        $stmt3->execute(array($sid,$cid,$subid));
    }
}
echo 1;
exit()
?>