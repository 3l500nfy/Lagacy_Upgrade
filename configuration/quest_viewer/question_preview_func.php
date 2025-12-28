<?php
if (!isset($_SESSION))
    session_start();
//require_once("../lib/globals.php");
require_once("../../lib/cbt_func.php");
require_once("../../lib/html_sanitizer.php");

openConnection();
global $dbh;

function get_presentation($testid, $subjectid, $version) {
    displayallquestion($testid, $subjectid, $version);
}

function displayallquestion($testid, $subjectid, $version) {
    //create the questions
global $dbh;
    $questions = "SELECT distinct(tbltestquestion.questionbankid) as qbid, tblquestionbank.title as qtitle, tbltestsection.title as stitle, tbltestsection.testsectionid as sectionid, tbltestsection.instruction as sinstruction from 
    tbltestsubject inner join tbltestsection on (tbltestsubject.testsubjectid= tbltestsection.testsubjectid) 
    inner join tbltestquestion on (tbltestsection.testsectionid = tbltestquestion.testsectionid) 
    inner join tblquestionbank on (tbltestquestion.questionbankid=tblquestionbank.questionbankid)
    WHERE(tbltestquestion.version='$version' and tbltestsubject.subjectid='$subjectid' and tbltestsubject.testid='$testid')";

    $stmt=$dbh->prepare($questions);
    $stmt->execute();
    $num=$stmt->rowCount();
    $rows=$stmt->fetch(PDO::FETCH_ASSOC);

    if ($num > 0) {
        $sectiontitle = $rows['stitle'];
        $sectioninstruction = $rows['sinstruction'];
        //create new section
        $curentsectionid = $rows['sectionid'];
        opensectiondiv($sectiontitle, $sectioninstruction);
        $counter = 0;
        $i=0;
        while ($rows=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $sectionid = $rows['sectionid'];
            if (($sectionid != $curentsectionid) && $i > 0) {
                $curentsectionid = $sectionid;
                //close the previous section and create new section
                $sectiontitle = $rows['stitle'];
                $sectioninstruction = $rows['sinstruction'];
                echo"</div><hr style='border-width:2px; border-style:dotted;' />"; //close the section div;
                opensectiondiv($sectiontitle, $sectioninstruction);
            }
            $questionid = $rows['qbid'];
            $questiontitle = $rows['qtitle'];
            $questiontitle = stripslashes($questiontitle);
            // Decode HTML entities and sanitize before rendering (defense-in-depth)
            $decoded = html_entity_decode($questiontitle, ENT_QUOTES);
            $sanitized = sanitizeHtmlForRender($decoded);
            $counter = $i + 1;
            $divid = "questiondiv" . $questionid;
            ?>
            <div class="questionanswerdiv" id="<?php echo $divid ?>">
                <div class="qadiv"style="background-color:#ffffff"><div class="questiondiv" >
                        <?php echo " <b>Question $counter: </b>"; ?><?php echo $sanitized; ?>
                    </div>
                    <div class="answerdiv">
                        <?php
                        showquestion($questionid, $questiontype = "OBJ");
                        echo"</div>";
                        echo"	</div></div>";
                        $i++;
                    }//endfor

                    //closing questionanswerdiv and answerdiv
                   // mysql_free_result($resultquestion);
                }
            }

            function showquestion($questionid, $questiontype = "OBJ") {
    global $dbh;
                $tblscoreanswerid = 0;
                if ($questiontype == "OBJ") {
                    //objectve question
                    $queryquest = "SELECT tblansweroptions.questionbankid as aqbi,tblansweroptions.answerid as opid, correctness,test FROM tblansweroptions where(tblansweroptions.questionbankid='$questionid'
		)";
$stmt=$dbh->prepare($queryquest);
$stmt->execute();
$num=$stmt->rowCount();

                    if ($num > 0) {
                        //get the answer in score if any

                        $optioncounter = 0;
                        echo"<ol class='ansopt'>";
                        while ($rows=$stmt->fetch(PDO::FETCH_ASSOC)) {
                            $answerid = $rows['opid'];
                            $correctness = $rows['correctness'];
                            $answertext = $rows['test'];
                            $answertext = str_replace("&#160;", "", $answertext);
                            $answertext = str_replace("&#160;", "", $answertext);
                            $answertext = stripslashes($answertext);
                            $answertext = str_replace("&#160;", "", $answertext);
                            $answertext = str_replace("&#160;", "", $answertext);
                            // Decode HTML entities and sanitize before rendering (defense-in-depth)
                            $decoded_answer = html_entity_decode($answertext, ENT_QUOTES);
                            $sanitized_answer = sanitizeHtmlForRender($decoded_answer);
                            if ($correctness == "1") {
                                echo"<li class='answer'>
					<label class='optionlabel'><table class='answertb' style='margin:2px;'><tr><td>" . str_replace("&#160;","",$sanitized_answer) . "</td><td><img src='" . siteUrl("assets/img/tickIcon.png") . "' /></td></tr></table></label> </li>";
                            } else {

                                echo"<li>  <label class='optionlabel'>" . str_replace("&#160;","",$sanitized_answer) . "</label> </li>";
                            }
                        }//endfor
                        echo"</ol>";
                        $code = $questionid . "code";
                        echo"<input type='hidden' name='$code' id='$code' value='$tblscoreanswerid'>";
                       // mysql_free_result($resultquest);
                    }
                } else {
                    //specify another questiontype
                }
            }

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

                    /*                     * **********end************************* */
                    ?>