<?php if(!isset($_SESSION)) session_start ();
require_once 'globals.php';
require_once 'security.php';

function validateversion($testid, $versionid) {
    global $dbh;
    //this function check if the number of questions set is up to the defined number of questions during the test configuration for a specific version
    //return truue if the version has enough question and false otherwise.
    if (isset($testid) && isset($versionid)) {

        //  get all the subject for the test
        $question = "SELECT testid,subjectid from tbltestsubject WHERE(testid=?)";
        $result = $dbh->prepare($question);
		$result->execute(array($testid));
//return $result->rowCount();
	if ($result->rowCount() > 0) {
            $result_rows = $result->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($result_rows); $i++) {
                $subjectid = $result_rows[$i]['subjectid'];
                //for each subject get all the section and check if the section have enough questions
                $qsection = "SELECT testsectionid,num_toanswer,numofeasy,numofmoderate,numofdifficult
			from tbltestsection INNER JOIN tbltestsubject ON tbltestsection.testsubjectid=tbltestsubject.testsubjectid
			WHERE	(subjectid='$subjectid' and testid='$testid') order by testsectionid ";

				$resultsection = $dbh->prepare($question);
				$resultsection->execute(array($testid));
                if ($resultsection->rowCount() > 0) {
                    $resultsection_rows = $resultsection->fetchAll(PDO::FETCH_ASSOC);
                    for ($j = 0; $j < count($resultsection_rows); $j++) {
                        $testsectionid = $resultsection_rows[$j]['testsectionid'];
                        $num_toanswer = $resultsection_rows[$j]['num_toanswer'];
                        $numofdifficult = (int) $resultsection_rows[$j]['numofdifficult'];
                        $numofmoderate = (int) $resultsection_rows[$j]['numofmoderate'];
                        $numofeasy = (int) $resultsection_rows[$j]['numofeasy'];

                        //get what is actualy available and compare with the stated number
                        //number of easy already set
                        $questionsimple = ("SELECT count(tbltestquestion.questionbankid)as total FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid=? and difficultylevel=? and version=?)");
                        $resultsample = $dbh->prepare($questionsimple);
						$resultsample->execute(array($testsectionid, "simple", $versionid));
						(int) $resultsample->rowCount(0, 'total');
						//$simple = (int) mysql_result($questionsimple, 0, 'total');
						

                        //number of difficult already set
                        $questiondifficult = ("SELECT count(tbltestquestion.questionbankid)as total FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid=? and difficultylevel=? and version=?)");
					$resultdifficult = $dbh->prepare($questiondifficult);
						$resultdifficult->execute(array($testsectionid, "difficult", $versionid));
						(int) $resultdifficult->rowCount(0, 'total');
                        //$difficult = (int) mysql_result($questiondifficult, 0, 'total');

                        //number of moredifficult already set
                        $questionmoredifficult = ("SELECT count(tbltestquestion.questionbankid)as total FROM tbltestquestion
					inner join tblquestionbank on tbltestquestion.questionbankid=tblquestionbank.questionbankid
					WHERE(testsectionid=? and difficultylevel=? and version=?)");
					$resultmoredifficult = $dbh->prepare($questionmoredifficult);
						$resultmoredifficult->execute(array($testsectionid, "moredifficult", $versionid));
						(int) $resultmoredifficult->rowCount(0, 'total');
                       // $moredifficult = (int) mysql_result($questionmoredifficult, 0, 'total');
                        ///compare the number of questions. if one of the level is not yet set to the required number then return false
                        if (($numofeasy > $simple) || ($numofmoderate > $difficult) || ($numofdifficult > $moredifficult)) {
                            return false;
                        }
                    }
                } else {
                    //subject has no section
                    return false;
                }
            }
        } else {
            //test subject not set
            return false;
        }
    } else {
        //not identified. testid or versionid not set
        return false;
    }
    //if false is not yet returned then the question composition is correct
    return true;
}
?>
