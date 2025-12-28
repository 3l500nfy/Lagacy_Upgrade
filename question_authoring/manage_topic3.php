<?php if(!isset($_SESSION)) session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("authoring_functions.php");
openConnection();
authorize();
if(!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin")))
{
    header("Location:".siteUrl("403.php"));
    exit();
}

$sbj=(isset($_POST['sbj'])?($_POST['sbj']):(0));

if (isset($_POST['delsubmit'])) {

    $subjectid = $_POST["subj"];
    $topicid = $_POST["topicid"];
    if ($subjectid == '' || $topicid == '') {
        echo "No subject or topic selected";
        exit();
    }
    $topicid=clean($topicid);
    $query = "update tblquestionbank set topicid =(select topicid from tbltopics where topicname='General' && subjectid = ? limit 1)";
    $stmt=$dbh->prepare($query);
    $exec=$stmt->execute(array($subjectid));

    if (!$exec) {
        echo"Database Error!";
        exit();
    }
    $query1 = "Delete from tbltopics where topicid=?"; //echo $sql; exit();
    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($topicid));

    if ($stmt1->rowCount() > 0) {
        echo "successful";
    }
    else
        echo "error while querying the database";
    exit();
}
?>
<div style="padding:20px; text-align: center;"> 
    <select name="del_topic" id="del_topic">
        <option value="">--Select topic--</option>
        <?php get_topics_as_options($sbj);?>
    </select><br />
    <button id="btn_del_topic" type="submit">Delete</button><br />
    <span style="font-style: italic; color:red;"><b>Note:</b> All questions under this topic will be moved to General topic</span>
</div>