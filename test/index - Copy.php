<?php
if (!isset($_SESSION))
    session_start();
?>
<?php require_once("../lib/globals.php");
require_once("../lib/security.php"); 
require_once("testfunctions.php");
openConnection(true);
//authorize();
if(!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID'])&& isset($_SESSION['testid']))){
redirect(siteUrl("test/login.php"));
}
//get candidate information and test he is writting
$candidateid=$_SESSION['candidateid'] ;
$testid=$_SESSION['testid'];
		
$testinfo=array();
if(!isset($_SESSION['gettestinfo'])){
	$testinfo=gettestinfo($testid);
}
else{
	$testinfo=$_SESSION['gettestinfo'];
}
$questionadministration=$testinfo['questionadministration'];
$optionadministration=$testinfo['optionadministration'];

//if it is the fisrt time he his login in, create his set of question based on the exams configuraion and store in presentation
//first check if the question exist already and the total number is equal to the specified number in the configuration and the
//questions are not yet answered.

$query1="SELECT count(distinct(questionid)) as totalquestions FROM tblpresentation where(candidateid=$candidateid and testid=$testid)";
//echo $query1;
$result1=mysql_query($query1);
 $totalquestions=mysql_result($result1,0,'totalquestions');
if($totalquestions==0){
//create the questions. the question for the student is not yet created

createcandidatequestions($testid,$candidateid,$questionadministration,$optionadministration);
}
else{
	//already created check if the number matches the specified number in testconfifuration
	$query2="SELECT sum(num_toanswer) as totaltoanswer FROM tbltestsection
	INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid 	 
	where(testid='$testid' and tbltestsubject.subjectid in
	(select subjectid from tblcandidatestudent inner join tblscheduling on 
	tblcandidatestudent.scheduleid=tblscheduling.schedulingid where(testid='$testid' and candidateid='$candidateid')) )";
	// echo $query2;
	 $result2=mysql_query($query2);
	 $totaltoanswer=mysql_result($result2,0,'totaltoanswer');
//	 echo"stud=$totalquestions test=$totaltoanswer";
	 if($totalquestions!=$totaltoanswer){
	 
		 // the total number of questions the student is to answer does not match the one specified.
		 //if he has not started answering the questions, just recreate
		 $query3="SELECT * FROM tblscore where (candidateid=$candidateid and testid=$testid)";
		 $result3=mysql_query($query3);
		 if(mysql_num_rows($result3)<=0){
			 //the candidate has not started answering. delete and recreate the question
			 $query4="delete from tblpresentation where(candidateid=$candidateid and testid=$testid)";
			 mysql_query($query4);
			 //create question
			 createcandidatequestions($testid,$candidateid,$questionadministration,$optionadministration);
			 
		 }
		 
	 }


}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT</title>
        <?php require_once("../partials/cssimports.php") ?>
		
        <link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>

		<link rel="stylesheet" type="text/css" href="jquery.countdown/jquery.countdown.css"> 
		<script type="text/javascript" src="jquery.countdown/jquery.countdown.js"></script>

    </head>

    <body>
       <?php include_once "../partials/navbar.php" ?>;
		
        <div id="container" class="container">
		<table width=100% border=1>
                        <tr>
                            <td><font size=4><strong>EXAM:<strong></font><?php echo $testinfo['name'];?> <br><font size=4> Duration:</font> <?php echo $testinfo['duration'];?> mns</font> </td>
							<td><font size=4><strong>Candidate NAME:  name of candidate<br> Candidate Number: number of candidate </font> </td>
							<td>
							<div id="digiclock"></div>
</div>

<font size=4>Time control <span id="cdtime">loading countdown...</span></font><br> <font size=4>Start Time:</font><?php echo $testinfo['starttime'];?>
							<br> <font size=4>End Time:</font><?php echo $testinfo['endtime'];?> 
							<br><font size=4>Remaining time:</font><span id='cdown'><?php echo $testinfo['endtime'];?></span></td>
							<td><img alt="Picture"  border="1" align="right"height="70"  width="70"src="<?php echo "../picts/$candidateid.jpg";?>" /></td>
						</tr>	
		</table>					
							
		<hr />
		<h3 class="step_heading"> <?php getsubject($candidateid,$testid);?> </h3>
            <form id="subselfrm" name="subselfrm" action="#" method="post">
                <div id="sel">
                    <table>
                        <tr>
						<?php
                        //<button id="genform">Generate</button>
						?>
						</tr>
                    </table>
                </div>
                <br />
            </form>
        </div><!-- /container -->


        <?php include_once "../partials/footer.php" ?>;
        <?php include_once "../partials/jsimports.php" ?>;
		<script language="JavaScript">
