<?php
function checkcompletion($testid,$candidateid){
	$query=mysql_query("SELECT completed, elapsed, duration from tbltimecontrol INNER JOIN tbltestconfig on 
	tbltimecontrol.testid=tbltestconfig.testid where(tbltimecontrol.testid='$testid' and tbltimecontrol.candidateid='$candidateid')");
	if(mysql_num_rows($query)>0){
	$completed=mysql_result($query,0,'completed');
		if($completed==1){
		return true;
		}else
		{
			//check if elapsed time is up to duration
			$elapsed=mysql_result($query,0,'elapsed');
			$duration=mysql_result($query,0,'duration');
			$duration=$duration*60;//convert duration to second
			mysql_free_result($query);
			if($duration > $elapsed){
				return false;
			
			}else{
				return true;
			}

		}
	}
	else{
	// the candidate has not stated
	return false;
	}
}
function timecontrol($testid,$candidateid,$waitingsecond){
	if(isset($testid)&& isset($candidateid)){
	/* this function update the tbltimecontrol to keep track of the number of second spent by the student 
	and return the elapsed time in second 
	
	*/ 
		$testinfo=array();
		$testinfo=$_SESSION['testinfo'];
		$duration=$testinfo['duration'];
		
		$curenttime=new DateTime();
		$curenttime1=$curenttime->format('H:i:s');
		$elapsed=0;
		
		$ip=getIpAddress();

		//get the last saved time
		$querysavedtime=mysql_query("SELECT curtime() as now, curenttime,elapsed,starttime FROM `tbltimecontrol` 
		where(testid='$testid' and candidateid='$candidateid') order by curenttime DESC");
		if(mysql_num_rows($querysavedtime)>0){
			$storedcurtime=mysql_result($querysavedtime,0,'curenttime');
			$storedelapsed=mysql_result($querysavedtime,0,'elapsed');
			if($storedelapsed==""){$storedelapsed=0;}
			$starttime=mysql_result($querysavedtime,0,'starttime');
			$curenttime=mysql_result($querysavedtime,0,'now');
			$storeddate = new DateTime($storedcurtime);
			$curenttime=new DateTime($curenttime);
			$curenttime1=$curenttime->format('H:i:s');
			$date1=$storeddate->format('H:i:s');
			
			$second = abs(strtotime($curenttime1) - strtotime($date1));
			
			if($second <= ($waitingsecond +20)){ //adding 20 second to the normal waiting duration to cather for server delay in processing
			//no problem has occured since we are still in the acceptable range of elapsed time
			
				$elapsed=$storedelapsed+$second;
				if($elapsed < $duration*60){
				$qry="REPLACE into tbltimecontrol (testid,candidateid,starttime,curenttime,elapsed,ip) values
			     ('$testid','$candidateid','$starttime','$curenttime1',$elapsed,'$ip')";
				$querysave=mysql_query($qry);
				}
				else{//time up
				$qry="REPLACE into tbltimecontrol (testid,candidateid,completed,starttime,curenttime,elapsed,ip) values
				('$testid','$candidateid',1,'$starttime','$curenttime1',$elapsed,'$ip')";
				$querysave=mysql_query($qry);
				//include logout here
				}
				//echo "". $qry;
			}
			else{
			//there is a delay. probably the server was not reachable. or they was a logout. so store the last time the server was accessed
			$qry="REPLACE into tbltimecontrol (testid,candidateid,starttime,curenttime,elapsed,ip) values
			 ('$testid','$candidateid','$starttime','$curenttime1',$storedelapsed,'$ip')";
				$querysave=mysql_query($qry);
			//echo $qry;
			}
			mysql_free_result($querysavedtime);	
		}
		else
		{//first time to login. store the start time and the elapsed time based on starting mode.
		$startingmode=$testinfo['startingmode'];
		if($startingmode=='on login'){  
			$starttime=$curenttime1;
			$elapsed=0;
		}
		else{//on start time add the late period
		$starttime=$testinfo['starttime'];
			$elapsed=abs(strtotime($curenttime1)-strtotime($starttime));
					
		}
		
		//$testinfo=array();
			$qry="INSERT into tbltimecontrol (testid,candidateid,starttime,curenttime,elapsed,ip) values
		 ('$testid','$candidateid','$starttime','$curenttime1',$elapsed,'$ip')";
//			echo $qry;
			$querysave=mysql_query($qry);
		
		}
			
		
	
		
	
	}else{
	//not identified. testid or candidateid not set//log out
	}
        $_SESSION['testinfo']['remainingsecond']= $duration*60-$elapsed;
       
	return $elapsed;
}

function checkcandiadeteschedule($testid,$candidateid){
	if(isset($testid)&& isset($candidateid)){
	$query="";
		
	
	}else{
	//not identified. testid or candidateid not set
	}
}


function createcandidatequestions($testid,$candidateid,$questionadministration,$optionadministration){
	//this function create a question paper for each candidate based on test settings and populate the display table
	//get all the subject the candidate registered for during the test
	$query="SELECT subjectid from tblcandidatestudent inner join tblscheduling on 
	tblcandidatestudent.scheduleid=tblscheduling.schedulingid WHERE(candidateid='$candidateid' and testid='$testid')";
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
			WHERE	(subjectid='$subjectid' and testid='$testid') order by testsectionid ";
			}
			else{//randomize the selection of the section
			$qsection="SELECT testsectionid,num_toanswer,numofeasy,numofmoderate,numofdifficult
			from tbltestsection INNER JOIN tbltestsubject ON tbltestsection.testsubjectid=tbltestsubject.testsubjectid
			WHERE	(subjectid='$subjectid' and testid='$testid') order by RAND()";

			}
			$querysection=mysql_query($qsection);
			if( mysql_num_rows($querysection)>0){
				for($j=0;$j<mysql_num_rows($querysection);$j++){
					$testsectionid=mysql_result($querysection,$j,'testsectionid');
					$num_toanswer=mysql_result($querysection,$j,'num_toanswer');
					$numofdifficult=(int)mysql_result($querysection,$j,'numofdifficult');
					$numofmoderate=(int)mysql_result($querysection,$j,'numofmoderate');
					$numofeasy=(int)mysql_result($querysection,$j,'numofeasy');
					//
					
					
					//select the question based on the configuration specified 
					if($questionadministration=="linear"){

					$qquestion="(SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='simple') limit $numofeasy)
					UNION
					(SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='difficult') limit $numofmoderate)
					UNION
					(SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='moredifficult') limit $numofdifficult)";
					}
					else{//randomize the question
					$qquestion="(SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='simple') order by RAND() limit $numofeasy)
					UNION
					(SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='difficult') order by RAND() limit $numofmoderate)
					UNION
					(SELECT testsectionid,tbltestquestion.questionbankid FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid='$testsectionid' and difficultylevel='moredifficult') order by RAND() limit $numofdifficult)";
						
					}
				//	echo $qquestion;
					$queryquestion=mysql_query($qquestion);
					//get the order of option to be presented to the students
					if( mysql_num_rows($queryquestion)>0){
						for($k=0;$k<mysql_num_rows($queryquestion);$k++){
							$questionbankid=mysql_result($queryquestion,$k,'questionbankid');
							
							//select the order of options to be presented to student and store them into presentation table
							if($optionadministration=="linear"){
							$qoption="SELECT * from tblansweroptions where(questionbankid='$questionbankid')";
							}
							else{
							$qoption="SELECT * from tblansweroptions where(questionbankid='$questionbankid') order by RAND()";
							}
							$queryoption=mysql_query($qoption);
							
							if( mysql_num_rows($queryoption)>0){
								for($l=0;$l<mysql_num_rows($queryoption);$l++){
								$answerid=mysql_result($queryoption,$l,'answerid');
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
					mysql_free_result($querysection);
			}
		}
		//execute the final query
		if($querypresentation!="INSERT INTO tblpresentation(candidateid,testid,sectionid,questionid,answerid) values"){
		$insertdata=mysql_query($querypresentation);
		}
		mysql_free_result($querysubject);
	}

}

