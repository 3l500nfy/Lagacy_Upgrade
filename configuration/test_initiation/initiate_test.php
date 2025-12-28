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
if (!has_roles(array("Test Administrator"))) {
    echo 0;
    exit();
}
if (!isset($_POST['init-session']) || !isset($_POST['init-testcode']) || !isset($_POST['init-testtype']) || !isset($_POST['init-semester'])) {
    echo 0;
    exit();
}
$user = $_SESSION['MEMBER_USERID'];
$session = clean($_POST['init-session']);
$testcode = clean($_POST['init-testcode']);
$testtype = clean($_POST['init-testtype']);
$semester = clean($_POST['init-semester']);

// check uniqueness
$sql = "select * from tbltestconfig where session=? && semester=? && testtypeid=? && testcodeid=?";
//echo $sql;
$stmt=$dbh->prepare($sql);
$stmt->execute(array($session,$semester,$testtype,$testcode));

if ($stmt->rowCount() != 0) {
    echo 2;
    exit();
}

// Get the test name from tbltestcode
$sql = "SELECT testname FROM tbltestcode WHERE testcodeid = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($testcode));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$testname = $row['testname'];

$sql1 = "insert into tbltestconfig (session, semester, testtypeid, testcodeid, testname, duration, testcategory, totalmark, startingmode, displaymode, questionadministration, optionadministration, versions, activeversion, initiatedby, dateinitiated, status) values 
    (?,?,?,?,?,'30','Multi-Subject','100','on login','All','random','random','1','1','".$user."',now(),'0')";

$stmt1=$dbh->prepare($sql1);
$stmt1->execute(array($session,$semester,$testtype,$testcode,$testname));

if ($stmt1->rowCount()==1) {
    echo 1;
    exit();
}
else
    echo 0;
exit();
?>