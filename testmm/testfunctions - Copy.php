<?php
//require_once("../lib/globals.php");
//$candidateid=1;
//$testid=23;

function gettestquestion($testid,$candidateid,$subject,$questionid=""){
	if(isset($testid)&& isset($candidateid)&& isset($subject)){
	
	}
}

function createcandidatequestions($testid,$candidateid,$questionadministration,$optionadministration){
	//this function create a question paper for each candidate based on test settings and populate the display table
	//get all the subject the candidate registered for during the test
	$query="SELECT subjectid from tblcandidatestudent inner join tblscheduling on 
	tblcandidatestudent.scheduleid=tblscheduling.tblschedulingid WHERE(candidateid='$candidateid' and testid='$testid')";
	$querysubject=mysql_query($query);
	if( mysql_num_rows($querysubject)>0){
	//create a query to combine the values to be stored in the presentation table
			$querypresentation="INSERT INTO tblpresentation(candidateid,testid,sectionid,questionid,answerid) values";
			
		for($i=0;$i<mysql_num_rows($querysubject);$i++){
			//for each subject, get all the section
			$subjectid=mysql_result($querysubject,$i,'subjectid');
			if($questionadministration=="linear"){
			$qsection="SELECT testsectionid,num_toanswer,numofeasy,numofmoderate,numofdifficult
			from tbltestsection INNER JOIN tbltestsubject ON tbltestsection.testsubjectid=tbltestsubject.testsubjectid
			WHERE	(subjectid='$subjectid' and testid='$testid')";
			}
			else{//randomize the selection of the section
			$qsection="SELECT testsectionid,num_toanswer,numofeasy,numofmoderate,numofdifficult
			from tbltestsection INNER JOIN tbltestsubject ON tbltestsection.testsubjectid=tbltestsubject.testsubjectid
			WHERE	(subjectid='$subjectid' and testid='$testid') order by RAND()";

			}
			$querysection=mysql_query($qsection);
			if( mysql_num_rows($querysection)>0){
				for($j=0;$j<mysql_num_rows($querysection);$j++){
					$testsectionid=mysql_result($querysubject,$j,'testsectionid');
					$num_toanswer=mysql_result($querysubject,$j,'num_toanswer');
					$numofdifficult=mysql_result($querysubject,$j,'numofdifficult');
					$numofmoderate=mysql_result($querysubject,$j,'numofmoderate');
					$numofeasy=mysql_result($querysubject,$j,'numofeasy');
					//
					
					//select the question based on the configuration specified 
					if($questionadministration=="linear"){

					$qquestion="SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='simple') limit $numofeasy
					UNION
					SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='difficult') limit $numofeasy
					UNION
					SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='moredifficult') limit $numofeasy";
					}
					else{//randomize the question
					$qquestion="SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='simple') order by RAND() limit $numofeasy
					UNION
					SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='difficult') order by RAND() limit $numofeasy
					UNION
					SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='moredifficult') order by RAND() limit $numofeasy";
						
					}
					$queryquestion=mysql_query($qquestion);
					//get the order of option to be presented to the students
					if( mysql_num_rows($queryquestion)>0){
						for($k=0;$k<mysql_num_rows($queryquestion);$k++){
							$questionbankid=mysql_result($queryquestion,$k,'questionbankid');
							
							//select the order of options to be presented to student and store them into presentation table
							if($optionadministration=="linear"){
							$qoption="SELECT * from tblansweroptions where(questionbankid='$questionbankid') order by RAND()";
							}
							$queryoption=mysql_query($qoption);
							
							if( mysql_num_rows($queryoption)>0){
								for($l=0;$l<mysql_num_rows($queryoption);$l++){
								$answerid=mysql_result($queryoption,$k,'answerid');
								//concatenate the query.
									if($querypresentation=="INSERT INTO tblpresentation(candidateid,testid,sectionid,questionid,answerid) values"){
									$querypresentation=$querypresentation."('$candidateid','$testid','$testsectionid','$questionbankid','$answerid')";
									}
									else
									{
									$querypresentation=$querypresentation.",('$candidateid','$testid','$testsectionid','$questionbankid','$answerid')";
									}
								}
							}
						}
						}
					}
			}
		}
		//execute the final query
		if($querypresentation!="INSERT INTO tblpresentation(candidateid,testid,sectionid,questionid,answerid) values"){
		$insertdata=mysql_query($querypresentation);
		}
	}

}

