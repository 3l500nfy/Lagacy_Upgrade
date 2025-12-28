<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");

openConnection();
authorize();
if (!isset($_POST['uid'])) {
    echo 4;
    exit();
}

$uid = clean($_POST['uid']);

$query= "select id from role where name='Question Author'";

$stmt=$dbh->prepare($query);
$stmt->execute();

if($stmt->rowCount()!=1)
{
    echo 0;
    exit();
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$roleid=$row['id'];


$query1 = "insert into userrole (userid, roleid) values (?,?)";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($uid,$roleid));


if($stmt1->rowCount()>0)
{
    echo 1;
    exit();
}
echo 0;
exit();
?>