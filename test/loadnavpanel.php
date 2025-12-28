<?php 
if(!isset($_SESSION)) {session_start();}
require_once '../lib/globals.php';
require_once('testfunctions.php');
openConnection(true);
global $dbh;
if (!isset($_SESSION['candidateid'])) {
     exit();
}

$candidateid=$_SESSION['candidateid'];
$testid=$_SESSION['testid'];
$subjectid=$_SESSION['curentsubject'];

//get the question already answered from score and place in array.
$already=array();
$queryanswered="SELECT tblscore.questionid as sqid from tblscore WHERE(candidateid='$candidateid' and testid='$testid' )";
$stmt = $dbh->prepare($queryanswered);
$result = $stmt->execute();
$numrows = $stmt->rowCount();
if($numrows>0){
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $already[] = $row['sqid'];
}

}

$studentquestion="SELECT distinct(tblpresentation.questionid), tblpresentation.candidateid,tblpresentation.testid from tblpresentation 
                 INNER JOIN tbltestsection on tblpresentation.sectionid=tbltestsection.testsectionid
		INNER JOIN tblquestionbank on tblpresentation.questionid=tblquestionbank.questionbankid
		INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid	
               WHERE (tblpresentation.candidateid='$candidateid' and tblpresentation.testid='$testid'
		and tbltestsubject.subjectid='$subjectid' )";
                
                $stmt1 = $dbh->prepare($studentquestion);
                $result = $stmt1->execute();
                $numrows = $stmt1->rowCount();
		
                if($numrows>0){
                    
                    echo"  <div id='qnavtitle'>NAVIGATIONS</div>";
                   
		
			$counter=0;
			for($i=0;$i<$numrows;$i++){
                                $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
                                $msg="";
                                //$questionid=mysql_result($resultquestion,$i,'questionid');
                                $questionid=$row1['questionid'];
                                if (in_array($questionid, $already)) {
                                           $msg="  background-color:greenyellow; background-image:url(tickIcon.png); ";  
                                           $qclass="answered";
                                           $title="Answered - click to go to question";
           

                                   // echo "y";
                                    
                                }
                                else
                                {
                                    $qclass="notanswered";
                                    $title="Not answered - click to go to question";
                                }
				
				$counter=$i+1;
                                $questionid="navpanel".$questionid;
                                $name="question".$i;
				

				echo "<div class=\"qbut $qclass\"  title='$title' id=\"$questionid\" name=\"$name\"style=\"$msg\" ><a href=\"javascript:void(0);\"> $counter </a></div>";
                                 
				
			}//endfor
	
		//mysql_free_result($resultquestion);
$stmt1->closeCursor();
		}
                


?>
