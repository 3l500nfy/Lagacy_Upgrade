<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
openConnection();
$query="SELECT * FROM tblfeedback order by id desc";
$stmt=$dbh->prepare($query);
$stmt->execute();
$row=$stmt->fetch(PDO::FETCH_ASSOC);

if( $stmt->rowCount() >0){
    echo"<table>";
    for($i=0; $i < $stmt->rowCount();$i++){
        $username=  $row[$i]['candidateid'];
        $comments=$row[$i]['comments'];
        echo"<tr><td>$username</td><td>$comments</td></tr>";
    }
echo"</table>";
}
?>
