<?php

if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");

openConnection();

$username = clean($_POST['username']);
$answer = clean($_POST['answer']);


$query = "SELECT * FROM user WHERE username = ? AND answer = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($username,$answer));

//Check whether the query was successful or not
if ($stmt->rowCount() == 1) {
    // Password recovery functionality has been disabled for security.
    // Please contact your administrator to reset your password.
    echo "Password recovery is not available. Please contact your administrator to reset your password.";
    exit();
} else {
    // Don't reveal whether username exists or not (security best practice)
    echo "If the provided information is correct, you will receive password reset instructions.";
    exit();
}
?>