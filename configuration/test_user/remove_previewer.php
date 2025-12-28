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

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid)) {
    echo 2;
    exit();
}
if(false && date_exceeded($testid))
{
    echo 3;
    exit();
}

$uid = clean($_POST['uid']);
$roleid = get_role_id("Test Previewer");

try {
    global $dbh;
    $dbh->beginTransaction();
    
    // Delete previewer entries
    $sql = "DELETE FROM tblquestionpreviewer WHERE userid = ? AND testid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$uid, $testid]);
    
    // Check if user has any other previewer roles
    $sql2 = "SELECT COUNT(*) as count FROM tblquestionpreviewer WHERE userid = ?";
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute([$uid]);
    $result = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    // If no other previewer roles, remove the role
    if ($result['count'] == 0) {
        $sql3 = "DELETE FROM userrole WHERE userid = ? AND roleid = ?";
        $stmt3 = $dbh->prepare($sql3);
        $stmt3->execute([$uid, $roleid]);
    }
    
    $dbh->commit();
    echo 1;
    exit();
} catch (PDOException $e) {
    if (isset($dbh)) {
        $dbh->rollBack();
    }
    error_log("Error in remove_previewer.php: " . $e->getMessage());
    echo 0;
    exit();
}
?>