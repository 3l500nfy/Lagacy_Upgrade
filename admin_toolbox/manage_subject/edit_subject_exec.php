<?php

require_once("../../lib/globals.php");
openConnection();

$message;

$subjectid = $_POST['subjectid'];
$oldsubjcode =$_POST['subjectcode'];
$subject = $_POST['subj'];
$subjectc = $_POST['subjcode'];
$subjcat = $_POST['subjcat'];

$subjectc = strtoupper(strtolower(trim($subjectc)));
$subject = ucwords(strtolower(trim($subject)));

$subjectcategory = clean($subjcat);

if ($subject != "" && $subjectc != "") {
$query = "SELECT * from tblsubject 
        where(subjectcode=? and subjectcategory=?
        and subjectid <> ? )";
$stmt=$dbh->prepare($query);
$stmt->execute(array($subjectc,$subjectcategory,$subjectid));

if($stmt->rowCount() <=0){

   //get the former code name

    $query = "UPDATE tblsubject SET subjectcode=?,subjectcategory=?, subjectname=? WHERE subjectid=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($subjectc,$subjectcategory,$subject,$subjectid));

    $query1 = "UPDATE tbltestcode SET testname=? WHERE testname=?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($subjectc,$oldsubjcode));

}
else {
$messages = "The specified subject already exist.";
}

}
else
{
    $messages = "Some fields are empty.";
}
header("Location: index.php");

?>