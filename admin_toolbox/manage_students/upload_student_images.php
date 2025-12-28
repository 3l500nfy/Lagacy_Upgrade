


<!DOCTYPE html>
<?php
session_start();
require_once("../../lib/globals.php");
ini_set("memory_limit","256M"); + ini_set('max_execution_time', 300);
?>
<html lang="en">
<head>
    <title><?php echo pageTitle("Computer Based Test") ?></title>
    <?php javascriptTurnedOff(); ?>
    <style>
        .modaldialog{
            display: none;
        }

        form{
            padding: 40px 20px;
            box-shadow: 0px 0px 10px;
            border-radius: 2px;
        }
        .selectedButton{
            background-color:#008800;
            border:1px solid #ff0000;
            color:#fff;
            border-radius:5px;
            padding:10px;
            text-shadow:1px 1px 0px green;
            box-shadow: 2px 2px 15px rgba(0,0,0, .75);
        }
        .selectedButton:hover{
            cursor:pointer;
            background:#c20b0b;
            border:1px solid #c20b0b;
            box-shadow: 0px 0px 5px rgba(0,0,0, .75);
        }
        #file{
            color:green;
            padding:5px; border:1px dashed #123456;
            background-color: #f9ffe5;
        }
        #selectedButton{
            margin-left: 45px;
        }
    </style>

    <!-------Including CSS File------>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
</head>
<body>

<div id="container" class="container" style="padding-left: 20px">
    <div class="page-header">
        <h1>Upload Student Passports</h1>
        <br>
        <div class="alert-notice">
            <b style="color:red;">NOTE:</b> Supported image format: .jpg, .png
        </div>
        <br />
    </div>
    <div>

        <form action="upload_exec.php" method="post" enctype="multipart/form-data">
            <table width="100%">
                <tr>
                    <td>Select Photo (one or multiple):</td>
                    <td><input type="file" name="files[]" multiple id="file"/></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><span  style="color: darkred">Note: Supported image format: .jpg, .png</span></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="submit" value="Upload Image(s)" id="selectedButton" class="selectedButton"/></td>
                </tr>
            </table>
        </form>

    </div>
</div>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
</body>
</html>
