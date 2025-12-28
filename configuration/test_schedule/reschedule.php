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

if (!isset($_POST['schd-select'])) {
    echo 5;
    exit();
}

$old_schid = clean($_POST['schid']);
$new_schid = clean($_POST['schd-select']);

//check to make user both schedule date are valid (not elapsed)
$schedule_config1 = get_schedule_config_param_as_array($old_schid);
$schedule_config2 = get_schedule_config_param_as_array($new_schid);

if (!is_test_administrator_of($schedule_config1['testid'])) {
    echo 2;
    exit();
}

$now = new DateTime();
$old_schid_date = new DateTime($schedule_config1['date'] . " " . $schedule_config1['dailystarttime']);
$new_schid_date = new DateTime($schedule_config2['date'] . " " . $schedule_config2['dailystarttime']);
$intaval1 = ($now->getTimestamp()) - ($old_schid_date->getTimestamp());
$intaval2 = ($now->getTimestamp()) - ($new_schid_date->getTimestamp());
if ($intaval1 > 0 || $intaval2 > 0 && isset($_POST['safemode'])) {
    echo 4; 
    exit();
}


//check to make sure there is still enough space for rescheduling
$scheduledcount = get_candidate_scheduled_count($old_schid);
$scheduledcount2 = get_candidate_scheduled_count($new_schid);
$schedulecapacity = get_schedule_capacity($new_schid);
$freeslot = $schedulecapacity;
if ($schedulecapacity != -1) {
    $freeslot = $schedulecapacity - $scheduledcount2;
    if ($freeslot < $scheduledcount) {
        echo 4;
        exit();
    }
}
$tid= $schedule_config1['testid'];
$testconfig=get_test_config_param_as_array($tid);
if($testconfig['testtypeid']==1){
$scheduledcount *=4;
}

//*******************************
//check the mapping (undone)
//*******************************
//PDO connection
try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, array(
                PDO::ATTR_PERSISTENT => true
            ));
} catch (PDOException $e) {

    echo 0;
    exit();
}

try {
    $dbh->beginTransaction();
    $sql = "update tblcandidatestudent set scheduleid = '$new_schid' where scheduleid='$old_schid'";
    $query = $dbh->exec($sql);
	
    if ($query != $scheduledcount) {
        $dbh->rollBack();
        echo 0;
        exit();
    }

    $sql2 = "delete from tblscheduling where schedulingid='$old_schid'";
    $query2 = $dbh->exec($sql2);
    if ($query2 != 1) {
        $dbh->rollBack();
        echo 0;
        exit();
    }

    $dbh->commit();
    echo 1;
    exit();
} catch (PDOException $e) {
    $dbh->rollBack();
    echo 0;
    exit();
}
?>