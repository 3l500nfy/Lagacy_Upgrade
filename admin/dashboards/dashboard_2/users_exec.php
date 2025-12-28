<?php
if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();

$usrid=(isset($_POST['usrid'])?($_POST['usrid']):(0));
$status=isset($_POST['status']);
$tid=(isset($_POST['test'])?($_POST['test']):(0));
$as_sbj=(isset($_POST['as-sbj'])?($_POST['as-sbj']):(array()));
if($tid==0 || $usrid==0)
{
    echo"Input Error."; exit();
}
$query = "update user set enabled = ? where id=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($status, $usrid));

$query1="select * from userrole where userid=? && roleid='10'";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($usrid));

$query2="insert into userrole (userid, roleid) values ('$usrid','10')";

$query3="select * from tbltestcompositor where userid=? && testid=?";
$stmt3 = $dbh->prepare($query3);
$stmt3->execute(array($usrid,$tid));

$query4="delete from tbltestcompositor where testid=? && userid=?";

if($stmt3->rowCount()>0)
{
    $stmt4 = $dbh->prepare($query4);
    $stmt4->execute(array($tid,$usrid));
    
}
if($stmt1->rowCount()==0)
{
    $stmt2 = $dbh->prepare($query2);
    $stmt2->execute(array($usrid));
}

foreach($as_sbj as $as)
{
    if(!isset($as))
        continue;
    $query5 = "insert into tbltestcompositor values (?,?,?)";
    $stmt5 = $dbh->prepare($query5);
    $stmt5->execute(array($usrid,$tid,$as));

}

echo "Operation was successfull";
?>