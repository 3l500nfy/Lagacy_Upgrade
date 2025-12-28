<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();

$usrid=(isset($_POST['usrid'])?($_POST['usrid']):(0));
$status=isset($_POST['status']);
$rl=(isset($_POST['newrole'])?($_POST['newrole']):(0));
$query = "update user set enabled = ? where id=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($status, $usrid));

$query1="select * from userrole where userid=? && roleid=?";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($usrid, $rl));


$query2="insert into userrole (userid, roleid) values (?,?)";
$stmt2 = $dbh->prepare($query2);
$stmt2->execute(array($usrid, $rl));

if($stmt1->rowCount()>0)
{
    echo "User already has such role";
    exit();
}

if($stmt2->rowCount()==0)
{
    echo "Action was not completed.";
    exit();
}

echo "Action was completed successfully.";
exit();
?>