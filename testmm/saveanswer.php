<?php
 if(!isset($_SESSION)) {  session_start();}
require_once '../lib/globals.php';
require_once('testfunctions.php');
openConnection(true);
global $dbh;
$candidateid=$_SESSION['candidateid'];
$testid=$_SESSION['testid'];
$duration=$_SESSION['testinfo']['duration'];

$questionid=$_POST['question'];
$answerid=$_POST['ans'];
$query="REPLACE INTO tblscore(candidateid,testid,questionid,answerid) VALUES('$candidateid','$testid','$questionid','$answerid')";
$stmt=$dbh->prepare($query);
$stmt->execute();
$elapsed= timecontrol($testid,$candidateid,$waitingsecond=60);

//get the remaining number of question
$query2="SELECT count(distinct(questionid)) as remaining from tblpresentation where(candidateid='$candidateid' and testid='$testid' and
questionid not in (SELECT questionid from tblscore where(candidateid='$candidateid' and testid='$testid')))";
$stmt1=$dbh->prepare($query2);
$stmt1->execute();
$row=$stmt1->fetch(PDO::FETCH_ASSOC);
if($stmt1-rowCount()>0) {
    $total = $row['remaining'];
//mysql_free_result($query2);
}
    echo $total;

?>