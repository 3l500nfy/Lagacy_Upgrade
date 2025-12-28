<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();

$vid = $_POST['vid2'];
$edt_center = ((isset($_POST['edt-center'])) ? ($_POST['edt-center']) : (0));
$edt_venue = ((isset($_POST['edt-vn'])) ? (clean($_POST['edt-vn'])) : (""));
$edt_venue = clean($edt_venue);
$edt_loc = ((isset($_POST['edt-loc'])) ? (clean($_POST['edt-loc'])) : (""));
$edt_loc = clean($edt_loc);
$edt_cap = ((isset($_POST['edt-cap'])) ? (clean($_POST['edt-cap'])) : (""));
$edt_cap = clean($edt_cap);

if (!preg_match("~^[0-9]{1,}$~", $edt_cap) || $edt_cap == "" || $edt_loc == "" || $edt_venue == "") {
    echo 0;
    exit();
}
$query="select * from tblvenue where centreid=? && name=? && venueid <> ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($edt_center,$edt_venue,$vid));

if($stmt->rowCount() >0)
{
    echo"2"; exit();
}
$query1 = "update tblvenue set centreid=?, name=?, location=?, capacity=? where venueid=?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($edt_center,$edt_venue,$edt_loc,$edt_cap,$vid));

echo"1";
?>