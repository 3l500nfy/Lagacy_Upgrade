<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once('../lib/security.php');

require_once("testfunctions.php");

openConnection();

//authorize();
if(!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID'])&& isset($_SESSION['testid']))){
redirect(siteUrl("test/login.php"));
}
//set a variable to show that the candidate has seen the question already
// in case he want to go back, redirect him here
if(isset($_SESSION['seequestion'])){
redirect(siteUrl("test/starttest.php"));
//header("location:starttest.php");
}
//get candidate information and test he is writting
$candidateid=$_SESSION['candidateid'] ;
$testid=$_SESSION['testid'];
//get the candidate biodata
$biodata=array();
if(!isset($_SESSION['biodata'])){
	$biodata=getbiodata($candidateid);
}
else{
	$biodata=$_SESSION['biodata'];
}
$candidatename=$biodata['candidatename'];
$matric=$biodata['matric'];



$testinfo=array();
if(!isset($_SESSION['testinfo'])){
	$testinfo=gettestinfo($testid);
}
else{
	$testinfo=$_SESSION['testinfo'];
}
		$name=$testinfo['name'];
		$startingmode=$testinfo['startingmode'];
		$duration=$testinfo['duration'];
		$starttime=$testinfo['starttime'];
		$dailystarttime=$testinfo['dailystarttime'];
unset($_SESSION['testinfo']);//unset in order to get the latest time as the student click on start		
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
            <?php echo pageTitle("Start Exams") ?>
        </title>

        <?php require_once("../partials/cssimports.php") ?>
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
        <?php 
        include_once("loginnavbar.php"); 
		?>

        <div class="container-narrow" style="width: 400px; margin-top: 70px;">
            <div class="span5">
                <div class="contentbox" style="padding-top: 20px;">
                    <div class="page-header">
						<h2 class="auto-style1"><?php echo $name;?> 
						<br>Welcome <small><?php echo" $candidatename"; ?></small></h2>
                    </div>
                    <?php
					if( checkcompletion($testid,$candidateid)==true){
					echo '<span style = "color: red; font-size: 11px;">*&nbsp;&nbsp;You have already taken the test.</span><br />';
						echo"<div  id ='startdiv' style='width:300px; margin-left:auto; margin-right:auto;'> 
							<button id='logoutbtn' class=\"cbtn tbnnavigation btn-info btn-block\">Logout</button>
							</div>";
					}else{
					?>
					 <div class="page-header">
					                <h2 class="auto-style1">General Instructions:<small>Read carefully</small></h2>
                    </div>
					<ol class='ansopt'>
					<li>Avoid using Navigation buttons as they may log you out
					<li>Answers are automatically saved as they are selected
					<li>Once you log out you cannot come back to the test again

					</ol>
					<?php
					$curenttime=new DateTime();
					$curenttime1=$curenttime->format('H:i:s');
					$dailystarttime=new DateTime($dailystarttime);
					$date1=$dailystarttime->format('H:i:s');
					$second = strtotime($curenttime1) - strtotime($date1);
					//echo"$second = strtotime($curenttime1) - strtotime($date1)";
					if($second <0){
					
					
					?>
						<h2 class="auto-style2"> Exams Start From: <span id='endtime'><?php echo $date1;?></span>
						<br> Remaining time: <span id='cdtime'> </span> </h2>
						<span style="visibility:hidden;" id='remainingsecond'><?php echo abs($second);?> </span>
						
						<div  id ='startdiv' style='width:300px; margin-left:auto; margin-right:auto; visibility:hidden'> 
							<button id='startbtn' class="cbtn tbnnavigation btn-info btn-block">Start Exams</button>
							</div>
            
						
					<?php
					}
					else
					{
					?>
						<div  id ='startdiv' style='width:300px; margin-left:auto; margin-right:auto;'> 
							<button id='startbtn' class="cbtn tbnnavigation btn-info btn-block">Start Exams</button>
							</div>
                        
					<?php
					}
			}//closing else part of exams alreadytaken		
					?>
                    
                </div>
            </div>
        </div>

        <?php include_once"../partials/jsimports.php" 
		?>;
        <script type="text/javascript">
		
		
		
//alert(cdown);
var finishedtext = "Start Exams Now!!!" // text that appears when the countdown reaches 0
//document.getElementById("startdiv").style.visibility='hidden';
		
		var remainingsecond=($("#remainingsecond").text()); 
//alert(remainingsecond);
remainingsecond=parseInt(remainingsecond);

function cd(){
remainingsecond=remainingsecond-1;
document.getElementById("remainingsecond").innerHTML =remainingsecond;
	//alert(remainingsecond);
	min=Math.floor(remainingsecond/60);
	sec=remainingsecond % 60;

	if (min < 10){
		min = "0" + min;
	}
	if (sec < 10){
		sec = "0" + sec;
	}
	
	if(remainingsecond<=0){
		clearTimeout(timerID);
		document.getElementById("cdtime").innerHTML = finishedtext;
		 document.getElementById("startdiv").style.visibility='visible';	
		}
	else{
   document.getElementById("cdtime").innerHTML = min + " mns" + sec + " sec";
	}	
	timerID = setTimeout("cd()", 1000); 
}

<?php
if($second <0){
echo"window.onload = cd;";
}
?>



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
