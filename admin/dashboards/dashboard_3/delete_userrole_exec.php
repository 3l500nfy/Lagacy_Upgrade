<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$roleid = clean($_POST['roleid']);
$userid = clean($_POST['userid']);

$query = "DELETE FROM userrole WHERE roleid = ? AND userid = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($roleid, $userid));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "1";
} else {
    echo "0";
}
?>