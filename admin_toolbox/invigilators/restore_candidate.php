<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
openConnection();

$candidatetype = $_POST['candidatetype'];
$examtype = $_POST['testid'];
$username = $_POST['username'];

// First get the candidate IDs for this registration number and test
$query1 = "SELECT sc.candidateid FROM tblscheduledcandidate sc 
           INNER JOIN tblcandidatestudent cs ON sc.candidateid = cs.candidateid
           INNER JOIN tblscheduling s ON cs.scheduleid = s.schedulingid
           WHERE sc.candidatetype = ? AND sc.RegNo = ? AND s.testid = ?";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($candidatetype, $username, $examtype));
$candidateids = $stmt1->fetchAll(PDO::FETCH_COLUMN);

if (!empty($candidateids)) {
    // Now update all matching records
    $placeholders = str_repeat('?,', count($candidateids) - 1) . '?';
    $query2 = "UPDATE tbltimecontrol 
               SET completed='0', ip='', elapsed=0, starttime=NULL, curenttime=NULL 
               WHERE candidateid IN ($placeholders) AND testid = ?";
    
    $params = array_merge($candidateids, array($examtype));
    $stmt2 = $dbh->prepare($query2);
    $stmt2->execute($params);
}

header('Location:index.php');

?>