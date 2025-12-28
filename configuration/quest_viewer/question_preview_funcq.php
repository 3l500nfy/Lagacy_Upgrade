<?php
if (!isset($_SESSION))
    session_start();
//require_once("../lib/globals.php");
require_once("../../lib/cbt_func.php");

openConnection();

function get_presentation($testid, $subjectid, $version) {
    displayallquestion($testid, $subjectid, $version);
}

function displayallquestion($testid, $subjectid, $version) {
    global $dbh;
    //create the questions

    $query = "SELECT distinct(tbltestquestion.questionbankid), tblquestionbank.title as qtitle, tbltestsection.title as stitle, tbltestsection.testsectionid as sectionid, tbltestsection.instruction as sinstruction from 
    tbltestsubject inner join tbltestsection on (tbltestsubject.testsubjectid= tbltestsection.testsubjectid) 
    inner join tbltestquestion on (tbltestsection.testsectionid = tbltestquestion.testsectionid) 
    inner join tblquestionbank on (tbltestquestion.questionbankid=tblquestionbank.questionbankid)
    WHERE(tbltestquestion.version=? and tbltestsubject.subjectid=? and tbltestsubject.testid=?)";
    //echo $questions; exit;
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($version,$subjectid,$testid));
    $row = $stmt->fetchAll();
//var_dump($row);exit;
if ($stmt->rowCount() > 0) {
$sectiontitle = $row['stitle'];
$sectioninstruction = $row['sinstruction'];
//create new section
$curentsectionid = $row['sectionid'];
opensectiondiv($sectiontitle, $sectioninstruction);
$counter = 0;
for ($i = 0; $i < $stmt->rowCount(); $i++) {
$sectionid = $row[$i]['sectionid'];
if (($sectionid != $curentsectionid) && $i > 0) {
    $curentsectionid = $sectionid;
    //close the previous section and create new section
    $sectiontitle = $row[$i]['stitle'];
    $sectioninstruction = $row[$i]['sinstruction'];
    echo "</div><hr style='border-width:2px; border-style:dotted;' />"; //close the section div;
    opensectiondiv($sectiontitle, $sectioninstruction);
}
$questionid = $row[$i]['tbltestquestion.questionbankid'];
$questiontitle = $row[$i]['qtitle'];
$questiontitle = stripslashes($questiontitle);
$counter = $i + 1;
$divid = "questiondiv" . $questionid;
?>
<div class="questionanswerdiv" id="<?php echo $divid ?>">
    <div class="qadiv" style="background-color:#ffffff">
        <div class="questiondiv">
            <?php echo " <b>Question $counter: </b>"; ?><?php echo html_entity_decode($questiontitle, ENT_QUOTES); ?>
        </div>
        <div class="answerdiv">
            <?php
            showquestion($questionid, $questiontype = "OBJ");
            echo "</div>";
            echo "	</div></div>";
            }//endfor
            //closing questionanswerdiv and answerdiv
            //mysql_free_result($resultquestion);
            }
            }

            function showquestion($questionid, $questiontype = "OBJ") {
    global $dbh;
                $tblscoreanswerid = 0;
                if ($questiontype == "OBJ") {
                    //objectve question
                    $query = "SELECT tblansweroptions.questionbankid,tblansweroptions.answerid,
		correctness,test FROM tblansweroptions where(tblansweroptions.questionbankid=?)";
                    $stmt=$dbh->prepare($query);
                    $stmt->execute(array($questionid));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($stmt->rowCount() > 0) {
                        //get the answer in score if any

                        $optioncounter = 0;
                        echo"<ol class='ansopt'>";
                        for ($k = 0; $k < $stmt->rowCount(); $k++) {
                            $answerid = $row[$k]['tblansweroptions.answerid'];
                            $correctness = $row[$k]['tblansweroptions.correctness'];
                            $answertext = $row[$k]['test'];
                            $answertext = str_replace("&#160;", "", $answertext);
                            $answertext = str_replace("&#160;", "", $answertext);
                            $answertext = stripslashes($answertext);
                            $answertext = str_replace("&#160;", "", $answertext);
                            $answertext = str_replace("&#160;", "", $answertext);
                            if ($correctness == "1") {
                                echo"<li class='answer'>
					<label class='optionlabel'><table class='answertb' style='margin:2px;'><tr><td>" . str_replace("&#160;","",html_entity_decode($answertext, ENT_QUOTES)) . "</td><td><img src='" . siteUrl("assets/img/tickIcon.png") . "' /></td></tr></table></label> </li>";
                            } else {

                                echo"<li>  <label class='optionlabel'>" . str_replace("&#160;","",html_entity_decode($answertext, ENT_QUOTES)) . "</label> </li>";
                            }
                        }//endfor
                        echo"</ol>";
                        $code = $questionid . "code";
                        echo"<input type='hidden' name='$code' id='$code' value='$tblscoreanswerid'>";
                        //mysql_free_result($resultquest);
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
