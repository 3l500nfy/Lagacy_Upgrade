<?php

session_start();


require_once("../../lib/globals.php");

openConnection();

$errmsg_arr = array();

$errflag = false;

$id = clean($_POST['id']);
$permissionname = clean($_POST['permissionname']);
$permissiondesc = clean($_POST['permissiondesc']);

if ($permissionname == '') {
    $errmsg_arr[] = 'Permission Name is missing';
    $errflag = true;
}

if ($permissiondesc == '') {
    $errmsg_arr[] = 'Permission Description is missing';
    $errflag = true;
}

if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: create.php");
    exit();
}

$query = "UPDATE permission SET name = ?, description = ? WHERE id = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($permissionname,$permissiondesc,$id));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>