function testpresentation($candidateid,$testid,$subjectid,$displaymode){
//this function get the question for the student based on his display mode.
//get all the questions and place them in session to minimize the number of query in the server
	if(!isset($_SESSION['candidatequestion'])){ //and curentsubject=subjectid
		//get all the question
		$studquestion="SELECT candidateid,testid,tblpresentation.sectionid,questionid,answerid from tblpresentation 
		INNER JOIN tblsection on tblpresentation.sectionid=tbltestsection.sectionid
		WHERE(candidateid='$candidateid' and testid='$testid' and testsubjectid='$subjectid')";
		$querystudquestion=mysql_query($studquestion);
		if(mysql_num_rows($querystudquestion)>0){
		$candidatequestion=array();
		for($i=0;$i<mysql_num_rows($querystudquestion);$i++){
		$candidatequestion['testid']=mysql_result($querystudquestion,$i,'testid');
		$candidatequestion['sectionid']=mysql_result($querystudquestion,$i,'sectionid');
		$candidatequestion['questionid']=mysql_result($querystudquestion,$i,'questionid');
		$candidatequestion['answerid']=mysql_result($querystudquestion,$i,'answerid');
		//$candidatequestion['instruction']=mysql_result($querystudquestion,$i,'testid');
		}
		//place the question in session variable
		$_SESSION['candidatequestion']=$candidatequestion;
		}
	}
	
	if(count($_SESSION['candidatequestion'])>0)

	if($displaymode=='All'){
	//display all question
		for($i=0;$i<count($_SESSION['candidatequestion']);$i++){
		
		
		
		
		}
	
	}
	else{ //display mode=question by question
	
	
	}


}

function presents1($candidateid,$testid,$subjectid,$displaymode){
	var $candidateid=$candidateid;
	var $testid=$testid;
	var $subjectid=$subjectid;
	var $displaymode=$displaymode;
	}
class presents{
	
	
	function getquestion(){
		if(!isset($_SESSION['candidatequestion'])){ //and curentsubject=subjectid
			//get all the question
			$studquestion="SELECT candidateid,testid,tblpresentation.sectionid,questionid,answerid from tblpresentation 
			INNER JOIN tblsection on tblpresentation.sectionid=tbltestsection.sectionid
			WHERE(candidateid='$candidateid' and testid='$testid' and testsubjectid='$subjectid')";
			$querystudquestion=mysql_query($studquestion);
			if(mysql_num_rows($querystudquestion)>0){
			$candidatequestion=array();
			for($i=0;$i<mysql_num_rows($querystudquestion);$i++){
			$candidatequestion['testid'][$i]=mysql_result($querystudquestion,$i,'testid');
			$candidatequestion['sectionid'][$i]=mysql_result($querystudquestion,$i,'sectionid');
			$candidatequestion['questionid'][$i]=mysql_result($querystudquestion,$i,'questionid');
			$candidatequestion['answerid'][$i]=mysql_result($querystudquestion,$i,'answerid');
			//$candidatequestion['instruction']=mysql_result($querystudquestion,$i,'testid');
			}
			//place the question in session variable
			$_SESSION['candidatequestion']=$candidatequestion;
			}
		}
	}
	
	function getnextquestion($curentquestion){
		if($curentquestion!=$count($_SESSION['candidatequestion'])-1){
		return $curentquestion+1;
		}
		else{
		$return 0;
		}
	
	}
	
