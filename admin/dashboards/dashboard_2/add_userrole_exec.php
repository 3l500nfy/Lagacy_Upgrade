<?php

session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();

$roleid = clean($_POST['roleid']);
$userid = clean($_POST['userid']);

$query = "INSERT IGNORE INTO userrole VALUES('',?,?)";
$stmt=$dbh->prepare($query);
$stmt->execute(array($userid,$roleid));

$query1 = "SELECT * FROM rolepermission WHERE roleid = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($roleid));

while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $permissionid = $row1['permissionid'];
    $query2 = "INSERT IGNORE INTO userpermission VALUES('', ?, ?)";
    $stmt2=$dbh->prepare($query2);
    $stmt2->execute(array($userid,$permissionid));
}
?>
