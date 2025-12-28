<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!isset($_POST['tid'])) {
    echo 4;
    exit();
}
if (!isset($_POST['sectionid'])) {
    echo 4;
    exit();
}

if (!isset($_POST['sbjid'])) {
    echo 4;
    exit();
}

$sectionid=clean($_POST['sectionid']);
$testid=clean($_POST['tid']);
$sbjid= clean($_POST['sbjid']);
$safemode = ((isset($_POST['safemode']) && $_POST['safemode']==1)?(true):(false));

if (!is_test_compositor_of($testid, null, $sbjid) && !is_test_administrator_of($testid)) {
    echo 2;
    exit();
}

if(date_exceeded($testid) && $safemode==true)
{
   echo 3;
   exit();
}

$query = "delete from tbltestsection where testsectionid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($sectionid));

if ($stmt->rowCount() !=1) {
    echo 0;
    exit();
}
echo 1;
exit();
?>