<?php if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();
global $dbh;
$sectid = $_POST['sectid'];
$op = $_POST['op'];
$qid = $_POST['qid'];
$ver = $_POST['ver'];
if(!has_roles(array("Test Compositor")) && !has_roles(array("Test Administrator")) )
{
    echo 0;
    exit();
}

 if($op=='add') {
     $query="insert into tbltestquestion values (?, ?, ?)";
     $stmt=$dbh->prepare($query);
     $stmt->execute(array($sectid,$qid,$ver));

     if($stmt->rowCount()>0)
         echo 1; exit;

 } else {
     $query="delete from tbltestquestion where testsectionid=? && questionbankid=? && version=?";
     $stmt=$dbh->prepare($query);
     $stmt->execute(array($sectid,$qid,$ver));

     if($stmt->rowCount() >0)
         echo 1; exit;
 }
 echo 0;
?>