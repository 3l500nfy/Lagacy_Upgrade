
<?php
require_once("../../lib/globals.php");
openConnection();

$hostid = $_POST['hostid'];
$ipuv = $_POST['ipuv'];
$iplv = $_POST['iplv'];

$sql = "INSERT INTO tblhost VALUES('',?, ?)";
$stmt=$dbh->prepare($query);
$exec=$stmt->execute(array($ipuv,$iplv));

if($exec){
    header("Location: host.php");
}
?>

