<?php if(!isset($_SESSION)) session_start();
require_once("../../lib/globals.php");
  openConnection();
  
  //echo $_SESSION['tid'];
  
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
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        </head>
    <body>
       
        <div id="container" class="container">
            <div class="header">
                 <?php require_once '../schedule_header.php'; ?>
                <h1>SBRS Entrance Exam Scheduling Platform </h1>
                <br><br>
            <div>
                <form action ="schedule_info_sbrs_entrance.php" method ="POST" class="style-frm">
                    <center>
                        <table>
                   
                            <tr>
                                <td><b>Enter Application Form No.: </b></td>
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

    </body>
</html>