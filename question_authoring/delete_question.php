<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("authoring_functions.php");
openConnection();

$qid = $_POST['qid'];
$query = "delete from tblansweroptions where questionbankid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($qid));

$query1 = "delete from tblquestionbank where questionbankid = ?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($qid));
echo "0";
exit();

?>