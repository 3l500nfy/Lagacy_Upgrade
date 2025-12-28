<?php
 require_once("../../lib/globals.php");
    openConnection();

$candidatetypeid = $_REQUEST['candidatetypeid'];

$query = "DELETE FROM tblcandidatetypes WHERE candidatetypeid =?";
$stmt=$dbh->prepare($query);
$result=$stmt->execute(array($candidatetypeid));
if($result){
    header("Location: index.php");
}
?>