
<?php
if (!isset($_SESSION))
    session_start();
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
<?php require_once 'schedule_header.php';?>
        <style type="text/css">
            .err{ color:red; font-style: italic;}
        </style>
    </head>
    <body>

        <div id="container" class="container">
            <div class="page-header">
                <br><br>
                <h1 style="padding-left: 70px">Exam Scheduling Platform - SBRS Students</h1>
            </div>
            
            <br><br><br>
            <div style="margin-bottom: 100px">
                <form action ="schedule_info_sbrs_students.php" method ="POST" class="style-frm">
                    <center>
                        <table>
                            <tr>
                                <td colspan="3" class="err">
                                    <?php
                                    if (isset($_GET['errcandid']) && $_GET['errcandid'] == "invalid")
                                        echo"Invalid matric or password";
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Sbrs.No.:</td>
                                <td>
                                    <input type ="text" name ="matric" id ="matricno" />
                                </td>
                                <td class="err">
                                    <?php
                                    if (isset($_GET['errmatric']) && $_GET['errmatric'] == "matric")
                                        echo"Invalid Matric number";
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Password :</td>
                                <td>
                                    <input type ="password" name ="pass" id ="stupassword" />
                                </td>
                                <td class="err">
                                    <?php
                                    if (isset($_GET['errpassword']) && $_GET['errpassword'] == "password")
                                        echo"Invalid Password";
                                    ?>
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
        <?php
 require_once '../partials/cbt_footer.php';
 ?>
<?php include_once dirname(__FILE__) . "/../partials/jsimports.php" ?>;

    </body>
</html>

