<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
//require_once('../lib/security.php');
require_once("testfunctions.php");
openConnection();

$ip= getipaddress();
$mac= getmacaddress();

//$venue=$_POST['venueid'];
$venue=1;

if($venue!=""){
//echo "INSERT INTO tblvenuecomputers values('$venue','$mac','$ip')";
$query=mysql_query("INSERT INTO tblvenuecomputers values('$venue','$mac','$ip')");
$stmt = $dbh->prepare($query);
$stmt->execute();

}


?>