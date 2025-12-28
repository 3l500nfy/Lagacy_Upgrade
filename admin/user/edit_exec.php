<?php

session_start();


require_once("../../lib/globals.php");

openConnection();

$errmsg_arr = array();

$errflag = false;

$id = clean($_POST['id']);
$username = clean($_POST['username']);
$displayname = clean($_POST['displayname']);
$email = clean($_POST['email']);
$staffno = clean($_POST['staffno']);

if ($username == '') {
    $errmsg_arr[] = 'Username is missing';
    $errflag = true;
}

if ($displayname == '') {
    $errmsg_arr[] = 'Display name is missing';
    $errflag = true;
}

if ($email == '') {
    $errmsg_arr[] = 'Email name is missing';
    $errflag = true;
}

if ($staffno == '') {
    $errmsg_arr[] = 'Personnel No. is missing';
    $errflag = true;
}

if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: create.php");
    exit();
}

$query = "UPDATE user SET username  = ?,
                        displayname = ?,
                        email = ?,
                        staffno = ?
                        WHERE id = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($username,$displayname,$email,$staffno,$id));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>
