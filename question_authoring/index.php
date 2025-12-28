<?php
if (!isset($_SESSION))
    session_start();
?>
<!DOCTYPE html>
<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("authoring_functions.php");


openConnection();
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
$pgtitle = "::Question Authoring";
$navindex = 4;
require_once '../partials/cbt_header.php';
?>
<html lang="en">
    <head>
        <title></title>
        <link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>
        <?php javascriptTurnedOff(); ?>
        <style>

        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
            <style type="text/css">
                .links
                {
                    display:inline-block;
                    padding:5px;
                }
                
                .row{
                }
            </style>
    </head>
    <body>
       
        <div id="container" class="container" style="padding-left: 20px">
            <div class="page-header">
                <br>
                <h1>Question Authoring</h1>
            </div>
            <div id=" " class="row">
<?php include 'toplinks.php';?>            </div>
        </div>  
    </body>
</html>