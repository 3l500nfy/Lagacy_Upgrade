<?php


session_start();


require_once("../../lib/globals.php");

openConnection();

$errmsg_arr = array();

$errflag = false;

$id = clean($_POST['id']);
$rolename = clean($_POST['rolename']);
$roledesc = clean($_POST['roledesc']);

if ($rolename == '') {
    $errmsg_arr[] = 'Role Name is missing';
    $errflag = true;
}

if ($roledesc == '') {
    $errmsg_arr[] = 'Role Description is missing';
    $errflag = true;
}

if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: create.php");
    exit();
}

$query = "UPDATE role SET name = ?, description = ? WHERE id = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($rolename,$roledesc,$id));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>
