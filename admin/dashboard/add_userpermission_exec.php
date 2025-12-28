<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$permissionid = clean($_POST['permissionid']);
$userid = clean($_POST['userid']);

$query = "INSERT IGNORE INTO userpermission VALUES('', ?, ?)";
$stmt=$dbh->prepare($query);
$stmt->execute(array($userid,$permissionid));

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "1";
} else {
    echo "0";
}
?>
