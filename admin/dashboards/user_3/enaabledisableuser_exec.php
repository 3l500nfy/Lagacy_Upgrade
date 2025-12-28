<?php
session_start();
require_once("../../lib/globals.php");

openConnection();

$str = clean($_POST['enabledisableuserctl']);

$enabledisableuserctl_arr = explode("|", $str);
$userid = $enabledisableuserctl_arr[0];
$enablestatus = $enabledisableuserctl_arr[1];

$query = "UPDATE user SET enabled = ? WHERE id = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($enablestatus,$userid));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "success";
}
else{
    echo "failure";
}
?>
