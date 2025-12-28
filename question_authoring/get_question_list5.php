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
    $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " &&  author=" . $_SESSION['MEMBER_USERID'] . " order by questiontime desc";
    $query1 = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && author <> " . $_SESSION['MEMBER_USERID'] . " order by questiontime desc";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($sbj));

    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($sbj));
} else {
    $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && difficultylevel = '$dlevel' && author=" . $_SESSION['MEMBER_USERID'] . " order by questiontime desc";
    $query1 = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && difficultylevel = '$dlevel' && author <> " . $_SESSION['MEMBER_USERID'] . " order by questiontime desc";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($sbj));

    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($sbj));
}

if ($stmt->rowCount() == 0 && $stmt1->rowCount() == 0) {
    echo "No question found.";
    exit();
}
echo"<form name='mapfrm' id='mapfrm' >";
echo"<fieldset><legend>Select new subject to assign selected questions:</legend>
   <b>Subject Category:</b>
                           <select name='subjcat2' id='subjcat2'>
                                                    <option value=''>--select--</option>
                                                    <option value='3'>O'Level</option>
                                                    <option value='1'>Regular</option>
                                                    <option value='2'>SBRS</option>
                                                </select> 
<b>New subject : </b> <select name='new_subject' id='new_subject'>";
get_subject_as_options($subjcat = "", $subj = "");
echo"</select>";
    ?>
<b>Topic: </b><select id="topic2" name="topic2">
    <option value="">--Select topic--</option> 
</select>

    <?php
   echo "</fieldset>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $q = stripslashes($row['title']);
    $qid = $row['questionbankid'];
    echo "<div class='qdiv'><label> <input style='padding:0px; margin:0px; border-width:0px;' type='checkbox' value='$qid' name='qsel[]' /> <div style='display:inline-block'>" . html_entity_decode($q, ENT_QUOTES) . "</div> </label></div>";
}
while ($row2 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $q = stripslashes($row2['title']);
    $qid = $row2['questionbankid'];
    echo "<div class='qdiv'><label> <input  style='padding:0px; margin:0px; border-width:0px;' type='checkbox' value='$qid' name='qsel[]' /> <div style='display:inline-block'>" . html_entity_decode($q, ENT_QUOTES) . "</div> </label></div>";
}
echo"<input type='submit' name='map' id='map' value='Apply'/></form>";
?>