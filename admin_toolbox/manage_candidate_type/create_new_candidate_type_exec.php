<?php

 require_once("../../lib/globals.php");
    openConnection();
$candidatetypename = $_POST['candidatetypename'];

$query = "INSERT INTO tblcandidatetypes VALUES( ' ', ?)";
$stmt=$dbh->prepare($query);
$result=$stmt->execute(array($candidatetypename));

if($result){
    header("Location: index.php");
}
?>