<?php if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if(!isset($_GET['tid']) || trim($_GET['tid'])==""){
    header("Location:". siteUrl("configuration/home.php"));
    exit();
}
$testid= clean($_GET['tid']);

if (!is_test_administrator_of($testid)) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$sql= "delete from tbltestconfig where testid = ?";
$stmt=$dbh->prepare($sql);
$stmt->execute(array($testid));
    header("Location:". siteUrl("configuration/home.php"));
    exit();



?>
