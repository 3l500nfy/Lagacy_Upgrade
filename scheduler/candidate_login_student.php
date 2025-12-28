
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

        <style type="text/css">
            .err{ color:red; font-style: italic;}
        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>

        <div id="container" class="container">
            <div class="page-header">
                 <?php require_once 'schedule_header.php'; ?>
                <h1>Exam Scheduling Platform - Regular Students</h1>
                <br><br>
            <div>
                <form action ="schedule_info_student.php" method ="POST" class="style-frm">
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
                                <td>Matric. No./Reg. No./Exam. No:</td>
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
                                <td>Password</td>
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

<?php include_once dirname(__FILE__) . "/../partials/jsimports.php" ?>;

    </body>
</html>
