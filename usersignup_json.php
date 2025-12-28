<?php
if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");

openConnection();

$userid = $_POST['userid'];
$sql = "SELECT * FROM user WHERE id = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($userid));

$row = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $row['username'];
$password = $row['password'];
$displayname = $row['displayname'];
$email = $row['email'];
$pnumber = $row['staffno'];

$user_object = array("userid" => $userid, "username" => $username, "password" => $password,"displayname" => $displayname,"email" => $email,"staffno" => $staffno);
echo json_encode($user_object);

?>