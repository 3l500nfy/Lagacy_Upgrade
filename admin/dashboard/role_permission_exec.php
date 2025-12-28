<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$permission = clean($_POST['permission']);
$role = clean($_POST['role']);

$arr = explode(";", $permission);

$query = "DELETE FROM rolepermission WHERE roleid = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($role));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    for ($i = 1; $i < count($arr); $i++) {
        $permission = $arr[$i];
        $query1 = "INSERT INTO rolepermission VALUES (NULL, ?, ?)";
        $stmt1 = $dbh->prepare($query1);
        $stmt1->execute(array($role, $permission));
    }

    if ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        echo "Record Updated";
    }
}
?>