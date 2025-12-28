<?php
if(!isset($_SESSION)) {  session_start();}
require_once '../lib/globals.php';
require_once('testfunctions.php');
openConnection(true);

 if (!isset($_SESSION['candidateid'])) {
   echo"end";
    exit();
}




$candidateid=$_SESSION['candidateid'];
$testid=$_SESSION['testid'];
$tinfo=gettestinfo($testid);
$duration=$tinfo['duration'];
$dur=$duration*60;

$endpoint=0;

//if the testadministrator has closed the test, close the interface
if(testopened($testid)==false){
    echo"end";
    exit();
    
}


 if (!isset($_SESSION['candidateid'])) {
   echo"end";
    exit();
}

//check if the candidate has already submitted in a different browser, then close all other instance of his work
  $query="SELECT completed from  tbltimecontrol where(testid='$testid' and candidateid='$candidateid' and completed=1)";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  $numrows = $stmt->rowCount();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($numrows>0){
echo"end";
exit;
  }
 


if(isset($_POST['completion'])){
//if the candidate has completed all the questions and click on the options logout, update the timer table
$query="UPDATE tbltimecontrol set completed=1 where(testid='$testid' and candidateid='$candidateid')";
$stmt = $dbh->prepare($query);
$stmt->execute();
exit;
}

	if(isset($_POST['endpoint'])){$endpoint=$_POST['endpoint'];}

			$elapsed= timecontrol($testid,$candidateid,$waitingsecond=30);
			if($elapsed >= $dur){
			echo"end";
		
			}
			else{
			//force the end iff the counter is downto 0
			 if($endpoint=='1'){
         $query="UPDATE tbltimecontrol set completed=1 where(testid='$testid' and candidateid='$candidateid')";
         $stmt = $dbh->prepare($query);
         $stmt->execute();
			 echo"end";
			 }else{
                             //the candidate has not completed yet so return the remaining second
                           echo  $_SESSION['testinfo']['remainingsecond'];
                         }
			
			}
?>