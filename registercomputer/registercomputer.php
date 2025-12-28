<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
//require_once('../lib/security.php');
require_once("../test/testfunctions.php");
openConnection();

$ip = getipaddress();
$mac = getmacaddress();

$venue = $_POST['venueid'];
$type = $_POST['type'];
//echo $venue;

if ($type == "2") {
//type 2 force replace even if computer is already register in another venue
    $query = "UPDATE tblvenuecomputers set venueid=?, computerip=? where computermac=? ";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($venue,$ip,$mac));
} else {
    $query = "select tblvenuecomputers.venueid, name from tblvenuecomputers inner join tblvenue on tblvenuecomputers.venueid= tblvenue.venueid
 where(computermac=?)";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($mac));
    if ($stmt->rowCount() > 0) {
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        $venueid = $row[0]['venueid'];
        $name = $row[0]['name'];
        if ($venueid == $venue) {
            echo"1";
        } else {
//computer already register in another venue
            echo"2";
        }
    } else {
        if ($venue != "") {
            $query = "INSERT INTO tblvenuecomputers values(?,?,?)";
            $stmt=$dbh->prepare($query);
            $stmt->execute(array($venue,$mac,$ip));
            echo"3";
        }
    }
}
?>