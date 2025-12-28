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

  $new_topic=$_POST['new_topic'];
  $qids=$_POST['qsel'];
  for($i=0; $i<count($qids); $i++)
  {
      if(isset($qids[$i]))
      {
          $qid=$qids[$i];
          $query="update tblquestionbank set topicid= ? where questionbankid = ?";
          $stmt=$dbh->prepare($query);
          $stmt->execute(array($new_topic,$qid));
      }
  }
?>