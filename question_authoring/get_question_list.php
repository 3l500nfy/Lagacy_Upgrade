<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("authoring_functions.php");
openConnection();

$sbj = $_POST['sbj'];
$topicid = $_POST['topicid'];
$dlevel = $_POST['dlevel'];
if ($dlevel == "all") {
    $query = "select * from tblquestionbank where subjectid=? ".(($topicid!="")?("&& topicid='$topicid'"):(""))." &&  author=" . $_SESSION['MEMBER_USERID']." order by questiontime desc";
    $query1 = "select * from tblquestionbank where subjectid=? ".(($topicid!="")?("&& topicid='$topicid'"):(""))." && author <> " . $_SESSION['MEMBER_USERID']." order by questiontime desc";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($sbj));

    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($sbj));
} else {
    $query = "select * from tblquestionbank where subjectid=? ".(($topicid!="")?("&& topicid='$topicid'"):(""))." && difficultylevel = '$dlevel' && author=" . $_SESSION['MEMBER_USERID']." order by questiontime desc";
    $query1 = "select * from tblquestionbank where subjectid=? ".(($topicid!="")?("&& topicid='$topicid'"):(""))." && difficultylevel = '$dlevel' && author <> " . $_SESSION['MEMBER_USERID']." order by questiontime desc";
}
$stmt=$dbh->prepare($query);
$stmt->execute(array($sbj));

$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($sbj));

if ($stmt->rowCount() == 0 && $stmt1->rowCount() == 0) {
    echo "No question found.";
    exit();
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $q = $row['title'];
    $q=stripslashes($q);
    $qid = $row['questionbankid'];
    echo "<div class='qdiv'>".html_entity_decode($q,ENT_QUOTES)."  [<a href='javascript:void(0);' class='editq' data-qid='$qid'>Edit</a>] ".((is_used($qid))?(""):("[<a href='javascript:void(0);' class='delq' data-qid='$qid'>Delete</a>]"))."</div>";
}
while ($row2 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $q = $row2['title'];
    $qid = $row2['questionbankid'];
    echo "<div class='qdiv'>".html_entity_decode($q,ENT_QUOTES)." [<a href='javascript:void(0);' class='editq' data-qid='$qid'>Edit</a>]  ".((is_used($qid))?(""):("[<a href='javascript:void(0);' class='delq' data-qid='$qid'>Delete</a>]"))."</div>";
}
?>