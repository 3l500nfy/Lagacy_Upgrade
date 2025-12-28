<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
//require_once('../lib/security.php');
require_once("testfunctions.php");
openConnection();
global $dbh;
$ip= getipaddress();
$mac= getmacaddress();

//$venue=$_POST['venueid'];
$venue=1;

if($venue!=""){
$query="INSERT INTO tblvenuecomputers values('$venue','$mac','$ip')";
$stmt=$dbh->prepare($query);
$stmt->execute();
}
?>