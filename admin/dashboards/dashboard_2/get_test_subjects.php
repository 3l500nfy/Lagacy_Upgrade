<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();
$uid = $_SESSION['MEMBER_USERID'];

$tid=$_POST['testid'];
$user=$_POST['user'];
$as=array();
$query="select tbltestsubject.subjectid, tblsubject.subjectcode, tblsubject.subjectname from tbltestsubject inner join tblsubject
    on (tbltestsubject.subjectid=tblsubject.subjectid) where tbltestsubject.testid=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($tid));


$row = $stmt->fetch(PDO::FETCH_ASSOC);


$query1="select subjectid from tbltestcompositor where testid=? && userid=?";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($tid,$user));



while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC))
{
    $as[]=$row1['subjectid'];
}
while ($row = $stmt->fetch(PDO::FETCH_ASSOC);)
{
    $sbjid=$row['subjectid'];
    $sbjnm=$row['subjectname'];
    $sbjcode=$row['subjectcode'];
    if(in_array($sbjid, $as))
    {
        echo"<label><input type='checkbox' name='as-sbj[]' checked value='$sbjid' /> $sbjnm</label><br />";
    }
 else {
        echo"<label><input type='checkbox' name='as-sbj[]' value='$sbjid' /> $sbjnm</label><br />";
    }
}
?>