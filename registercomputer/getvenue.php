<?php if(!isset($_SESSION)) session_start();
    require_once("../lib/globals.php");
openConnection();

$centreid=$_REQUEST['centreid'];

$query = "SELECT * from tblvenue WHERE centreid = ?";
$stmt=$dbh->prepare($query);

echo"<option value=''>Select venue</option>";
$stmt->execute(array($centreid));
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $venueid=$row['venueid'];
        $venuename=$row['name'];
        echo "<option value='$venueid'>$venuename</option>";
    
}
?>