<?php if(!isset($_SESSION)) session_start ();
    require_once("../../lib/globals.php");
    require_once("../../lib/security.php");
    openConnection();

$name = clean($_POST['name']);
if($name=="")
{
    echo"0"; exit();
}

$query="select * from tblcentres where centrename=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($name));

if($stmt->rowCount()  >0)
{
    echo"2"; exit();
}
$query1 = "INSERT INTO tblcentres (centrename) VALUES (?)";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($name));

echo"1";
?>
