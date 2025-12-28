<?php

require_once("lib/globals.php");

openConnection();

$username = clean($_POST['username']);

$query = "SELECT * FROM user WHERE username='$username'";
$stmt=$dbh->prepare($query);
$stmt->execute(array($username));

//Check whether the query was successful or not
if ($result) {
    if ($stmt->rowCOunt() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $row['question'];
    }
}
?>