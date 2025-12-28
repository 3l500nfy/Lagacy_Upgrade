<?php if(!isset($_SESSION))     session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php"); 
openConnection(true);

function isRegistered($tid, $sid)
{
    $query= "select * from tbltestsubject where testid=? && subjectid=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($tid,$sid));
    if($stmt->rowCount() > 0)
        return true;
    else 
        return false;
}

function doneRegistered($tid)
{
    $query= "select * from tbltestsubject where testid=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($tid));
    if($stmt->rowCount() >0)
        return true;
    else 
        return false;
}

?>