function testpresentation($candidateid,$testid,$subjectid,$displaymode){
//this function get the question for the student based on his display mode.
//get all the questions and place them in session to minimize the number of query in the server
	if(!isset($_SESSION['candidatequestion'])){ //and curentsubject=subjectid
		//get all the question
		$studquestion="SELECT distinct candidateid,testid,tblpresentation.sectionid,questionid from tblpresentation 
		INNER JOIN tblsection on tblpresentation.sectionid=tbltestsection.sectionid
		WHERE(candidateid='$candidateid' and testid='$testid' and testsubjectid='$subjectid')";
		$querystudquestion=mysql_query($studquestion);
		if(mysql_num_rows($querystudquestion)>0){
		$candidatequestion=array();
		for($i=0;$i<mysql_num_rows($querystudquestion);$i++){
		$candidatequestion['testid'][$i]=mysql_result($querystudquestion,$i,'testid');
		$candidatequestion['sectionid'][$i]=mysql_result($querystudquestion,$i,'sectionid');
		$candidatequestion['questionid'][$i]=mysql_result($querystudquestion,$i,'questionid');
		//$candidatequestion['instruction']=mysql_result($querystudquestion,$i,'testid');
		}
		//place the question in session variable
		$_SESSION['candidatequestion']=$candidatequestion;
		mysql_free_result($querystudquestion);
		}
	}
}

	
	
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
			mysql_free_result($querystudquestion);

			}
		}
	}
	
	function getnextquestion($curentsubject){
	$candidatequestion=array();
	
		if(isset($_SESSION['candidatequestion'])){
		 $candidatequestion=$_SESSION['candidatequestion'];
		 if(isset($_SESSION['curentquestion'][$curentsubject])){
		 $curent=$_SESSION['curentquestion'][$curentsubject];
		 }
		 else{
		 $curent=0;
		 }
		 if (isset($candidatequestion[$curentsubject][$curent+1])){
		 $_SESSION['curentquestion'][$curentsubject]=$curent+1;
		 return $candidatequestion[$curentsubject][$curent+1];
		 }
		 else{
		 return $candidatequestion[$curentsubject][$curent];
		 }
		 
		 
		 }
		 
			
	}
	
		function getpreviousquestion($curentsubject){
	$candidatequestion=array();
	
		if(isset($_SESSION['candidatequestion'])){
		 $candidatequestion=$_SESSION['candidatequestion'];
		 if(isset($_SESSION['curentquestion'][$curentsubject])){
		 $curent=$_SESSION['curentquestion'][$curentsubject];
		 }
		 else{
		 $curent=0;
		 }
		 if (isset($candidatequestion[$curentsubject][$curent-1])){
		 $_SESSION['curentquestion'][$curentsubject]=$curent-1;
		 return $candidatequestion[$curentsubject][$curent-1];
		 }
		 else{
		 return $candidatequestion[$curentsubject][$curent];
		 }
		 
		 
		 }
		 
			
	}
	
	function getquestionsorder($testid,$candidateid){
		$query="SELECT DISTINCT (`questionid`), subjectid FROM `tblpresentation` 
		inner join tbltestsection on tbltestsection.testsectionid=tblpresentation.sectionid
		inner join tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid
		WHERE 	tblpresentation.testid='$testid' and tblpresentation.candidateid='$candidateid'
		ORDER BY presentationid	";
		echo $query;
		$result=mysql_query($query);
		if(mysql_num_rows($result)>0){
			$questionorder=array();
			$counter=0;
			$previoussubjectid=-1;
			for($n=0;$n < mysql_num_rows($result);$n++){
				$questionid=mysql_result($result,$n,'questionid');
				$subjectid=mysql_result($result,$n,'subjectid');
				if($previoussubjectid!=$subjectid){
				//reset the counter. subject hs changed
				$counter=0;
				$previoussubjectid=$subjectid;
				$_SESSION['curentquestion'][$subjectid]=0;//initialize the index of the first question to be used in next and previous
				}
				//echo"++ $subjectid,$counter,$questionid ++";
				//echo $_SESSION['curentquestion'][$subjectid];
				$questionorder[$subjectid][$counter]=$questionid;
				$counter++;
			}
			$_SESSION['candidatequestion']=$questionorder;
			mysql_free_result($result);

		}
	}
	

	
