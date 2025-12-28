<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
openConnection();
authorize();
if(!has_roles(array("Super Admin")) && !has_roles(array("PC Registrar")))
{
    echo"Sorry you don't have permission to work on this.";
    exit();
}
?>
<div>
    <form action="#" method="post">
        <table>                  
            <tr>
                <td>Centre </td>
                <td>
                    <select name ="centrename" id ="centreid">
                        <option value ="$venueid">--Select Centre--</option>
                        <?php
                        $query = "SELECT * FROM tblcentres";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $centreid = $row['centreid'];
                            $centername = $row['centrename'];
                            echo "<option value ='$centreid'>$centername</option>";
                        }
                        ?>                                    
                    </select>
                </td>
            </tr>
            <tr>
                <td>Venue </td>
                <td>
                    <select name ="venuename" id ="venueid">
                        <option value ="$venueid">--Select Venue--</option>
                        <?php
                        $query = "SELECT * FROM tblvenue";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $venueid = $row['venueid'];
                            $venuename = $row['venuename'];
                            echo "<option value ='$venueid'>$venuename</option>";
                        }
                        ?> 
                    </select>
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button  type="button" name ="saves" id="saves" class ="btn btn-primary">Add this computer</button>
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>

        </table>
    </form>
</div>