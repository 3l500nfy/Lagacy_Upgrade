<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once('../lib/security.php');

require_once("testfunctions.php");

openConnection();
global $dbh;
//authorize();
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/login.php"));
}
//set a variable to show that the candidate has seen the question already
// in case he want to go back, redirect him here
if (isset($_SESSION['seequestion'])) {
    redirect(siteUrl("test/starttest.php"));
//header("location:starttest.php");
}
//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];
//get the candidate biodata
$biodata = array();
if (!isset($_SESSION['biodata'])) {
    $biodata = getbiodata($candidateid);
} else {
    $biodata = $_SESSION['biodata'];
}
$candidatename = ucwords( trim($biodata['candidatename'],','));

$matric = $biodata['matric'];



$testinfo = array();
if (!isset($_SESSION['testinfo'])) {
    $testinfo = gettestinfo($testid);
} else {
    $testinfo = $_SESSION['testinfo'];
}
$name = $testinfo['name'];
$startingmode = $testinfo['startingmode'];
$duration = $testinfo['duration'];
$starttime = $testinfo['starttime'];
$dailystarttime = $testinfo['dailystarttime'];
unset($_SESSION['testinfo']); //unset in order to get the latest time as the student click on start		
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
<?php echo pageTitle("Start Exams") ?>
        </title>
       
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>

        <style type="text/css">

            .tbnnavigation
            {
                -moz-border-radius:5px;
                -webkit-border-radius:5px;
                border-radius:5px;
                margin:3px;
                font-weight:bold;
                font-size:2em;
                padding:10px;

            }

            .auto-style1 {
                text-align: center;
            }
            .auto-style2 {
                text-align: left;
            }
        </style>
    </head>

    <body>
        <div style="text-align:center;"><img src="<?php echo siteUrl("assets/img/dariya_logo1.png"); ?>" /></div>
            <div class="span5 style-div" style="margin-left: auto; width:700px; margin-top: 50px; padding-left: 30px; margin-right: auto;">
                <div class="contentbox" style="padding-top: 10px;  ">
                    <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
                        <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); text-rendering: optimizelegibility; font-size: 22px; font-weight: 700; line-height:40px; "><?php echo $name; ?> 
                            <br>Welcome <small><?php echo" $candidatename"; ?></small></h2>
                    </div>
<?php
if (checkcompletion($testid, $candidateid) == true) {
    echo '<span style = "color: red; font-size: 11px;">*&nbsp;&nbsp;You have already taken the test.</span><br />';
    echo"<div  id ='startdiv' style='width:300px; margin-left:auto; margin-right:auto;'> 
							<button id='logoutbtn' class=\"cbtn tbnnavigation btn-info btn-block\">Logout</button>
							</div>";
} else {
    ?>
                        <div class="page-header">
                            <h2 class="auto-style1">General instructions to candidates:<small> Read carefully</small></h2>
                        </div>
                        <ol class='ansopt'style="line-height: 22px; font-size: 16px; ">
                            <li>Your time will start counting as soon as you click on <i><b>Start Exams.</b></i>
                            <li>Click on the subject code to view the questions.
                            <li>Use the NAVIGATION panel to quickly move to desired questions.
                             <li>Answers are automatically saved as they are selected.
                             <li>Avoid using<i> Forward, Backward, Refresh</i> buttons of your browser as they may log you out.
                            <li>Once you click on the<i> Submit Button</i>, you cannot come back to the test again.
                            <li> <i><b>You are advised to adhere strictly to the examination Regulations/Discipline as spelt out in the current University/Faculty prospectus and Information brochure.
                                        Ignorance of these regulations will not be accepted as an excuse for any misdemeanors. </b></i>
                            <li>Borrowing of pencils, biros, calculator etc is not allowed as each candidate is expected to have his/hers.
                            <li>GSM Handset(s) switched or un-switched are not allowed in the examination hall.
                              

                        </ol>
    
                    
                            <div  id ='startdiv' style='width:300px; margin-left:auto; margin-right:auto;'> 
                                <button id='startbtn' class="cbtn tbnnavigation btn-info btn-block">Start Exams</button>
                            </div>

                            <?php
                        
                    }//closing else part of exams alreadytaken		
                    ?>

                </div>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">

    $("#startbtn").bind('click', function(evt){
        window.location.href = "starttest.php";
 
    });

    $("#logoutbtn").bind('click', function(evt){
        window.location.href = "logout.php";
 
    });



    /*  $(document).ready(function (){
                $('#username').focus();
            }); */
        </script>
    </body>
</html>
