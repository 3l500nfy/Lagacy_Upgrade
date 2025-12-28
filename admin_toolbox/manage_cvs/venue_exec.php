<?php

require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();

$centreid = ((isset($_POST['centre']) && $_POST['centre'] !== "") ? $_POST['centre'] : null);
$capacity = ((isset($_POST['capacity']))?(clean($_POST['capacity'])):(""));
$venuename = ((isset($_POST['venue']))?(clean($_POST['venue'])):(""));
$location = ((isset($_POST['location']))?(clean($_POST['location'])):(""));

if (!preg_match("~^[0-9]{1,}$~", $capacity) || $capacity == "" || $location == "" || $venuename == "" || $centreid === null || $centreid === "") {
    echo 0;
    exit();
}

// Verify that the centre exists
$check_centre = "SELECT centreid FROM tblcentres WHERE centreid = ?";
$stmt_check = $dbh->prepare($check_centre);
$stmt_check->execute(array($centreid));
if ($stmt_check->rowCount() == 0) {
    echo 0;
    exit();
}

$query="select * from tblvenue where centreid=? && venuename=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($centreid,$venuename));

if($stmt->rowCount() >0)
{
    echo"2"; exit();
}

$query1 = "INSERT INTO tblvenue (centreid, venuename, location, capacity) VALUES(?, ?, ?, ?)";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($centreid,$venuename,$location,$capacity));

echo "1";

?>