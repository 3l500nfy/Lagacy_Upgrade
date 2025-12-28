<?php if(!isset($_SESSION)) session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
openConnection();
authorize();
global $dbh;
if(!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin")))
    
{
    header("Location:".siteUrl("403.php"));
    exit();
}

if (isset($_POST['addsubmit'])) {

    $subjectid = $_POST["subj"];
    $topic = $_POST["topic"];
    if ($subjectid == '' || $topic == '') {
        echo "No subject or topic selected";
        exit();
    }
    $topic=clean($topic);
    $query = "select * from tbltopics where subjectid= ? && topicname=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($subjectid,$topic));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        echo"Topic already exists";
        exit();
    }
    $query1 = "INSERT into tbltopics (subjectid, topicname) values ( ?, ? )"; //echo $sql; exit();
    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($subjectid,$topic));
    if ($stmt1->rowCount() > 0) {
        echo "successful";
    }
    else
        echo "error while querying the database";
    exit();
}
?>
<div style="padding:20px; text-align: center;"> 
    <span><b>Topic title:</b></span> &nbsp;<input name="add_topic" id="add_topic" type="text"/><br />
    <button id="btn_add_topic" type="submit">Add</button>
</div>