function displaysinglequestion($candidateid, $testid, $subjectid,$curentquestion){
    global $limit;
		$studentquestion="SELECT candidateid,tblpresentation.testid,tblpresentation.sectionid,questionid,tblquestionbank.title, 
		tbltestsection.title,tbltestsection.instruction from tblpresentation 
		INNER JOIN tbltestsection on tblpresentation.sectionid=tbltestsection.testsectionid
		INNER JOIN tblquestionbank on tblpresentation.questionid=tblquestionbank.questionbankid
		INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid
		WHERE(candidateid='$candidateid' and tblpresentation.testid='$testid' 
		 and questionid='$curentquestion' and tbltestsubject.subjectid='$subjectid')";
		
		//echo $studentquestion;
		$resultquestion=mysql_query($studentquestion);
		if(mysql_num_rows($resultquestion)>0){
			$sectiontitle=mysql_result($resultquestion,0,'tbltestsection.title');
			$sectioninstruction=mysql_result($resultquestion,0,'instruction');
			//create new section
			opensectiondiv($sectiontitle, $sectioninstruction);
						
				$questionid=mysql_result($resultquestion,0,'questionid');
				$questiontitle=mysql_result($resultquestion,0,'title');
                                $questiontitle=stripslashes($questiontitle);
				?>
				<div id="questionanswerdiv" style="border-width: 1px; border-style: solid; border-color: green; ">
				<div id="questiondiv">
				<font size=3><b>question <?php echo $limit+1;?>:<b/><?php echo html_entity_decode($questiontitle, ENT_QUOTES);?></font>
				</div>
				<div id="answerdiv">
				<?php
				showquestion($questionid, $testid,$candidateid,$questiontype="OBJ");
									
			echo"	</div>";	//closing  answerdiv
			//include next and previous here
                        $firsttype="";
                        $lasttype="";
                        if($_SESSION['firstquestion']==1){
                         $firsttype=  "disabled";
                        }
                        
                         if($_SESSION['lastquestion']==1){
                             $lasttype="disabled";
                            
                        }
					echo"<div style='width:300px; margin-left:auto; margin-right:auto;'>";
					echo" <button id='previous' class=\"cbtn tbnnavigation\" $firsttype>Previous</button>";
					echo" <button id='next' class=\"cbtn tbnnavigation \" $lasttype>Next</button></div>";
					
				echo"	</div>	";	//closing questionanswerdiv 
				mysql_free_result($resultquestion);

		}
}
	
	
	function displayallquestion($candidateid, $testid, $subjectid){
	//create the questions
	
		$studentquestion="SELECT distinct(questionid),tblpresentation.candidateid,tblpresentation.testid,tblpresentation.sectionid,
		tblquestionbank.title, tbltestsection.title,tbltestsection.instruction from tblpresentation 
		INNER JOIN tbltestsection on tblpresentation.sectionid=tbltestsection.testsectionid
		INNER JOIN tblquestionbank on tblpresentation.questionid=tblquestionbank.questionbankid
		INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid		
		WHERE(tblpresentation.candidateid='$candidateid' and tblpresentation.testid='$testid'
		and tbltestsubject.subjectid='$subjectid')";
		//echo $studentquestion;
		$resultquestion=mysql_query($studentquestion);
		if(mysql_num_rows($resultquestion)>0){
			$sectiontitle=mysql_result($resultquestion,0,'tbltestsection.title');
			$sectioninstruction=mysql_result($resultquestion,0,'instruction');
			//create new section
			$curentsectionid=mysql_result($resultquestion,0,'sectionid');
			opensectiondiv($sectiontitle, $sectioninstruction);
			$counter=0;
			for($i=0;$i<mysql_num_rows($resultquestion);$i++){
				$sectionid=mysql_result($resultquestion,$i,'sectionid');
				if(($sectionid!=$curentsectionid) && $i>0){
					$curentsectionid=$sectionid;
					//close the previous section and create new section
					$sectiontitle=mysql_result($resultquestion,$i,'tbltestsection.title');
					$sectioninstruction=mysql_result($resultquestion,$i,'instruction');
					echo"</div><hr style='border-width:2px; border-style:dotted;' />";//close the section div;
					opensectiondiv($sectiontitle, $sectioninstruction);
				}
				$questionid=mysql_result($resultquestion,$i,'questionid');
				$questiontitle=mysql_result($resultquestion,$i,'title');
                                $questiontitle=stripslashes($questiontitle);
				$counter=$i+1;
                                $divid="questiondiv".$questionid;
				?>
				<div class="questionanswerdiv" id="<?php echo $divid ?>" style="border-width: 1px; border-style: solid; border-color: black; ">
				<div class="questiondiv" >
				<font size=3><b> <?php echo " Question $counter: "; ?><b/><?php echo html_entity_decode($questiontitle, ENT_QUOTES);?></font>
				</div>
				<div id="answerdiv">
				<?php
				showquestion($questionid, $testid,$candidateid,$questiontype="OBJ");
				echo"</div>";
							echo"	</div>";
			}//endfor
	//closing questionanswerdiv and answerdiv
		mysql_free_result($resultquestion);

		}
	}