<!--
var hoursleft = 0;
var minutesleft = 10;			// you can change these values to any value greater than 0
var secondsleft = 0;
var millisecondsleft = 0;
var finishedtext = "Countdown finished!" // text that appears when the countdown reaches 0
end = new Date();
end.setHours(end.getHours()+hoursleft);
end.setMinutes(end.getMinutes()+minutesleft);
end.setSeconds(end.getSeconds()+secondsleft);
end.setMilliseconds(end.getMilliseconds()+millisecondsleft);
function cd(){
	now = new Date();
	diff = end - now;
	diff = new Date(diff);
	var msec = diff.getMilliseconds();
	var sec = diff.getSeconds();
	var min = diff.getMinutes();
	var hr = diff.getHours()-1;
	if (min < 10){
		min = "0" + min;
	}
	if (sec < 10){
		sec = "0" + sec;
	}
	
	if(now >= end){
		clearTimeout(timerID);
		document.getElementById("cdtime").innerHTML = finishedtext;
	}
	else{
	   document.getElementById("cdtime").innerHTML = hr + ":" + min + ":" + sec;
	}		// you can leave out the + ":" + msec if you want...
			// If you do so, you should also change setTimeout to setTimeout("cd()",1000)
	timerID = setTimeout("cd()", 1000); 
}
window.onload = cd;
//-->
</script>
        <script type="text/javascript">
       //var cdown=($("#cdown").text()-0); 
       var cdownstr=new Date();
	  cdownstr= "01:00:00";
var cdown=((cdownstr.substring(0,2)-0)*60*60)+((cdownstr.substring(0,2)-0)*60)+(cdownstr.substring(0,2)-0);
//alert(cdown);	   
var intvl=0;
var e=0;

function addZero(i)
{
if (i<10) 
  {
  i="0" + i;
  }
return i;
}

function myFunction()
{
var d=new Date();
var x=document.getElementById("demo");
var h=addZero(d.getHours());
var m=addZero(d.getMinutes());
var s=addZero(d.getSeconds());
//x.innerHTML=h + ":" + m + ":" + s;
}


intvl=window.setInterval(function(){	
      var cdownstr=new Date();
	 // cdownstr= "01:00:00";

										cdown -=1; 
										var hv= ((cdown)-(cdown%(60*60)))/(60*60);
										//alert(hv);
										var mv=(cdown-((cdown-(hv*60))%60))/60;
										//alert(mv);
										if(mv==60) mv==0;
										var sv=((cdown-((cdown)-(cdown%(60*60))))%60);
										var hstr=((hv<10)?("0"+hv):(hv));
										var mstr=((mv<10)?("0"+mv):(mv));
										var sstr=((sv<10)?("0"+sv):(sv));
										
											var h=addZero(cdownstr.getHours());
											var m=addZero(cdownstr.getMinutes());
											var s=addZero(cdownstr.getSeconds());

										$('#cdown').text(h+":"+m+":"+s);
										//$('#cdown').text(hstr+":"+mstr+":"+sstr); 
										if(cdown<=5)
										{
										if(e==0)
										 e=window.setInterval(function(){
										$('#cdown').fadeOut(100).fadeIn(100);
										}, 200);
										}
										if(cdown==0)
										{
											window.clearInterval(e);
											window.clearInterval(intvl);
											
										}
									}, 
						1000
						);	   
	/**
var newYear = new Date(); 
newYear = new Date(newYear.getFullYear() + 1, 1 - 1, 1); 
$('#defaultCountdown').countdown({until: newYear}); 
 
$('#removeCountdown').toggle(function() { 
        $(this).text('Re-attach'); 
        $('#defaultCountdown').countdown('destroy'); 
    }, 
    function() { 
        $(this).text('Remove'); 
        $('#defaultCountdown').countdown({until: newYear}); 
    } 
);
**/
			
$(".subjbtn").bind('click', function(evt){

 $.ajax({
    type:'POST',
    url:'loadquestion.php',
    data:{subject:$(this).attr('id')}
}).done(function(msg){
 


  //  $("#subseldiv").remove();
  //  $("#subselfrm").append(msg);
  $("#sel").html(msg);
  });
  
return false;
});



//////
$(document).on('click', ".tbnnavigation", function(evt){
  $.ajax({
    type:'POST',
    url:'loadquestion.php',
    data:{direct:$(this).attr('id')}
}).done(function(msg){
 
//alert(msg);
  $("#sel").html(msg);

  });
return false;
});
/////
 
  
 $(document).on('click', ".answeroption", function(evt){
  $.ajax({
    type:'POST',
    url:'saveanswer.php',
    data:{question:$(this).attr('id'), ans:$(this).val()}
}).done(function(msg){
 
//alert(msg);
  });
//return false;
}); 
 
 
 
 		   $("button").button();
        </script>
    </body>
</html>