	function getpreviousquestion($curentquestion){
		if($curentquestion!==0){
		return $curentquestion-1;
		}
		else{
		$return $count($_SESSION['candidatequestion'])-1;
		}
	
	}
	
	function displayallquestion(){
	//create new section
	?>
	<div id="sectiondiv" style="border-width: 1px; border-style: solid; border-color: green;" >
		<div id="sectioninfodiv">
		<font size=3><b>SECTION:<b/></font><br>
		<font size=3><b>INSTRUCTION<b/></font>
		</div>
	<?php
		for($i=0;$i<count($candidatequestion)$i++){
			if($candidatequestion['sectionid'][$i]!=$curentsectionid && $i>0){
			//close the previous section and create new section
			?>
			</div>	
	<div id="sectiondiv" style="border-width: 1px; border-style: solid; border-color: green;" >
		<div id="sectioninfodiv">
		<font size=3><b>SECTION:<b/></font><br>
		<font size=3><b>INSTRUCTION<b/></font>
		</div>
				<?php
			
			}
			$query="SELECT tblsection.instruction"
			
			
		}//endfor
	}
}




function gettestinfo($testid){
$testinfo=array();
	if(isset($testid)){
	
	$querytestname=mysql_query("SELECT testname,testtypename, session, duration, dailystarttime, displaymode, startingmode,testcategory,
	questionadministration,optionadministration
	FROM tbltestconfig inner join tbltestcode
	on tbltestconfig.testcodeid=tbltestcode.testcodeid
	left join tbltesttype on tbltestconfig.testtypeid=tbltesttype.testtypeid
	where(tbltestconfig.testid='$testid')");
	if( mysql_num_rows($querytestname)>0){
		$testname=mysql_result($querytestname,0,'testname');
		$testtypename=mysql_result($querytestname,0,'testtypename');
		$session=mysql_result($querytestname,0,'session');
		$duration=mysql_result($querytestname,0,'duration');
		$dailystarttime=mysql_result($querytestname,0,'dailystarttime');
		$startingmode=mysql_result($querytestname,0,'startingmode');
		$testcategory=mysql_result($querytestname,0,'testcategory');
		$displaymode=mysql_result($querytestname,0,'displaymode');
		$questionadministration=mysql_result($querytestname,0,'questionadministration');
		$optionadministration=mysql_result($querytestname,0,'optionadministration');
		$name=strtoupper($testname);
		$session1=$session+1;
		$name=$name." ".strtoupper($testtypename)." "."($session/$session1)";
		$testinfo['name']= $name;
		$testinfo['questionadministration']= $questionadministration;
		$testinfo['optionadministration']= $optionadministration;
		$testinfo['testcategory']= $testcategory;
		$testinfo['displaymode']= $displaymode;
		$testinfo['duration']= $duration;
		$testinfo['starttime']= $dailystarttime;
		if($startingmode=='on login'){
		//start time on login
		$date = new DateTime($dailystarttime);
		$date->modify("+ $duration minutes");
				}
		else{
		//start time as specified. get the ellapsed time and add to it 
		$date = new DateTime($dailystarttime);
		$date->modify("+30 minutes");
		
		}
		$testinfo['endtime']= $date->format('H:i:s');
		//place the variable in session and return it to the caller
		$_SESSION['testinfo']=$testinfo;
		
		mysql_free_result($querytestname);
		return $testinfo;
	}
	}
		
}

function getcandidateinfo($loginid){
$candidateinfo=array();
	if(isset($loginid)){
	$querytestname=mysql_query("SELECT testname,testtypename, session, duration, dailystarttime, displaymode, startingmode,testcategory,
	questionadministration,optionadministration
	FROM tbltestconfig inner join tbltestcode
	on tbltestconfig.testcodeid=tbltestcode.testcodeid
	left join tbltesttype on tbltestconfig.testtypeid=tbltesttype.testtypeid
	where(tbltestconfig.testid='$testid')");
	if( mysql_num_rows($querytestname)>0){
		$testname=mysql_result($querytestname,0,'testname');
		$testtypename=mysql_result($querytestname,0,'testtypename');
		$session=mysql_result($querytestname,0,'session');
		$duration=mysql_result($querytestname,0,'duration');
		$dailystarttime=mysql_result($querytestname,0,'dailystarttime');
		$startingmode=mysql_result($querytestname,0,'startingmode');
		$testcategory=mysql_result($querytestname,0,'testcategory');
		$displaymode=mysql_result($querytestname,0,'displaymode');
		$questionadministration=mysql_result($querytestname,0,'questionadministration');
		$optionadministration=mysql_result($querytestname,0,'optionadministration');
		$name=strtoupper($testname);
		$session1=$session+1;
		$name=$name." ".strtoupper($testtypename)." "."($session/$session1)";
		$testinfo['name']= $name;
		$testinfo['questionadministration']= $questionadministration;
		$testinfo['optionadministration']= $optionadministration;
		$testinfo['testcategory']= $testcategory;
		$testinfo['displaymode']= $displaymode;
		$testinfo['duration']= $duration;
		$testinfo['starttime']= $dailystarttime;
		if($startingmode=='on login'){
		//start time on login
		$date = new DateTime($dailystarttime);
		$date->modify("+ $duration minutes");
				}
		else{
		//start time as specified. get the ellapsed time and add to it 
		$date = new DateTime($dailystarttime);
		$date->modify("+30 minutes");
		
		}
		$testinfo['endtime']= $date->format('H:i:s');
		//place the variable in session and return it to the caller
		$_SESSION['testinfo']=$testinfo;
		return $testinfo;
	}
	}
		mysql_free_result($querytestname);
}

function getsubject($candidateid,$testid){
	$studsubject=array();
	//this function get all the subject applicable to the student and place them as hyperlink
	$subject="SELECT tblsubject.subjectid,subjectcode,subjectname from tblsubject 
	inner join tblcandidatestudent on tblcandidatestudent.subjectid=tblsubject.subjectid
	inner join tblscheduling on tblcandidatestudent.scheduleid=tblscheduling.schedulingid
	where(tblcandidatestudent.candidateid='$candidateid' and tblscheduling.testid='$testid')";
	$querysubject=mysql_query($subject);
	if( mysql_num_rows($querysubject)>0){
		//candidate registerer for exam. generate his hyperlink to start each subject.
		echo"Start Subject:";
		for($i=0;$i<mysql_num_rows($querysubject);$i++){
		$subjectid=mysql_result($querysubject,$i,'subjectid');
		$subjectcode=mysql_result($querysubject,$i,'subjectcode');
		$subjectname=mysql_result($querysubject,$i,'subjectname');
		$studsubject['subjectid'][$i]=$subjectid;
		$studsubject['subjectcode'][$i]=$subjectcode;
		$studsubject['subjectname'][$i]=$subjectname;
		
		//echo"|<a href=\"#\" id='$subjectid'>$subjectname</a>";
		echo" <button id='$subjectid' class=\"cbtn subjbtn\">$subjectcode</button>";
		}
		$_SESSION['studsubject']=$studsubject;
	}
	mysql_free_result($querysubject);
}

function authenticatecandidate($candidateid,$testid,$venueid="",$testdate=""){
return true;
	if(isset($candidateid)&& isset($testid)){
	if($venueid==""){$venueid="%";}
	if($testdate==""){$testdate="%";}
	$queryauth = "SELECT candidateid FROM  tblcandidatestudent
		 INNER JOIN tblscheduling ON tblcandidatestudent.scheduleid= tblscheduling.schedulingid
		  WHERE(testid='$testid'and candidateid=$candidateid and  venueid like'$venueid')";
		// echo" <script language='jvascript'> alert('$queryauth')</script>";
	$resultauth=mysql_query($queryauth);
	if( mysql_num_rows($resultauth)>0){
	return true;

	}else{return false;}
	}
	else{
	return false;
	}
	
}

function checkuniquelogin($candidateid,$testid){

}


?>