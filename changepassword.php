<?php
if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");
require_once("lib/security.php");

authorize();
openConnection();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php javascriptTurnedOff(); ?>
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
        <div style="text-align:center;"><img src="<?php echo siteUrl("assets/img/dariya_logo1.png"); ?>" /></div>
        <div style="padding-left: 100px;">  <a href="login.php">Login</a></div>
        <div class="span5 style-div" style="margin-left: auto; width:350px; margin-top: 70px; padding-left: 30px; margin-right: auto;">
            <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
                <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); text-rendering: optimizelegibility; font-size: 18px; font-weight: 700; line-height:40px; ">Change Password</h2>
            </div>
            <div class="span4 offset4">
                <div class="well">
                    <?php
                    //Display validation error
                    if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                        foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
                            echo '<span style = "color: red; font-size: 11px;">*&nbsp;&nbsp;', $msg, '</span><br />';
                        }
                        unset($_SESSION['ERRMSG_ARR']);
                    }
                    ?>
                    <form action ="changepassword_exec.php" method ="post" class="style-frm">

                        <div class="control-group">
                            <label for="username" style="font-weight:bold;">Enter Old Password</label>
                            <div class="controls">
                                <input class="input-block-level" type="password" name="oldpassword" placeholder="Old Password" required/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="username" style="font-weight:bold;">Enter New Password</label>
                            <div class="controls">
                                <input class="input-block-level" type="password" name="newpassword" placeholder="New Password" required/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="username" style="font-weight:bold;">Re-enter New Password</label>
                            <div class="controls">
                                <input class="input-block-level" type="password" name="reenterpassword" placeholder="Re-Enter Password" required/>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-info btn-block" style="font-weight:bold;">Change Password</button>
                            </div>
                        </div> <!-- /.control-group -->
                        <hr>
                    </form>
                </div>
            </div>
        </div>
        <div style="text-align: center;"> <br /><?php include_once dirname(__FILE__) . "/partials/footer.php" ?>;</div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

    </body>
</html>