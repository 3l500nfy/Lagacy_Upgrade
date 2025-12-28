


<!DOCTYPE html>
<?php
session_start();
require_once("../../lib/globals.php");
ini_set("memory_limit","256M");
ini_set('max_execution_time', 300);
?>
<html lang="en">
    <head>
        <title><?php echo pageTitle("Computer Based Test") ?></title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <?php ?>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>

        <div id="container" class="container" style="padding-left: 20px">
            <div class="page-header">
                <h1>Upload Student Data-Excel Format  </h1>
                <br>
                <div class="alert-notice">
                        <b style="color:red;">NOTE:</b> You can upload from Excel workbook .xls or .xlsx format. click <a  style="color:orange; font-style: italic;" target="_blank" href="<?php echo siteUrl("assets/file/student_list_upload_template.xlsx"); ?>">here</a> to download template
                </div>
                <br />
            </div>
            <div>
                <form action ="manage_student_exec.php" enctype="multipart/form-data" method ="POST" class="style-frm">
                    <center>
                        <table >

                            <tr>
                                <td>Select Excel-File</td>
                                <td>
                                    <input type ="file" name ="file" /> 
                                </td>
                            </tr>


                            <tr>
                                <td></td>
                                <td>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type ="submit" name = "" class ="btn btn-primary" id ="continue_btn">Upload</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
    </body>
</html>
