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
//echo "Mal. Nura"; exit;
//page title
if (!isset($_POST['tid']) || trim($_POST['tid']) == "" || trim($_POST['tid']) == 0) {
    echo -1;
    exit();
}

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid)) {
    echo -3;
    exit();
}

if (!is_test_administrator_of($testid)) {
    echo -3;
    exit();
}

$dur = clean($_POST['duration']);
$tpad = ((is_numeric($_POST['tpad']))?($_POST['tpad']):(0));
$startmode = clean($_POST['startmode']);
$qdisplay = clean($_POST['qdisplay']);
$qadmin = clean($_POST['qadmin']);
$optadmin = clean($_POST['optadmin']);
$endorsement = clean($_POST['signature']);
$status = clean($_POST['availability']);
$calc = clean($_POST['allowcalc']);
$pkey = clean($_POST['pkey']);
if (strlen($pkey) != 3) {
    //$pkey="cbt";
    echo -5;
    exit();
}

if (date_exceeded($testid, 0)) {
    // echo -2; exit(); 
    //;
}

$query = "update tbltestconfig set allow_calc=?, duration=?, startingmode=?, endorsement=?, displaymode=?, questionadministration=?, optionadministration=?, status=?, passkey =?, timepadding=? where testid=? ";
$stmt = $dbh->prepare($query);
$exec=$stmt->execute(array($calc, $dur, $startmode,$endorsement,$qdisplay,$qadmin,$optadmin,$status,$pkey,$tpad,$testid));
if (!$exec) {
    echo -4;
    exit();
}
echo 1;
exit();
?>