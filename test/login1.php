<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once('../lib/security.php');

if (is_authenticated()) {
    $alreadyLoggedInUrl = siteUrl("/test/loggedin.php");
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

        <?php require_once("../partials/cssimports.php") ?>
    </head>

    <body>
         <?php //
         include_once("loginnavbar.php"); 
		?>

        <div class="container-narrow" style="width: 400px; margin-top: 70px;">
            <div class="span5">
                <div class="contentbox" style="padding-top: 20px;">
                    <div class="page-header">
                        <h2>Please Login <small>to Start the Examination</small></h2>
                    </div>
                    <?php require_once('../partials/notification.php'); ?> 

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
                    <form action="login_exec.php" method="post" autocomplete="off">
                        
						<div class="control-group">
                            <label for="examstype">Exams Type</label>
                            <div class="controls">
							<select name="testid" id="testid"  class="input-block-level" required><option value="">Select Category</option>
							<?php
							//populate all exams to take place today
							$query="SELECT tbltestconfig.testid,testname,testtypename,session,semester FROM tbltestconfig 
							inner join tbltestcode ON tbltestconfig.testcodeid=tbltestcode.testcodeid
							left join tbltesttype ON tbltestconfig.testtypeid=tbltesttype.testtypeid
							INNER JOIN tblscheduling on tblscheduling.testid=	tbltestconfig.testid
							where(tblscheduling.date=curdate( ) and tblscheduling.dailyendtime>curtime())"; 
                                                       
                                                        
							$result=mysql_query($query);
							for($i=0;$i<mysql_num_rows($result);$i++){
								$testid=mysql_result($result,$i,'testid');
								$testname=strtoupper(mysql_result($result,$i,'testname'));
								$testtypename=mysql_result($result,$i,'testtypename');
								$session=mysql_result($result,$i,'session');
								$semester=mysql_result($result,$i,'semester');
								echo"<option value='$testid'> $testname-$testtypename- $session </option>";
								}
							?>
							</select>
                            
                              
                            </div>
                        </div><!-- /.control-group -->

						
						
						
						<div class="control-group">
                            <label for="username">UserName</label>
                            <div class="controls">
                                <input class="input-block-level" type="text" id="username" name="username" placeholder="Username" required/>
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
                        <hr>
                        <p>
                           
                    </form>
                </div>
            </div>
        </div>

        <?php include_once"../partials/jsimports.php" 
		?>;
        <script type="text/javascript">
		 setTimeout(function () { 
      location.reload();
    }, 360 * 1000);
	
            $(document).ready(function (){
                $('#username').focus();
            });
        </script>
    </body>
</html>
