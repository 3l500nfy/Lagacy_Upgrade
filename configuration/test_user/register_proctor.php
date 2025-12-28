<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_POST['tid'])) {
    echo 4;
    exit();
}
if (!isset($_POST['uid'])) {
    echo 4;
    exit();
}
if (!isset($_POST['schdid'])) {
    echo 4;
    exit();
}

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid)) {
    echo 2;
    exit();
}

$uid = clean($_POST['uid']);
$schdid = $_POST['schdid'];

$endkey= clean($_POST['end-key']);
if(strlen($endkey)==0 || strlen($endkey)>3)
{
    echo"4";
    exit();
}

$query = "insert into tbltestinvigilator values (?,?,?,?)";
$stmt=$dbh->prepare($query);
$stmt->execute(array($uid,$testid,$schdid,$endkey));

if ($stmt->rowCount() == 1) {
    echo 1;
    exit();
} else {
    echo 0;
    exit();
}
?>