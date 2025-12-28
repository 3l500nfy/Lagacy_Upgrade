<?php

if (!isset($_SESSION))
session_start();

require_once("../lib/globals.php");

require_once('../lib/security.php');
require_once("testfunctions.php");

openConnection();
global $dbh;
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']))) {
    redirect(siteUrl("test/login.php"));
}

if (isset($_POST['testid'])){
    $_SESSION['testid']=$_POST['testid'];
    redirect(siteUrl('test/index.php'));
    exit();
}


?>