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

$sbj = (isset($_POST['sbj']) ? ($_POST['sbj']) : (0));

if (isset($_POST['editsubmit'])) {

    $subjectid = $_POST["subj"];
    $topicid = $_POST["topicid"];
    $edition = (isset($_POST['edition']) ? ($_POST['edition']) : (""));

    if ($subjectid == '' || $topicid == '') {
        echo "No subject or topic selected";
        exit();
    }
    
    if (trim($edition) == '') {
        echo "Enter a new topic name";
        exit();
    }
    $topicid = clean($topicid);
    $query = "select * from tbltopics where subjectid= ? && topicname=? && topicid <> ?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($subjectid,$edition,$topicid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        echo"Topic already exists";
        exit();
    }

    $query1 = "update tbltopics set topicname=? where topicid=?"; //echo $sql; exit();
    $stmt1=$dbh->prepare($query1);
    $exec=$stmt1->execute(array($edition,$topicid));

    if ($exec) {
        echo "successful";
    }
    else
        echo "error while querying the database";
    exit();
}
?>
<div style="padding:20px; text-align: center;"> 
    <select name="edit_topic" id="edit_topic">
        <option value="">--Select topic--</option>
        <?php get_topics_as_options($sbj); ?>
    </select><br />
    <span>New topic name: </span><br />
    <input type="text" name="edition" id="edition" placeholder="New topic name" /> <br />
    <button id="btn_edit_topic" type="submit">Save Changes</button><br />
</div>