function showquestion($questionid, $testid,$candidateid,$questiontype="OBJ"){
    $tblscoreanswerid=0;
	if($questiontype=="OBJ"){
	//objectve question
		$queryquest="SELECT tblpresentation.questionid,tblpresentation.answerid,
		correctness,test FROM tblpresentation
		INNER JOIN tblansweroptions ON tblpresentation.answerid=tblansweroptions.answerid
		where(tblpresentation.questionid='$questionid' and 
		tblpresentation.testid='$testid' and tblpresentation.candidateid='$candidateid'
		)";
		//echo $queryquest;
			$resultquest=mysql_query($queryquest);
		//echo "ans=".mysql_num_rows($resultquest);
		//$tblscoreanswerid="";
			if(mysql_num_rows($resultquest)>0){
			//get the answer in score if any
			$queryscore="SELECT tblscore.answerid FROM tblscore where(questionid='$questionid' and 
			testid='$testid' and candidateid='$candidateid' )";
			$resultscore=mysql_query($queryscore);
			if(mysql_num_rows($resultscore)>0){
			$tblscoreanswerid=mysql_result($resultscore,0,'tblscore.answerid');
                        if($tblscoreanswerid=="") $tblscoreanswerid=0;
			
			}
			

			
			$optioncounter=0;
			echo"<ol class='ansopt'>";
				for($k=0;$k< mysql_num_rows($resultquest);$k++){
					$answerid=mysql_result($resultquest,$k,'tblpresentation.answerid');
					$answertext=mysql_result($resultquest,$k,'test');
                                        $answertext=stripslashes($answertext);
					//$tblscoreanswerid=mysql_result($resultquest,$k,'tblscore.answerid');
					//$answerid=mysql_result($resultquest,$k,'answerid');
					//$answerid=mysql_result($resultquest,$k,'answerid');
					//$optioncounter=integerToRoman($k+1);
					if($answerid==$tblscoreanswerid){
						echo"<li>
					<label class='selected-opt'><input type=radio class=\"answeroption\" name='$questionid' id='$answerid' value='$answerid' checked> <div style='display:inline-block; cursor:pointer;'> ".html_entity_decode($answertext, ENT_QUOTES)."</div></label> </li>";
					}
					else{
					echo"<li>  <label><input type=radio class=\"answeroption\"  name='$questionid' id='$answerid' value='$answerid'><div style='display:inline-block; cursor:pointer;'>".html_entity_decode($answertext, ENT_QUOTES)."</div></label> </li>";
					}
					
					
				}//endfor
				echo"</ol>";
				$code=$questionid."code";
				echo"<input type='hidden' name='$code' id='$code' value='$tblscoreanswerid'>";
					mysql_free_result($resultquest);
			}
		
	
	}
	else{
	//specify another questiontype
	}
	
}
	
