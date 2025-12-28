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
            <?php echo pageTitle("User Authentication") ?>
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
                        <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); text-rendering: optimizelegibility; font-size: 18px; font-weight: 700; line-height:40px; ">Please Login <small>to use CBT</small></h2>
                    </div>
                    <?php require_once('partials/notification.php'); ?> 

                    <?php
                    //Display validation error
                    if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                        foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
                            echo '<span style = "color: red; font-size: 11px;">*&nbsp;&nbsp;', $msg, '</span><br />';
                        }
                        unset($_SESSION['ERRMSG_ARR']);
                    }
                    ?>
                    <?php if (isset($_SESSION['LOGIN_FAILED'])): ?>
                        <div class="alert alert-error">
                            <?php
                            echo $_SESSION['LOGIN_FAILED'];
                            unset($_SESSION['LOGIN_FAILED']);
                            ?>
                        </div>
                    <?php endif ?>

                    <?php
                    // require_once("login.php"); 
                    ?>
                    <form action="login_exec.php" method="post" class="style-frm">
                        <div class="control-group">
                            <label for="username" style="font-weight: bold;">UserName</label>
                            <div class="controls">
                                <input class="input-block-level" type="text" id="username" name="username" placeholder="Username" required/>
                            </div>
                        </div><!-- /.control-group -->
                        <div class="control-group">
                            <label for="password" style="font-weight: bold;">Password</label> 
                            <div class="controls">
                                <input class="input-block-level" type="password" name="password" placeholder="Password" required/>
                            </div>                

                        </div><!-- /.control-group -->

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-info btn-block" style="font-weight: bold;">Login</button>
                            </div>
                        </div> <!-- /.control-group -->
                        <hr>
                        <p>
                            <small>
                                <a href="usersignup.php" tabindex="-1">Request an account</a> | <a href="recoverpassword.php" tabindex="-1">Forgot Password?</a>
                            </small>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
            $(document).ready(function (){
                $('#username').focus();
            });
        </script>
    </body>
</html>