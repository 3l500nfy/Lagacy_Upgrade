<?php


if (!isset($_SESSION)) session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$permissionid = clean($_POST['permissionid']);
$userid = clean($_POST['userid']);

$query = "DELETE FROM userpermission WHERE permissionid = ? AND userid = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($permissionid, $userid));


if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "1";
} else {
    echo "0";
}
?>