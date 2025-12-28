<?php
if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");
require_once('lib/security.php');

if (is_authenticated()) {
    $alreadyLoggedInUrl = siteUrl("loggedin.php");
    redirectTo($alreadyLoggedInUrl);
}

openConnection();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
            <?php echo pageTitle("Password Change") ?>
        </title>
                <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <style type="text/css">
            .control-group{
                margin-top:10px;
            }
        </style>
    </head>
    <body>
        <div class="container-narrow" style=" margin-top: 70px;">
            <div class="span5 style-div" style="margin-left: auto; width:350px; margin-top: 70px; padding-left: 30px; margin-right: auto;">
                <div class="contentbox" style="padding-top: 10px;">
                    <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
                        <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); color:green; text-rendering: optimizelegibility; font-size: 18px; font-weight: 700; line-height:40px; ">Password Change Success!</h2>
                    </div>
                    <?php require_once('partials/notification.php'); ?> 
                    <br /><br /><br />
                    Your password was changed successfully. <br /><br />Click <a href='login.php'>here to login again</a>.
                    <br /><br /><br />
                </div>
            </div>
        </div>
    </body>
</html>
