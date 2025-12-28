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
    $query = "select * from tblquestionbank where subjectid=? ".(($topicid!="")?("&& topicid='$topicid'"):(""))." && difficultylevel = ? && author=" . $_SESSION['MEMBER_USERID']." order by questiontime desc";
    $query1 = "select * from tblquestionbank where subjectid=? ".(($topicid!="")?("&& topicid='$topicid'"):(""))." && difficultylevel = ? && author <> " . $_SESSION['MEMBER_USERID']." order by questiontime desc";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($sbj,$dlevel));

    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($sbj,$dlevel));
}

if ($stmt->rowCount() == 0 && $stmt1->rowCount() == 0) {
    echo "No question found.";
    exit();
}
echo"<form name='mapfrm' id='mapfrm' >";
echo"<b>Difficulty level to assign to selected questions: </b> <select name='new_dlvl' id='new_dlvl'><option value='simple' ".(($dlevel=='simple')?('selected'):(''))." >Simple</option>
    <option value='difficult' ".(($dlevel=='difficult')?('selected'):(''))." >Difficult</option><option value='moredifficult' ".(($dlevel=='moredifficult')?('selected'):(''))." >More Difficult</option></select>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $q = stripslashes($row['title']);
    $qid = $row['questionbankid'];
    echo "<div class='qdiv'><label> <input style='padding:0px; margin:0px; border-width:0px;' type='checkbox' value='$qid' name='qsel[]' /> <div style='display:inline-block'>".html_entity_decode($q,ENT_QUOTES)."</div> </label></div>";
}
while ($row2 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $q = stripslashes($row2['title']);
    $qid = $row2['questionbankid'];
    echo "<div class='qdiv'><label> <input  style='padding:0px; margin:0px; border-width:0px;' type='checkbox' value='$qid' name='qsel[]' /> <div style='display:inline-block'>".html_entity_decode($q,ENT_QUOTES)."</div> </label></div>";
}
echo"<input type='submit' name='map' id='map' value='Apply'/></form>";
?>