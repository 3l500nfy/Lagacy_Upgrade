<?php if(!isset($_SESSION)) session_start();
require_once("../lib/globals.php");
  openConnection();
  
  
  
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo pageTitle("Computer Based Test") ?></title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <?php require_once("../partials/cssimports.php") ?>
    </head>
    <body>
       
        <div id="container" class="container">
            <div class="header">
                <img class="banner" src="../assets/img/banner.png" alt="banner" >
                <h2>SBRS Entrance Exam Scheduling Platform </h2>
            </div>
            <div>
                <form action ="schedule_info.php" method ="POST">
                    <center>
                        <table>
                           <!-- <tr>
                                <td>Jamb Reg.No</td>
                                <td>
                                    <input type ="text" name ="jamb" id ="jambno" />
                                </td>
                            </tr>-->
                            <tr>
                                <td><b>Enter Application ID: </b></td>
                                <td>
                                    
                                    <input type ="text" name ="ApplicationID" id ="ApplicationID" />
                                
                                   <!-- <select name="state">
                                        <option>--Select State --</option>

                                        <?php
                                          /*  $sql = "SELECT * FROM tblstate";
                                            $result = mysql_query($sql);
                                            while($row = mysql_fetch_array($result)){
                                                 	 
                                                $stateid = $row['stateid'];
                                                $statename = $row['statename'];
                                                
                                                echo "<option value ='$stateid'>$statename</option>";
                                            }*/
                                        ?>
                                    </select>-->
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type ="submit" name = "continue_btn" class ="btn btn-primary" id ="continue_btn">Continue</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            </div>
        </div>
<?php include_once dirname(__FILE__) . "/../partials/jsimports.php" ?>;
    </body>
</html>