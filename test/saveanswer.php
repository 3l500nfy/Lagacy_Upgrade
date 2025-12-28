<?php
 if(!isset($_SESSION)) {
     session_start();
 }
require_once ('../lib/globals.php');
require_once('testfunctions.php');

// Validate CSRF token
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
    exit;
}

openConnection(true);
global $dbh;
$candidateid=$_SESSION['candidateid'];
$testid=$_SESSION['testid'];
$duration=$_SESSION['testinfo']['duration'];

$questionid=$_POST['question'];
$answerid=$_POST['ans'];
$query="REPLACE INTO tblscore(candidateid,testid,questionid,answerid) VALUES(?,?,?,?)";
$stmt = $dbh->prepare($query);
$stmt->execute([$candidateid, $testid, $questionid, $answerid]);

$elapsed= timecontrol($testid,$candidateid,$waitingsecond=60);
/* if($elapsed >= $duration*60){
			echo"end";			
			}
 *///get the remaining number of question
$query2="SELECT count(distinct(questionid)) as remaining from tblpresentation where(candidateid=? and testid=? and
questionid not in (SELECT questionid from tblscore where(candidateid=? and testid=?)))";
$stmt1 = $dbh->prepare($query2);
$stmt1->execute([$candidateid, $testid, $candidateid, $testid]);
$numrows = $stmt1->rowCount();
$row = $stmt1->fetch(PDO::FETCH_ASSOC);
if($numrows>0) {
    $total = $row['remaining'];
}

// Regenerate session ID after successful answer submission
session_regenerate_id(true);
// Update activity timestamp
$_SESSION['exam_last_activity'] = time();

echo $total;


?>