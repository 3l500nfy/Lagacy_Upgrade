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
if (!isset($_POST['tsbj'])) {
    echo 4;
    exit();
}

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid))
{
    echo 2;
    exit();
}

$uid = clean($_POST['uid']);
$tsbj = $_POST['tsbj'];
if (count($tsbj) == 0) {
    echo 5;
    exit();
}

try {
    global $dbh;
    $dbh->beginTransaction();
    
    // Delete existing compositor entries
    $sql = "DELETE FROM tbltestcompositor WHERE userid = ? AND testid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$uid, $testid]);
    
    // Insert new compositor entries
    $sql2 = "INSERT INTO tbltestcompositor (userid, testid, subjectid) VALUES (?, ?, ?)";
    $stmt2 = $dbh->prepare($sql2);
    
    foreach ($tsbj as $sbj) {
        $stmt2->execute([$uid, $testid, $sbj]);
    }
    
    $dbh->commit();
    echo 1;
    exit();
} catch (PDOException $e) {
    if (isset($dbh)) {
        $dbh->rollBack();
    }
    error_log("Error in register_compositor.php: " . $e->getMessage());
    echo 0;
    exit();
}
?>