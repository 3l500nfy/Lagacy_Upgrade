<?php

require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");

openConnection();

// Check if required POST variables are set
if (!isset($_POST['candidateid']) || !isset($_POST['testid'])) {
    echo "<div class='alert-error'>Error: Required parameters are missing.</div>";
    exit();
}

$candidateid = $_POST['candidateid'];
$testid = $_POST['testid'];

?>

<div id="resultdiv">
		<?php
                 function opensectiondiv($sectionname, $instruction) {
                ?>
                            <div class="sectiondiv"  >
                                <div id="sectioninfodiv" style="font-weight:normal;">
                            <?php
                            if ($sectionname != "") {
                                echo"	<div style='text-align:center; font-size:1.2em; text-decoration:underline;'><b>SECTION: </b>" . strtoupper($sectionname) . " </div>";
                            }
                            if (trim($instruction) != "") {
                                echo"<div style='text-align:center; font-size:1.2em;'><b>INSTRUCTION: </b>$instruction </div>";
                            }
                            echo"</div>";
                        }
                        
function displayallquestion($candidateid, $testid, $subjectid) {
    global $dbh;
    //create the questions

    $studentquestion = "SELECT distinct(questionid),tblpresentation.candidateid,tblpresentation.testid,tblpresentation.sectionid,
		tblquestionbank.title, tbltestsection.title,tbltestsection.instruction from tblpresentation 
		INNER JOIN tbltestsection on tblpresentation.sectionid=tbltestsection.testsectionid
		INNER JOIN tblquestionbank on tblpresentation.questionid=tblquestionbank.questionbankid
		INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid		
		WHERE(tblpresentation.candidateid=? and tblpresentation.testid=?
		and tbltestsubject.subjectid=?)";
    $stmt = $dbh->prepare($studentquestion);
    $stmt->execute(array($candidateid, $testid, $subjectid));
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($row) > 0) {
        $sectiontitle = $row[0]['title'];
        $sectioninstruction = $row[0]['instruction'];
        //create new section
        $curentsectionid = $row[0]['sectionid'];
        opensectiondiv($sectiontitle, $sectioninstruction);
        $counter = 0;
        for ($i = 0; $i < count($row); $i++) {
            $sectionid = $row[$i]['sectionid'];
            if (($sectionid != $curentsectionid) && $i > 0) {
                $curentsectionid = $sectionid;
                //close the previous section and create new section
                $sectiontitle = $row[$i]['title'];
                $sectioninstruction = $row[$i]['instruction'];
                echo"</div><hr style='border-width:2px; border-style:dotted;' />"; //close the section div;
                opensectiondiv($sectiontitle, $sectioninstruction);
            }
            $questionid = $row[$i]['questionid'];
            $questiontitle = $row[$i]['title'];

            $questiontitle = stripslashes($questiontitle);
            $counter = $i + 1;
            $divid = "questiondiv" . $questionid;
            ?>
            <div class="questionanswerdiv" id="<?php echo $divid ?>">
                <div class="qadiv"style="background-color:#ffffff"><div class="questiondiv" >
                        <?php echo " <b>Question $counter: </b>"; ?><b/><?php echo html_entity_decode($questiontitle, ENT_QUOTES); ?>
                    </div>
                    <div class="answerdiv">
                        <?php
                        showquestion($questionid, $testid, $candidateid, $questiontype = "OBJ");
                        echo"</div>";
                        echo"	</div></div>";
                    }//endfor
                }
            }

            function showquestion($questionid, $testid, $candidateid, $questiontype = "OBJ") {
                global $dbh;
                $tblscoreanswerid = 0;
                if ($questiontype == "OBJ") {
                    //objectve question
                    $queryquest = "SELECT tblpresentation.questionid,tblpresentation.answerid,
		correctness,test FROM tblpresentation
		INNER JOIN tblansweroptions ON tblpresentation.answerid=tblansweroptions.answerid
		where(tblpresentation.questionid=? and 
		tblpresentation.testid=? and tblpresentation.candidateid=?
		)";
                    $stmt = $dbh->prepare($queryquest);
                    $stmt->execute(array($questionid, $testid, $candidateid));
                    $row1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($row1) > 0) {
                        //get the answer in score if any
                        $queryscore = "SELECT tblscore.answerid FROM tblscore where(questionid=? and 
			testid=? and candidateid=? )";
                        $stmt = $dbh->prepare($queryscore);
                        $stmt->execute(array($questionid, $testid, $candidateid));
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($stmt->rowCount() > 0) {
                            $tblscoreanswerid = $row['answerid'];
                            if ($tblscoreanswerid == "")
                                $tblscoreanswerid = 0;
                        }

                        $optioncounter = 0;
                        echo"<ol class='ansopt'>";
                        for ($k = 0; $k < count($row1); $k++) {
                            $answerid = $row1[$k]['answerid'];
                            $correctness = $row1[$k]['correctness'];
                            $answertext = $row1[$k]['test'];
                            $answertext = stripslashes($answertext);

                            if($correctness == 1){
                                if ($tblscoreanswerid==$answerid) {
                                    echo"<li> <label class='selected-opt optionlabel'> <div style='background-color: #8dc96e; display:inline-block; '> " . html_entity_decode($answertext, ENT_QUOTES) . " </div> </label>  </li>";
                                } else {
                                    echo"<li>  <label class=' optionlabel'><div style='background-color: #8dc96e;display:inline-block; '>" . html_entity_decode($answertext, ENT_QUOTES) . "</div></label> </li>";
                                }
                            } else {
                                if ($tblscoreanswerid==$answerid) {
                                    echo"<li> <label class=' selected-opt optionlabel'> <div style='display:inline-block;  '> " . html_entity_decode($answertext, ENT_QUOTES) . " </div> </label>  </li>";
                                } else {
                                    echo"<li>  <label class='optionlabel'><div style='display:inline-block; '>" . html_entity_decode($answertext, ENT_QUOTES) . "</div></label> </li>";
                                }
                            }
                        }
                        echo"</ol>";
                        $code = $questionid . "code";
                        echo"<input type='hidden' name='$code' id='$code' value='$tblscoreanswerid'>";
                    }
                }
            }
            
            $query = "SELECT distinct tblsubject.subjectid, subjectcode, subjectname from tblsubject 
                inner join tbltestsubject on tbltestsubject.subjectid=tblsubject.subjectid 
                inner join tbltestsection on tbltestsection.testsubjectid=tbltestsubject.testsubjectid
                inner join tblpresentation on tblpresentation.sectionid=tbltestsection.testsectionid
                where(tblpresentation.testid=? and tblpresentation.candidateid=?)";
            $stmt = $dbh->prepare($query);
            $stmt->execute(array($testid, $candidateid));
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($row2); $i++) {
                $subjectid = $row2[$i]['subjectid'];
                $subjectcode = $row2[$i]['subjectcode'];
                $subjectname = $row2[$i]['subjectname'];
               
                echo"<div style=' text-align: center; font-size: 2em;'>SUBJECT:$subjectcode( $subjectname )</div>  ";
                displayallquestion($candidateid, $testid, $subjectid);
            }
            ?>   
		 
        </div>
