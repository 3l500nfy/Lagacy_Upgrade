<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");
require_once('../../../lib/security.php');

openConnection();
$uid = $_SESSION['MEMBER_USERID'];

$tid = $_POST['testid'];
$user = $_POST['user'];
$dt = $_POST['tdt'];
$as = array();
$query = "select tblscheduling.schedulingid, tblvenue.venueid, tblvenue.name from tblvenue inner join tblscheduling on (tblvenue.venueid=tblscheduling.venueid) where tblscheduling.testid=? && tblscheduling.date=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($tid, $dt));

$query1 = "select schedulingid from tbltestinvigilator where testid=? && userid=?";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($tid, $user));

while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $as[] = $row1['schedulingid'];
}
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $vid = $row['schedulingid'];
    $vnm = $row['name'];

    if (in_array($vid, $as)) {
        echo"<label style='padding:0px; margin:0px; border-width:0px;'><input  style='padding:0px; margin:0px; border-width:0px;' type='checkbox' name='as-vn[]' checked value='$vid' /> $vnm</label><br />";
    } else {
        echo"<label style='padding:0px; margin:0px; border-width:0px;'><input style='padding:0px; margin:0px; border-width:0px;' type='checkbox' name='as-vn[]' value='$vid' /> $vnm</label><br />";
    }
}
?>