function opensectiondiv($sectionname, $instruction){
	?>
	<div class="sectiondiv"  >
		<div id="sectioninfodiv" style="font-weight:bolder; ">
		<?php
		if($sectionname!=""){echo"	<div style='text-align:center; font-size:1.2em; text-decoration:underline;'><b>SECTION: <b/>".strtoupper($sectionname)." </div><br>";}
		if($instruction!=""){echo"<font size=3><b>INSTRUCTION: <b/>$instruction </font>";}
		echo"</div>";

}


function gettestinfo($testid){
$testinfo=array();
	if(isset($testid)){
	
	$querytestname=mysql_query("SELECT testname,testtypename, session, duration, dailystarttime, displaymode, startingmode,testcategory,
	questionadministration,optionadministration, curtime() as now
	FROM tbltestconfig inner join tbltestcode
	on tbltestconfig.testcodeid=tbltestcode.testcodeid
	left join tbltesttype on tbltestconfig.testtypeid=tbltesttype.testtypeid
	where(tbltestconfig.testid='$testid')");
	if( mysql_num_rows($querytestname)>0){
		$testname=mysql_result($querytestname,0,'testname');
		$now=mysql_result($querytestname,0,'now');
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
		$testinfo['startingmode']= $startingmode;
		$testinfo['duration']= $duration;
		$testinfo['starttime']= $dailystarttime;// student start time take into consideration if the student comes late
		$testinfo['dailystarttime']= $dailystarttime;
		//check any elapsed time that the student has used before login in again  and subtract it to the remaining time.
		$candidateid=$_SESSION['candidateid'];
		//get duration in second
		$duration=$duration*60;
		$elapsed=0;
		$queryelapsed=mysql_query("SELECT elapsed from tbltimecontrol where (testid='$testid' and candidateid='$candidateid')");
		if(mysql_num_rows($queryelapsed)>0){
		$elapsed=mysql_result($queryelapsed,0,'elapsed');
		$duration=$duration-$elapsed;
			
		}
                
		$testinfo['remainingsecond']= $duration;

		if($startingmode=='on login'){
		//start time on login
		$date = new DateTime($now);
		$testinfo['starttime']= $date->format('H:i:s');
		$date->modify("+ $duration seconds");
		}
		else{
		//start time as specified. get the ellapsed time and add to it 
			$date = new DateTime($dailystarttime);
			if($elapsed!=0){
			//student started exams before being log out. compute his ending time from now to the remaining seconds
			$date = new DateTime($now);
			}else{
			//first login check if he is late and reduce his remaining time
			$date2 = new DateTime($now);
			$to_time = strtotime($date2);
			$from_time = strtotime($date);
			$passsecond=abs($to_time - $from_time);
			$testinfo['remainingsecond']= $duration-$passsecond;

			
			}
			$date1=$date->format('H:i:s');
			$date->modify("+ $duration seconds");
			
			
			
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
		$testinfo['startingmode']= $startingmode;
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
	$subject="SELECT tblsubject.subjectid,tblsubject.subjectcode,tblsubject.subjectname, instruction from tblsubject 
	inner join tblcandidatestudent on tblcandidatestudent.subjectid=tblsubject.subjectid
	inner join tblscheduling on tblcandidatestudent.scheduleid=tblscheduling.schedulingid
	Inner Join tbltestsubject ON tblsubject.subjectid=tbltestsubject.subjectid
	where(tblcandidatestudent.candidateid='$candidateid' and tblscheduling.testid='$testid'
	and tbltestsubject.testid='$testid')";
	$querysubject=mysql_query($subject);
	if( mysql_num_rows($querysubject)>0){
		//candidate registerer for exam. generate his hyperlink to start each subject.
		echo"<p style='color: #FFFFFF; font-size:14pt'>Select Subject:";
		for($i=0;$i<mysql_num_rows($querysubject);$i++){
		$subjectid=mysql_result($querysubject,$i,'subjectid');
		$subjectcode=mysql_result($querysubject,$i,'subjectcode');
		$subjectname=mysql_result($querysubject,$i,'subjectname');
		$instruction=mysql_result($querysubject,$i,'instruction');
	
		$studsubject['subjectid'][$i]=$subjectid;
		$studsubject['subjectcode'][$i]=$subjectcode;
		$studsubject['subjectname'][$i]=$subjectname;
		$studsubject['instruction'][$i]=$instruction;
		
		//echo"|<a href=\"#\" id='$subjectid'>$subjectname</a>";
		echo" <button id='$subjectid' class=\"cbtn subjbtn\">$subjectcode</button>";
		}
		$_SESSION['studsubject']=$studsubject;
		mysql_free_result($querysubject);
	}
	
}



function integerToRoman($integer)
{
 // Convert the integer into an integer (just to make sure)
 $integer = intval($integer);
 $result = '';
 
 // Create a lookup array that contains all of the Roman numerals.
 $lookup = array('M' => 1000,
 'CM' => 900,
 'D' => 500,
 'CD' => 400,
 'C' => 100,
 'XC' => 90,
 'L' => 50,
 'XL' => 40,
 'X' => 10,
 'IX' => 9,
 'V' => 5,
 'IV' => 4,
 'I' => 1);
 
 foreach($lookup as $roman => $value){
  // Determine the number of matches
  $matches = intval($integer/$value);
 
  // Add the same number of characters to the string
  $result .= str_repeat($roman,$matches);
 
  // Set the integer to be the remainder of the integer and the value
  $integer = $integer % $value;
 }
 
 // The Roman numeral should be built, return it
 return $result;
}

function getbiodata($candidateid){
$query=mysql_query("SELECT candidatetype, regno from tblscheduledcandidate where candidateid='$candidateid'");
if( mysql_num_rows($query)>0){
		$regno=mysql_result($query,0,'regno');
		$candidatetype=mysql_result($query,0,'candidatetype');
		//get the candidate name from the appropriate table
		if($candidatetype==3){
		//regular student
		$queryname=mysql_query("SELECT matricnumber, 
		surname, concat(IFNULL(firstname,' ') ,' ' , IFNULL(othernames,' ')) AS othername from tblstudents
		where matricnumber='$regno' ");

		}elseif($candidatetype==1){
		//jamb student
		$queryname=mysql_query("SELECT matricnumber, 
		surname, concat(IFNULL(firstname,' ') , IFNULL(othernames,' ')) AS othername from tblstudents
		where matricnumber='$regno' ");

		
		}elseif($candidatetype==2){
		//sbrs student
		$queryname=mysql_query("SELECT 	surname, concat(IFNULL(firstname,' ') , IFNULL(othernames,' ')) AS othername from tblstudents
		where matricnumber='$regno' ");

		}
		if( mysql_num_rows($queryname)>0){
			$surname=strtoupper(mysql_result($queryname,0,'surname'));
			$othername=ucfirst(strtolower(mysql_result($queryname,0,'othername')));
			$biodata['candidatename']=$surname.", ".$othername;
		}

			$biodata['matric']=$regno;


		mysql_free_result($query);
		$_SESSION['biodata']=$biodata;
			
	}
	return $biodata;

}

function authenticatecandidate($candidateid,$testid){
global $failedmessage;
//return true;
	//this function will check for valid testcomputer alreadylogin, doulelogin in one computer, 
	$testcomputer=testcomputer($mac=getmacaddress(),$candidateid);
	if($testcomputer==1){
		if(computerdoublelogin($testid)==false){
			if(alreadylogin($candidateid,$testid)==false){
				return true;
			}else{
			$failedmessage="you have already login in another computer.";
			if(checkcompletion($testid,$candidateid)==true){
			$failedmessage="you have already taken the exams.";
			}else{$failedmessage="you have already login in another computer.";}
		
			return false;
			}
		}else{
		$failedmessage="This computer is already in use by another candidate.";
		return false;
		}
	}elseif($testcomputer==2)
	{//incorect testcomputer
	$failedmessage="You are not scheduled in this venue.";
	return false;
	}elseif($testcomputer==3){
	$failedmessage="You are not scheduled in this venue today.";
	}
	else{
	$failedmessage="This computer is not allowed to take the test.";
	}
}


function testcomputer($mac,$candidateid){
// this function return 1 if the computer is registered under the venue the student is trying to take the test
//2 if accepted computer but candidate not in right venue
// 0 unknown computer 
$result=mysql_query("SELECT * from tblvenuecomputers where(computermac='$mac')");
if(mysql_num_rows($result)>0){
   //allowed computer
   $venueid=mysql_result($result,0,'venueid');
	//check if in right venue
	$result2=mysql_query("SELECT candidateid, curdate() as today, tblscheduling.date as examsdate from tblscheduling inner join tblcandidatestudent
on tblscheduling.schedulingid=tblcandidatestudent.scheduleid
 where(candidateid='$candidateid' and tblscheduling.venueid='$venueid')");
	if(mysql_num_rows($result2)>0){
	  //candidate in right venue
	  
		$today=mysql_result($result2,0,'today');
		$examsdate=mysql_result($result2,0,'examsdate');
		//echo $today, $examsdate; die();
		
		if($today==$examsdate){
	
	return 1;
	}else{
	return 3;//candidate not scheduled for today
	}
	}else{
	//the candidate is not scheduled in this venue
	return 2;
	}
	
}
else{
return 0;// computer not allowed

}
}



function computerdoublelogin($testid){
return false;
//if the same ip is active and attempts to login with another user name stop it until logout
$newip=getIpAddress();
$result=mysql_query("SELECT * from tbltimecontrol where(ip='$newip' and testid='$testid' and completed=0)");
if(mysql_num_rows($result)>0){
   //computer in use by another user
   return true;
}else{
return false;
}
}



function alreadylogin($candidateid,$testid){
//
$result=mysql_query("SELECT * from tbltimecontrol where(candidateid='$candidateid' and testid='$testid')");
if(mysql_num_rows($result)>0){
   //allowed computer
   $ip=mysql_result($result,0,'ip');
   if($ip==""){
   return 0;
   }else{
   // check if the ip is same with that of the user
   if($ip==getIpAddress()){
   return 0;
   }else{
   return 1;
   }
   
   }
}else
{
// this is the first login
return 0;
}

}


function examsperiod($testid,$candidateid){
	$result2=mysql_query("SELECT candidateid, curdate() as today, curtime() as now, tblscheduling.date as examsdate, dailystarttime,dailyendtime
	from tbltestconfig inner join  tblscheduling on tbltestconfig.testid=tblscheduling.testid  inner join tblcandidatestudent
on tblscheduling.schedulingid=tblcandidatestudent.scheduleid
 where(candidateid='$candidateid' and tblscheduling.testid='$testid')");
 
 
	if(mysql_num_rows($result2)>0){
	  
		$today=mysql_result($result2,0,'today');
		$examsdate=mysql_result($result2,0,'examsdate');
		$dailystarttime=mysql_result($result2,0,'dailystarttime');
		$dailyendtime=mysql_result($result2,0,'dailyendtime');
		$now=mysql_result($result2,0,'now');
		
		
		
		//echo $today, $examsdate; die();
		
		if($today==$examsdate){
		if($dailystarttime<=$now){
		return true;
		}
}else{
return false;
}
}else{
return false;}

}
function checksecuritycode($candidateid,$securitycode){

$result=mysql_query("SELECT * from tbltimecontrol where(candidateid='$candidateid' and testid='$testid')");
if(mysql_num_rows($result)>0){
   //allowed computer
   $ip=mysql_result($result,0,'ip');
   
}
}


function getIpAddress(){
	header('Cache-Control: no-cache, must-revalidate');
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
	{
	  $ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
	  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
	  $ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}



function getmacaddress(){
$sample_arr = array();
$pingcommand = getIpAddress();
$sample_str = shell_exec('arp -a '.$pingcommand);
$sample_arr = explode("\n", $sample_str);
$sample_arr_ = explode(" ", $sample_arr[3]);
$final_arr = array();
foreach($sample_arr_ as $val){
	if($val != ""){
		$final_arr[] = $val;
	}
}
$mac = $final_arr[1];
return $mac;
}

?>