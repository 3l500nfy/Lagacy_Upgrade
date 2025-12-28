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

if (!isset($_POST['uid'])) {
    echo 4;
    exit();
}

$uid = clean($_POST['uid']);
$query = "delete from userrole where userid=? && roleid=(select id from role where name='Test Administrator' limit 1)";
$stmt = $dbh->prepare($query);
$stmt->execute(array($uid));


if($stmt->rowCount()>0)
{
    echo 1;
    exit();
}

echo 0;
exit();
?>