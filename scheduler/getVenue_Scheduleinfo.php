<?php if(!isset($_SESSION)) session_start();
    require_once("../lib/globals.php");
openConnection();
    $tid=$_SESSION['tid'];
    
    if(isset($_REQUEST['centreid']))
    {
        $centreid=$_REQUEST['centreid'];
            $sql= "SELECT * from tblvenue WHERE centreid = $centreid && venueid in (select venueid from tblscheduling where testid ='$tid')";
            //echo $sql;

    }
    else
    $query= "SELECT * from tblvenue WHERE venueid in (select venueid from tblscheduling where testid ='?')";
    echo"<option value=''>Select venue</option>";
    $stmt = $dbh->prepare($query);
    $stmt->execute($tid);
    $numrows = $stmt->rowCount();
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {       
        $venueid=$row['venueid'];
        $venuename=$row['name'];
        echo "<option value='$venueid'>$venuename</option>";
    
}

?>
