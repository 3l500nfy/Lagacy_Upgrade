<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$errmsg_arr = array();

$errflag = false;

$username = clean($_POST['username']);
$password = clean($_POST['password']);

if ($username == '') {
    $errmsg_arr[] = 'Username is missing';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Password is missing';
    $errflag = true;
}

if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: login.php");
    exit();
}

if (isset($username) && isset($password)) {
    if ($username == "admin" && $password == "admin") {
        $_SESSION['SESS_ADMIN_LOGGED_IN'] = true;
        session_write_close();
        header("location: dashboard.php");
        exit();
    } else {
        $_SESSION['SESS_ADMIN_INVALID_LOGIN'] = "Invalid Username/Password";
        session_write_close();
        header("location: adminlogin.php");
        exit();
    }
}
?>