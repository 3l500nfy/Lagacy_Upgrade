<?php if(!isset($_SESSION)) session_start();
    require_once("../../lib/globals.php");
openConnection();
    $tid=$_SESSION['tid'];
    
    if(isset($_REQUEST['centreid']))
    {
        $centreid=$_REQUEST['centreid'];
            $query= "SELECT * from tblvenue WHERE centreid = $centreid && venueid IN (SELECT venueid FROM tblscheduling WHERE testid ='$tid')";
            
            //echo $sql;

    }
    else
    $query= "SELECT * from tblvenue WHERE venueid IN (SELECT venueid FROM tblscheduling WHERE testid =?)";
    echo"<option value=''>Select venue</option>";
    $stmt = $dbh->prepare($query);
    $stmt->execute($tid);

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {       
        $venueid=$row['venueid'];
        $venuename=$row['name'];
        echo "<option value='$venueid'>$venuename</option>";
    
}

?>
