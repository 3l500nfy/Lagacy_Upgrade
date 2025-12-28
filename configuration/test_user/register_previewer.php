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

$roleid = get_role_id("Test Previewer");

try {
    global $dbh;
    
    // Add role if not exists
    if(!in_array($roleid, fetch_roleids_by_userid($uid))) {
        $sql = "INSERT INTO userrole (userid, roleid) VALUES (?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$uid, $roleid]);
    }
    
    $dbh->beginTransaction();
    
    // Delete existing previewer entries
    $sql = "DELETE FROM tblquestionpreviewer WHERE userid = ? AND testid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$uid, $testid]);
    
    // Insert new previewer entries
    $sql2 = "INSERT INTO tblquestionpreviewer (userid, testid, subjectid) VALUES (?, ?, ?)";
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
    error_log("Error in register_previewer.php: " . $e->getMessage());
    echo 0;
    exit();
}
?>