<?php
session_start();
require_once("../../lib/globals.php");
openConnection();
$message;
$subjectid = $_REQUEST['subjectid'];

    $query = "DELETE FROM  tblsubject WHERE subjectid=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($subjectid));

    header("Location: index.php");
?>