<?php


session_start();


require_once("../../lib/globals.php");

openConnection();

$errmsg_arr = array();

$errflag = false;

$permissionname = clean($_POST['permissionname']);
$permissiondesc = clean($_POST['permissiondesc']);

if ($permissionname == '') {
    $errmsg_arr[] = 'Permission name is missing';
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

$query = "INSERT INTO permission VALUES('',?, ?)";
$stmt = $dbh->prepare($query);
$stmt->execute(array($permissionname, $permissiondesc));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>
