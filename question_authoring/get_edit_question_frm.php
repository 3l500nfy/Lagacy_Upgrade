<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("authoring_functions.php");
openConnection();
global $dbh;

$qid = $_POST['qid'];
$query = "select * from tblquestionbank where questionbankid = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($qid));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$query1 = "select * from tblansweroptions where questionbankid=?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($qid));

$sbjid = $row['subjectid'];
$topicid = $row['topicid'];
$title = $row['title'];
$dlvl = $row['difficultylevel'];
$status = $row['active'];
?>

<form id="edit_frm" method="POST" action="commit_question_editing.php" target="editingframe">
    <b>Topic:</b> <select name="edit_topic"><?php get_topics_as_options($sbjid, $topicid, true); ?></select> &nbsp; 
    <b>Difficulty:</b> <select name="dlvl"><option value="simple">Simple</option><option value="difficult" <?php echo(($dlvl == "difficult") ? ("selected") : ("")); ?>>Difficult</option><option value="moredifficult" <?php echo(($dlvl == "moredifficult") ? ("selected") : ("")); ?>>More difficult</option></select> &nbsp; 
    <b>Status:</b> <select name="edit_status" style="width:100px;"><option value="true" >Active</option><option value="false" <?php echo(($status == "false") ? ("selected") : ("")); ?>>Dormant</option></select> &nbsp; 
    <hr />
    <iframe src="commit_question_editing.php" name="editingframe" style="width:100%; padding:0px; margin:0px; height: 50px; border-style:none; border-width: 0px;">
    
    </iframe>    
    <h3>Question:</h3>
    <input type="hidden" name="qid2" value="<?php echo $qid; ?>"/>
    <input type="hidden" name="sbj2" value="<?php echo $sbjid; ?>"/>
    <textarea name="edit_q" style="width:100%"><?php echo stripslashes($title); ?></textarea>
    <hr />
    <h3>Options:</h3>
    <?php
    $i = 0;
    while ($row2 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        $i++;
        $correctness = $row2['correctness'];
        $test = $row2['test'];
        $optid = $row2['answerid'];
        echo"<input type='hidden' name='opti$i' value='$optid' /><textarea name='opt$i'>".stripslashes($test)."</textarea>&nbsp;<label style='float:right;'><input type='radio' name='corr[]' value='$i' " . (($correctness == 1) ? ("checked") : ("")) . " style='padding:0px; margin: 0px;' /> Set this option as the correct answer</label>
        <br /><br />";
    }
    if($i<5){
    for($i=$i+1; $i<=5; $i++)
    {
        echo"<textarea name='optt$i'></textarea>&nbsp;<label style='float:right;'><input type='radio' name='corr[]' value='$i' style='padding:0px; margin: 0px;' /> Set this option as the correct answer</label>
    
        <br /><br />";
    }
    }
    ?>
    <hr />
    <b>When Saving... </b>
    <select id="savemode" name="savemode">
        <?php
        $query2 = 'select * from tbltestquestion where questionbankid=?';
        $stm2=$dbh->prepare($query2);
        $stm2->execute(array($qid));

        if ($stm2->rowCount() == 0) {
            echo'<option value="replace">Replace original </option>';
        }
        ?>
        <option value="new">Save as new</option>
    </select>
    <br />
    <input type="submit" name="submit_edit" id="submit_edit" value="Apply" />
</form>