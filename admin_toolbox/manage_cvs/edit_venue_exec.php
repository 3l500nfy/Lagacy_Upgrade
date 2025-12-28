<?php
require_once("../../lib/globals.php");
openConnection();
global $dbh;
$centreid = $_POST['centre'];
$hostid = $_POST['host'];
$venuename = $_POST['venue'];
$location = $_POST['location'];
$capacity = $_POST['capacity'];

$query = "UPDATE tblvenue SET   venue = '$venue', location = '$Location', capacity = ? WHERE venueid = ?";
$stmt=$dbh->prepare($query);
$result=$stmt->execute(array($capacity,$venueid));

if($result){
    header("Location: manage_centervenue.php");
}
?>