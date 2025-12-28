<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();
$usrid = (isset($_POST['usrid']) ? ($_POST['usrid']) : (0));
$status = isset($_POST['status']);
$tid = (isset($_POST['test']) ? ($_POST['test']) : (0));
$dt = (isset($_POST['tdt']) ? ($_POST['tdt']) : ('0000-00-00'));
$as_vn = (isset($_POST['as-vn']) ? ($_POST['as-vn']) : (array()));
if ($tid == 0 || $usrid == 0) {
    echo"Input Error.";
    exit();
}
$query = "update user set enabled = ? where id=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($status, $usrid));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$query1 = "select * from userrole where userid=? && roleid='11'";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($usrid));

$query2 = "insert into userrole (userid, roleid) values (?,'11')";
$stmt2 = $dbh->prepare($query2);
$stmt2->execute(array($usrid));

$query3 = "select * from tbltestinvigilator inner join tblscheduling on (tblscheduling.schedulingid=tbltestinvigilator.schedulingid) where userid=? && tbltestinvigilator.testid=? && tblscheduling.date=?";
$stmt3 = $dbh->prepare($query3);
$stmt3->execute(array($usrid,$tid,$dt));

$query4 = "delete from tbltestinvigilator where testid=? && userid=? && schedulingid=";
$stmt4 = $dbh->prepare($query4);
$stmt4->execute(array($tid,$usrid));

//if user already has the role of testcompositor
if ($stmt3->rowCount() > 0) {
    $row = $stmt3->fetch(PDO::FETCH_ASSOC);
    $sch=$row['schedulingid'];
    $query4 = "delete from tbltestinvigilator where testid=? && userid=? && schedulingid=?";
    $stmt4 = $dbh->prepare($query4);
    $stmt4->execute(array($tid,$usrid,$sch));

}
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
if ($stmt->rowCount() == 0) {
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    //exit();
}

foreach ($as_vn as $as) {
    if (!isset($as))
        continue;
    $query5 = "insert into tbltestinvigilator values (?,?,?)";
    $stmt5= $dbh->prepare($query5);
    $stmt5->execute(array($usrid,$tid,$as));
}

echo "Operation was successfull";
?>