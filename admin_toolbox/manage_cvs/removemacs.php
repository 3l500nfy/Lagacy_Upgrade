<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();

$vid = $_POST['vid2'];
$macs=((isset($_POST['mcaddr']))?($_POST['mcaddr']):(array()));

for($i=0; $i<count($macs); $i++)
{
    $mac=$macs[$i];

    $query = "delete from tblvenuecomputers where venueid=? && computermac=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($vid,$mac));
}
echo"Operation was successfull";
?>