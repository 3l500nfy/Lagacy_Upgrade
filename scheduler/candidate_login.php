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
        
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
       
    </head>
    <body>
       
        <div id="container" class="container">
            <div class="header">
                <?php  require_once 'schedule_header.php';?>
                <h1>Exam Scheduling Platform - PUTME </h1>
                <br>
                <br>
                <br>
                <br>
                <form action ="schedule_info.php" method ="POST" class="style-frm">
                    <center>
                        <table>
                            <tr>
                                <td>Jamb Reg.No</td>
                                <td>
                                    <input type ="text" name ="jamb" id ="jambno" />
                                </td>
                            </tr>
                            <tr>
                                <td>State of Origin</td>
                                <td>
                                    <select name="state">
                                        <option>--Select State --</option>

                                        <?php
                                            $query = "SELECT * FROM tblstate";
                                            $stmt = $dbh->prepare($query);
                                            $stmt->execute();
                                            $numrows = $stmt->rowCount();
                                            
                                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                 	 
                                                $stateid = $row['stateid'];
                                                $statename = $row['statename'];
                                                
                                                echo "<option value ='$stateid'>$statename</option>";
                                            }
                                        ?>
                                    </select>
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