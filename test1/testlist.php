<?php

if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");

require_once('../lib/security.php');
require_once("testfunctions.php");

openConnection();
global $dbh;
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']))) {
    redirect(siteUrl("test/login.php"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <style>
        input[type=button], input[type=submit], input[type=reset] {
            background-color: #44af60;
            border: none;
            color: white;
            padding: 10px 24px;
            text-decoration: none;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div style="text-align:center;"><img src="<?php echo siteUrl("assets/img/dariya_logo1.png"); ?>" /></div>
<div class="span5 style-div" style="margin-left: auto; width:700px; margin-top: 50px; padding-left: 30px; margin-right: auto;">
    <div class="contentbox" style="padding-top: 5px;  ">
        <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
            <h2>Please Select a Test</h2>
<?php
$candidateid=$_SESSION['candidateid'];
$query = "SELECT tbltestconfig.testid, tbltestconfig.testname, tbltesttype.testtypename, tbltestconfig.session, tbltestconfig.semester 
    FROM tbltestconfig 
    INNER JOIN tbltestcode ON tbltestconfig.testcodeid=tbltestcode.testcodeid
    INNER JOIN tbltesttype ON tbltestconfig.testtypeid=tbltesttype.testtypeid
    INNER JOIN tblscheduling ON tblscheduling.testid=tbltestconfig.testid
    INNER JOIN tblcandidatestudent ON tblcandidatestudent.scheduleid=tblscheduling.schedulingid
    INNER JOIN tblscheduledcandidate ON tblscheduledcandidate.candidateid=tblcandidatestudent.candidateid
    WHERE tblscheduling.date=curdate() 
    AND tblscheduling.dailyendtime>curtime() 
    AND tblscheduling.dailystarttime<=curtime() 
    AND tblcandidatestudent.candidateid=?";
//exit;
$stmt = $dbh->prepare($query);
$result = $stmt->execute(array($candidateid));
$numrows = $stmt->rowCount();
if ($numrows > 0){
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $testid = $row['testid'];
        $testname = $row['testname'];
        $testtypename = $row['testtypename'];
        $session = $row['session'];
        $semester = $row['semester'];
        echo "<div style='margin-right:auto; margin-left: auto; width:700px; margin-top: 50px; padding-left: 30px; margin-right: auto;'> $testname-$testtypename- $session
            <form action='testlist_exec.php' method='post'>
            <input type='hidden' value='$testid' name='testid'>
            <input type='submit' value='Select Test' name='submit'>
            </form>
        </div>";
    }
}else{
    echo "<h2 style = 'color: red; font-size: 11px;'>No Available Test at the Moment</h2>";
}


?>
        </div>
    </div>
</div>
</body>
</html>
