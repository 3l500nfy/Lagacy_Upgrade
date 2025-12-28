<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");

if(isset($_POST['comments'])){

$comments=  trim($_POST['comments']);
//echo
if (isset($comments)){
    openConnection();
$to = "eportaldevelopment@abu.edu.ng";
$subject = "CBT user feedback";
$message =$comments;
$testid=$_POST['testid'];
$candidateid=$_POST['candidateid'];
$query="INSERT into tblfeedback (testid,candidateid,comments) values(?,?,?)";
$stmt->$dbh->prepare($query);
$stmt->execute(array($testid,$candidateid,$comments));

$from = "cbtuser@abu.edu.ng";
$headers = "From:" . $from;
mail($to,$subject,$message,$headers);

$to = "donfackkana@gmail.com";
mail($to,$subject,$message,$headers);

$to = "contactenesi@gmail.com";
mail($to,$subject,$message,$headers);

$to = "uabbadewu@gmail.com";
mail($to,$subject,$message,$headers);
//echo"$to,$subject,$message,$headers";

}
}
redirect(siteUrl("index.php"));
?>
