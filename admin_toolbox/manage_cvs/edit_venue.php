<?php
require_once("../../lib/globals.php");
openConnection();
global $dbh;
$studentid = $_REQUEST['studentid'];
$query = "SELECT * FROM student WHERE studentid = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($studentid));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    $studentid = $row['studentid'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $othernames = $row['othernames'];
}
?>

<html>
    <head>
        <title></title>
        <?php require_once("../../partials/cssimports.php") ?> 
    </head>
    <body>
         <div id="container" class="container">
            <div class ="row">
                <div class ="span12"> 
                    <h2>Edit Venue & Host</h2>
                    <br /><br />
                </div>
                <hr/>
                <div class ="span12">
                    <form action ="edit_venue_exec.php" method ="POST">
                         <table>                  
                        <tr>
                            <td>Centre ID</td>
                            <td>
                                <select name ="centre" id ="">
                                    <?php
                                    $query1 = "SELECT * FROM tblcentres";
                                    $stmt1=$dbh->prepare($query1);
                                    $stmt1->execute();

                                    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                                        $centreid = $row['centreid'];
                                        $centername = $row['name'];
                                         echo "<option value ='$centreid'>$centername</option>";
                                    }
                                    ?>                                    
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Host ID</td>
                            <td>
                                <select name ="host" id ="">
                                    <?php
                                    $query2 = "SELECT * FROM tblhost";
                                    $stmt2=$dbh->prepare($query2);
                                    $stmt2->execute();

                                    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                        $hostid = $row['hostid'];
                                         echo "<option value ='$hostid'>$hostid</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>
                                <input type ="text" name ="venue" id ="venue" height="5">
                            </td>
                        </tr>
                        <tr>
                            <td>Location</td>
                            <td>
                                <input type ="text" name ="location" id ="location">
                            </td>
                        </tr>
                        <tr>
                            <td>Capacity</td>
                            <td>
                                <input type ="text" name ="capacity" id ="capacity">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button type ="submit" name ="" class ="btn btn-primary">Save</button>
                            </td>
                        </tr>
                         <tr>
                             <td></td>
                             <td>
                                 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                 <a href="index.php">Back to Center & Venue Manager</a>
                             </td>
                         </tr> 
                    </table>
        </form>
                     </div>
    </body>
</html>