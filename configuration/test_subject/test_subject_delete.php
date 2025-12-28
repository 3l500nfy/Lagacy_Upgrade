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
if (!isset($_POST['sbjid']) || !isset($_POST['tid'])) {
    echo 5;
    exit();
}

$sbjid = clean($_POST['sbjid']);
$tid = clean($_POST['tid']);

if (!is_test_administrator_of($tid)) {
    echo 2;
    exit();
}

if(date_exceeded($tid) && isset($_POST["safemode"]) && $_POST['safemode']!="") 
{
    echo 4;
    exit();
}

$test_schedules = get_test_schedule_as_array($tid);
$test_schedules[]=0;
$test_schedules_str = trim(implode(",", $test_schedules), ",");

$candidlist=array();
$candidlist[]=0;
$query = "select distinct candidateid from tblcandidatestudent where subjectid = ? && scheduleid in (?)";
$stmt=$dbh->prepare($query);
$stmt->execute(array($sbjid,$test_schedules_str));

if ($stmt->rowCount() >0) {
    if (!isset($_GET['displace']) || clean($_GET['displace']) == 0) {
        echo 3;
        exit();
    }
}
else
{
    
    while($row=  $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $candidlist[]=$row['candidateid'];
    }
    
}
$candidlist_str=trim(implode(",",$candidlist)," ,");

//queries to execute

$query1 = "delete from tbltestsubject where subjectid=? && testid=?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($sbjid,$tid));

$query2 = "delete from tblcandidatestudent where subjectid=? && scheduleid in (?)";

$query3 = "delete from tblcandidatestudent where candidateid in (?)";

//PDO connection
/*try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, array(
                PDO::ATTR_PERSISTENT => true
            ));
} catch (PDOException $e) {

    echo 0;
    exit();
}*/

try {
    $dbh->beginTransaction();
    $action = ((isset($_GET['displace'])) ? (clean($_GET['displace'])) : (0));
    if ($action == 0) {// ignore affected candidates
        $stmt2=$dbh->prepare($query2);
        $query2=$stmt2->execute(array($sbjid,$test_schedules_str));
       // $query2 = $dbh->exec($sql2);
        if ($query2 < 1) {
            $dbh->rollBack();
            echo 0;
            exit();
        }
    } else
    if ($action == 1) {
        $stmt3=$dbh->prepare($query3);
        $query3=$stmt3->execute(array($candidlist_str));
        //$query3 = $dbh->exec($sql3);
        if ($query3 < 1 && $query3 !== 0) {
            $dbh->rollBack();
            echo 0;
            exit();
        }
        $stmt2=$dbh->prepare($query2);
        $query2=$stmt2->execute(array($sbjid,$test_schedules_str));
        //$query2 = $dbh->exec($sql2);
        if ($query2 < 1) {
            $dbh->rollBack();
            echo 0;
            exit();
        }
    } else
    if ($action == 2) {

        $query4 = $dbh->exec($sql4);
        if ($query4 < 1 && $query4 !== 0) {
            $dbh->rollBack();
            echo 0;
            exit();
        }
        $stmt2=$dbh->prepare($query2);
        $query2=$stmt2->execute(array($sbjid,$test_schedules_str));
        //$query2 = $dbh->exec($sql2);
        if ($query2 < 1) {
            $dbh->rollBack();
            echo 0;
            exit();
        }
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