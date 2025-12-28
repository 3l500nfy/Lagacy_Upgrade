<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
require_once('../../lib/security.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php require_once("../../partials/cssimports.php") ?>
    </head>

    <body>
        <?php include_once("../../partials/navbar.php"); ?>
        <div id="container" class="container">
            <div class="page-header">
                <h2>Administrator Login <small>- Human Resource Management System</small></h2>
            </div>
            <?php require_once('../../partials/notification.php'); ?> 
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

                    //Display for login failure
                    if (isset($_SESSION['SESS_ADMIN_INVALID_LOGIN'])) {
                        echo '<span style = "color: red; font-size: 11px;">*&nbsp;&nbsp;', $_SESSION['SESS_ADMIN_INVALID_LOGIN'], '</span><br />';
                        unset($_SESSION['SESS_ADMIN_INVALID_LOGIN']);
                    }
                    ?>
                    <fieldset>
                        <legend>Administrator, Log In</legend>
                        <br />                        
                        <form action="adminlogin_exec.php" method="post" autocomplete="off">
                            <div class="control-group">
                                <label for="username">UserName</label>
                                <div class="controls">
                                    <input class="input-block-level" type="text" name="username" placeholder="Username" required/>
                                </div>
                            </div><!-- /.control-group -->
                            <div class="control-group">
                                <label for="password">Password</label> 
                                <div class="controls">
                                    <input class="input-block-level" type="password" name="password" placeholder="Password" required/>
                                </div>                

                            </div><!-- /.control-group -->

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn btn-info btn-block">Sign in</button>
                                </div>
                            </div> <!-- /.control-group -->
                        </form>
                    </fieldset>
                </div> <!-- /.well -->
            </div>
        </div>
        <?php include_once dirname(__FILE__) . "/../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
    </body>
</html>

