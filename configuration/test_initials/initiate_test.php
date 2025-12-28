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
if (!isset($_POST['init-session']) || !isset($_POST['init-testcode']) || !isset($_POST['init-testtype']) || !isset($_POST['init-semester'])) {
    echo 0;
    exit();
}
//echo $_POST['init-semester'];
$user = $_SESSION['MEMBER_USERID'];
$session = clean($_POST['init-session']);
$testcode = clean($_POST['init-testcode']);
$testtype = clean($_POST['init-testtype']);
$semester = clean($_POST['init-semester']);

$sql = "select testid from tbltestconfig where session=? && semester=? && testtypeid=? && testcodeid=?";
//echo $sql; exit();
$stmt=$dbh->prepare($sql);
$stmt->execute(array($session,$semester,$testtype,$testcode));

if ($stmt->rowCount() == 1) {
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['testid'];
    exit();
}
else
    echo -1;
exit();
?>