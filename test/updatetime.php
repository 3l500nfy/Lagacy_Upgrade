<?php
if(!isset($_SESSION)) {  session_start();}
require_once '../lib/globals.php';
require_once('testfunctions.php');

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['exam_csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['exam_csrf_token']) {
        exit;
    }
}

// Check exam session timeout (2 hours of inactivity)
$exam_timeout = 7200; // 2 hours in seconds
if (isset($_SESSION['exam_last_activity']) && 
    (time() - $_SESSION['exam_last_activity']) > $exam_timeout) {
    // Session timeout - invalidate exam session safely
    unset($_SESSION['candidateid']);
    unset($_SESSION['testid']);
    unset($_SESSION['seequestion']);
    unset($_SESSION['biodata']);
    unset($_SESSION['testinfo']);
    unset($_SESSION['exam_csrf_token']);
    unset($_SESSION['exam_last_activity']);
    echo "end";
    exit;
}

openConnection(true);

 if (!isset($_SESSION['candidateid'])) {
   echo"end";
    exit();
}




$candidateid=$_SESSION['candidateid'];
$testid=$_SESSION['testid'];

// Update last activity timestamp
$_SESSION['exam_last_activity'] = time();

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
  $query="SELECT completed from  tbltimecontrol where(testid=? and candidateid=? and completed=1)";
  $stmt = $dbh->prepare($query);
  $stmt->execute([$testid, $candidateid]);
  $numrows = $stmt->rowCount();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($numrows>0){
echo"end";
exit;
  }
 


if(isset($_POST['completion'])){
//if the candidate has completed all the questions and click on the options logout, update the timer table
$query="UPDATE tbltimecontrol set completed=1 where(testid=? and candidateid=?)";
$stmt = $dbh->prepare($query);
$stmt->execute([$testid, $candidateid]);

// Regenerate session ID before invalidation
session_regenerate_id(true);

// Invalidate exam-related session data only (preserve other session data)
unset($_SESSION['candidateid']);
unset($_SESSION['testid']);
unset($_SESSION['seequestion']);
unset($_SESSION['biodata']);
unset($_SESSION['testinfo']);
unset($_SESSION['exam_csrf_token']);
unset($_SESSION['exam_last_activity']);

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
         $query="UPDATE tbltimecontrol set completed=1 where(testid=? and candidateid=?)";
         $stmt = $dbh->prepare($query);
         $stmt->execute([$testid, $candidateid]);
         
         // Regenerate session ID before invalidation
         session_regenerate_id(true);
         
         // Invalidate exam-related session data only (preserve other session data)
         unset($_SESSION['candidateid']);
         unset($_SESSION['testid']);
         unset($_SESSION['seequestion']);
         unset($_SESSION['biodata']);
         unset($_SESSION['testinfo']);
         unset($_SESSION['exam_csrf_token']);
         unset($_SESSION['exam_last_activity']);
         
			 echo"end";
			 }else{
                             //the candidate has not completed yet so return the remaining second
                           echo  $_SESSION['testinfo']['remainingsecond'];
                         }
			
			}
?>