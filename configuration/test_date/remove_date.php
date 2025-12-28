<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_POST['tid']) || trim($_POST['tid']) == "" || trim($_POST['tid']) == 0) {
    echo -3;
    exit();
}

$displace = 0;
if (isset($_GET['displace'])) {
    if (clean($_GET['displace']) == 1)
        $displace = 1;
    else
        $displace = 0;
}

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid)) {
    echo -2;
    exit();
}

$dt = clean($_POST['dt']);

$now = new DateTime();
$tdt = new DateTime($dt . $now->format(" H:i:s"));
if (($tdt->getTimestamp()) - ($now->getTimestamp()) < 0 && isset($_POST['safemode']) && $_POST['safemode']!="") {
    echo -1;
    exit();
}

if (!test_date_registered($testid, $dt)) {
    echo -4;
    exit();
}

$schedulecount = candidate_schedule_count($testid, $dt);
if ($displace == 0 && $schedulecount > 0) {
    echo -5;
    exit();
}

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

    $sql = "delete from tblcandidatestudent where scheduleid in (select schedulingid from tblscheduling where testid= '$testid' && date ='$dt')";
    $query = $dbh->exec($sql);
    if ($query < 1 && $query !== 0) {
        $dbh->rollBack();
        echo 0;
        exit();
    }

    $sql4 = "delete from tblfaculty_schedule_mapping where schedulingid in (select schedulingid from tblscheduling where testid= '$testid' && date ='$dt')";
    $query4 = $dbh->exec($sql4);
    if ($query4 < 1 && $query4 !== 0) {
        $dbh->rollBack();
        echo 0;
        exit();
    }

    $sql2 = "delete from tblscheduling where testid='$testid' && date = '$dt'";
    $query2 = $dbh->exec($sql2);
    if ($query2 < 1 && $query2 !== 0) {
        $dbh->rollBack();
        echo 0;
        exit();
    }


    $sql3 = "delete from tblexamsdate where testid='$testid' && date = '$dt'";
    $query3 = $dbh->exec($sql3);
    if ($query3 != 1 && $query3 !== 0) {
        $dbh->rollBack();
        echo 0;
        exit();
    }


    $dbh->commit();
    echo 1;
} catch (PDOException $e) {
    $dbh->rollBack();
    echo 0;
    exit();
}
?>