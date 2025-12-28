
<?php if (!isset($_SESSION))
    session_start(); ?>
<!DOCTYPE html>
<?php
require_once("../lib/globals.php");
openConnection();
?>
<html lang="en">
    <head>
        <title>Computer Based Test</title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
       
         <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <?php require_once 'schedule_header.php';?>
    </head>
    <body>
        <div style="margin-bottom: 50px">  
            <br>
            <br>
                <h1 style="padding-left: 70px">Exam Scheduling Platform </h1>
                <center>
                    <form action ="index_exec.php" method ="POST" class="style-frm">
                        <table>
                            <tr>
                                <td></td>
                                <td colspan="2"><img src="../assets/img/com.jpg"/></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="2"><?php if (isset($_GET['tconfig']))
            echo"<span style='color:red;'>No Test Available Yet.</span>"; ?></td>
                            </tr>
                            <tr>
                                <td width ="120"><b>Candidate Type</b></td>
                                <td>
                                    <select name ="candidatetype" id="candidatetype" class="">
                                        <option value="">--Select Candidate Type --</option>
                                        <?php
                                        $query = "SELECT * FROM tblcandidatetypes";
                                        $stmt = $dbh->prepare($query);
                                        $stmt->execute();
                                        $numrows = $stmt->rowCount();
                                       
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                            $candidatetypeid = $row['candidatetypeid'];
                                            $candidatetype = $row['candidatetype'];

                                            echo "<option value ='$candidatetypeid'>$candidatetype</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type="submit" value="Continue" name = "continue_btn" class ="btn btn-primary" id ="continue_btn"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>
            </div>

               <?php
 require_once '../partials/cbt_footer.php';
 ?>

        <?php include_once dirname(__FILE__) . "/../partials/jsimports.php" ?>;
        <script type ="text/javascript">
           
                $(document).on("click", "#continue_btn", function(){
                    if($("#candidatetype").val()=="") 
                        {
                            alert("Select a student type.");
                            return false;
                        }
                    else return true;
                });             
        </script>
    </body>
</html>