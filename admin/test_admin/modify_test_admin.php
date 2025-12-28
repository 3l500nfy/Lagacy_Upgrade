<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();
authorize();
if (!has_roles(array("Super Admin"))) {
    echo 2;
    exit();
}

if (!isset($_POST['pno']) || !isset($_POST['action'])) {
    echo 4;
    exit();
}

$pno = clean($_POST['pno']);
$action = clean($_POST['action']);
if ($action == 'enable')
    $query = "update user set enabled='1' where staffno = ?";
else
    $query = "update user set enabled='0' where staffno = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($pno));

if($stmt->rowCount()>0)
{
    echo 1;
    exit();
}

echo 0;
exit();
?>