<?php
if (!isset($_SESSION))
    session_start();
require_once("lib/globals.php");

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

    </head>
    <body>
                <div style="text-align:center;"><img src="<?php echo siteUrl("assets/img/dariya_logo1.png"); ?>" /></div>
                <div style="padding-left: 100px;">  <a href="login.php">Login</a></div>
        <div class="span5 style-div" style="margin-left: auto; width:350px; margin-top: 40px; padding-left: 30px; padding-right: 30px; margin-right: auto;">
            <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
                <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); text-rendering: optimizelegibility; font-size: 18px; font-weight: 700; line-height:40px; ">Sign Up</h2>
            </div>
            <div class="span4 offset4">
                <div class="well">

                    <?php
                    //Display validation error
                    if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                        echo "<div class='alert alert-error'>";
                        foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
                            echo '<span style = "color: red; font-size: 11px;">*&nbsp;&nbsp;', $msg, '</span><br />';
                        }
                        echo "</div>";
                        unset($_SESSION['ERRMSG_ARR']);
                    }
                    ?>
                    <form method ="post" action="usersignup_exec.php" id ="usersignup" class="style-frm">
                        <h2 class="cooltitle3">Sign Up Form</h2>
                        <br />
                        <div class="control-group">
                            <label class="control-label" for="">Username</label>
                            <div class="controls">
                                <input type="text" name ="username" id="username" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">Password</label>
                            <div class="controls">
                                <input type="password" name ="password" id="password" placeholder="Password" required>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">Display Name</label>
                            <div class="controls">
                                <input type="text" name ="displayname" id="displayname" placeholder="Display Name" required>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">Email</label>
                            <div class="controls">
                                <input type="email" name ="email" id="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">Personnel No.</label>
                            <div class="controls">
                                <input type="text" name ="pnumber" id="pnumber" placeholder="Personnel No." required>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">Question</label>
                            <div class="controls">
                                <select name ="question" id ="question" style="width:200px;">
                                    <option value ="">--Please Select Question</option>
                                    <option value ="What is the name of your best childhood friend">What is the name of your best childhood friend</option>
                                    <option value ="What is the name of your favourite classroom teacher">What is the name of your favourite classroom teacher</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">Answer</label>
                            <div class="controls">
                                <input type="text" name ="answer" id="answer" placeholder="Answer Question Above" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">                        
                                <button type="submit" class="btn btn-info">Sign Up</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
                <div style="text-align: center;">
                    <br /><br />
        <?php include_once dirname(__FILE__) . "/partials/footer.php" ?>;
                </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

    </body>
</html>
