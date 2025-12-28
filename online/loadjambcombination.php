<?php
require_once '../lib/globals.php';

openConnection(true);
$programmeid = $_POST['programmeid'];
$result = "SELECT requirement, name FROM tblentrycombination inner join tblprogramme on 
    tblentrycombination.programmeid = tblprogramme.programmeid WHERE tblprogramme.programmeid = ?";
$stmt = $dbh->prepare($result);
$stmt->execute(array($programmeid));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($stmt->rowCount() > 0){
    $req = $row['requirement'];
    $name = $row['name'];
    
    echo "<h4>Admission into $name requires the following:<br><i> $req</i><h4>";
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
