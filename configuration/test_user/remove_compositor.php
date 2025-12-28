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

try {
    global $dbh;
    $dbh->beginTransaction();
    
    // Delete compositor entries using prepared statement
    $sql = "DELETE FROM tbltestcompositor WHERE userid = ? AND testid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$uid, $testid]);
    
    $dbh->commit();
    echo 1;
    exit();
} catch (PDOException $e) {
    if (isset($dbh)) {
        $dbh->rollBack();
    }
    error_log("Error in remove_compositor.php: " . $e->getMessage());
    echo 0;
    exit();
}
?>