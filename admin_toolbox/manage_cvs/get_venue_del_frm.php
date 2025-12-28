<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();

$vid = $_POST['vid'];

$query = "delete from tblvenue where venueid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($vid));

$query1="delete from tblvenuecomputers where venueid=?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($vid));
echo"1";
?>