<?php
 require_once("../../lib/globals.php");
    openConnection();

$candidatetypeid = $_POST['candidatetypeid'];
$candidatetype = $_POST['candidatetype'];


$query = "UPDATE tblcandidatetypes SET candidatetype =? WHERE candidatetypeid =?";
$stmt=$dbh->prepare($query);
$result=$stmt->execute(array($candidatetype,$candidatetypeid));
if($result){
    header("Location: index.php");
}
?>