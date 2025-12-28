<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_POST['tid']) || trim($_POST['tid']) == "" || trim($_POST['tid']) == 0) {
    echo -3;
    exit();
}

$testid = clean($_POST['tid']);

if (!is_test_administrator_of($testid)) {
    echo -2;
    exit();
}

$dt = clean($_POST['dt']);

if (date_exceeded($testid, 0, "highest") && isset($_POST['safemode']) && $_POST['safemode']!="") {
    echo -1;
    exit();
}

if (test_date_registered($testid, $dt)) {
    echo -4;
    exit();
}

$query = "insert into tblexamsdate (testid, date) values (?,?) ";
$stmt=$dbh->prepare($query);
$stmt->execute(array($testid,$dt));

if ($stmt->rowCount() == 0) {
    echo 0;
    exit();
}
echo 1;
exit();
?>