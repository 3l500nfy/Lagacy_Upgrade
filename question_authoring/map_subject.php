<?php
if (!isset($_SESSION))
    session_start();
?>
<!DOCTYPE html>
<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("authoring_functions.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$new_subject = $_POST['new_subject'];
$qids = $_POST['qsel'];

$sql = "select topicid from tbltopics where subjectid=? and topicname='General'";
$stmt=$dbh->prepare($query);
$stmt->execute(array($new_subject));

if ($stmt->rowCount() == 0) {
    exit();
}

$topicid = $stmt->fetch(PDO::FETCH_ASSOC)[0];

for ($i = 0; $i < count($qids); $i++) {
    if (isset($qids[$i])) {
        $qid = $qids[$i];
        $query = "update tblquestionbank set subjectid= ?, topicid=? where questionbankid = ?";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($new_subject,$topicid,$qid));
    }
}
?>