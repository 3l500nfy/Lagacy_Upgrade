<?php
if (!isset($_SESSION))
    session_start();
//require_once("../lib/globals.php");
require_once("../lib/cbt_func.php");

openConnection();
global $dbh;

// function get_test_id() return a test id if exist or 0;

function get_test_id($sess, $code, $ttype, $sem, $tcat)
{
    global $dbh;

    $query = "select  testid from tbltestconfig where session=? && testcodeid= ? && testtypeid=? && semester=? && testcategory=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($sess, $code, $ttype, $sem, $tcat));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['testid'];

    }
    return 0;


}

//function get_all_candidate_as_array() return all candidates who successfully sat for the exam
function get_all_candidate_as_array($tid, $options)
{
    global $dbh;
    $cd = array();
    $test_params = get_test_config_param_as_array($tid);
    //echo $options['category'];
    if ($options['category'] == "regno") {
        $regno = $options['regno'];
        if ($test_params['testname'] == 'Post-UTME') {
            $candidateid = get_candidate_id($regno, 1);
        } else if ($test_params['testname'] == 'SBRS') {
            $candidateid = get_candidate_id($regno, 2);
        } else if ($test_params['testname'] == 'SBRS-NEW') {
            $candidateid = get_candidate_id($regno, 4);
        } else {
            $candidateid = get_candidate_id($regno, 3);
        }
        $query = "select distinct candidateid from tblpresentation where testid= $tid && candidateid = $candidateid ";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cd[] = $row['candidateid'];
        }


    } else if ($options['category'] == 'schedule') {
        $schd = $options['schedule'];
        $candidateids = get_candidate_id_as_array(array($schd));
        $candidateidsstr = trim(implode(",", $candidateids), ", ");

        $query = "select distinct candidateid from tblpresentation where testid= $tid && candidateid in ($candidateidsstr) ";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cd[] = $row['candidateid'];
        }

    } else {
        $query = "select distinct candidateid from tblpresentation where testid=? ";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($tid));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cd[] = $row['candidateid'];
        }
    }
    return $cd;
    //var_dump($cd);
}

function get_subject_code_name($sbj)
{
    global $dbh;
    $query = "select subjectcode, subjectname from tblsubject where subjectid=$sbj";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $result = $row['subjectcode'] . "-" . $row['subjectname'];
    return $result;
}

function get_subject_registered_as_array($tid, $cid)
{
    global $dbh;
    $s = array();
    $query = "select subjectid from tblcandidatestudent where scheduleid in (select scheduleid from tblscheduling where testid=?) && candidateid=?";
    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid, $cid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $s[$i] = $row['subjectid'];
        $i++;
    }
    return $s;
}

//function get_all_question_as_array() return all questions registered on a test
function get_all_questionid_as_array($tid, $sbj = '')
{
    global $dbh;
    $q = array();
    if ($sbj == '')
        $query = "select questionbankid from tbltestquestion inner join tbltestsection on(tbltestquestion.testsectionid=tbltestsection.testsectionid) inner join tbltestsubject on(tbltestsection.testsubjectid=tbltestsubject.testsubjectid) where tbltestsubject.testid='$tid'";

    else
        $query = "select questionbankid from tbltestquestion inner join tbltestsection on(tbltestquestion.testsectionid=tbltestsection.testsectionid) inner join tbltestsubject on(tbltestsection.testsubjectid=tbltestsubject.testsubjectid) where tbltestsubject.subjectid='$sbj' && tbltestsubject.testid='$tid'";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $q[] = $row['questionbankid'];
    }
    return $q;
}

function get_questions($quests, $sbjid = "all")
{
    global $dbh;
    //$param = 0;
    $quest = trim(implode(",", $quests), ", ");
    $output = "No Question found.";
    if ($sbjid == "all") {
        $query = "select * from tblquestionbank where questionbankid in ($quest)";
        //$param = array(1);
    } else {
        $query = "select * from tblquestionbank where questionbankid in ($quest) && subjectid=$sbjid";
        //$param = array($quest, 2);
    }
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $numrow = $stmt->rowCount();

    //$query = mysql_query($sql) or die("Error in function (get_questions)");
    if ($numrow > 0) {
        $output = "<div id='allq' class='quests'>All Question</div>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qst = $row['title'];
            $qid = $row['questionbankid'];
            $output .= "<div id='$qid' class='quests'>" . html_entity_decode(stripslashes($qst), ENT_QUOTES) . "</div>";
        }
    }
    return $output;
}

function get_question_options_as_array($qid)
{
    global $dbh;
    $opts = array();
    $query = "select * from tblansweroptions where questionbankid= ?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($qid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $opts[] = array('answerid' => $row['answerid'], 'test' => $row['test'], 'correctness' => $row['correctness']);
    }
    return $opts;
}

function get_question_pass_count($qid, $tid, $options)
{
    global $dbh;
    $copt = 0;
    foreach ($options as $opt) {
        if ($opt['correctness'] == 1) {
            $copt = $opt['answerid'];
            break;
        }
    }
    if ($copt == 0)
        return 0;
    else {
        $query = "select count(distinct candidateid) as pres from tblscore where testid=? && questionid=? && answerid=?";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($tid, $qid, $copt));
        $numrow = $stmt->rowCount();
        if ($numrow > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['pres'];
        } else
            return 0;
    }
}

function get_choice_count($tid, $optid)
{
    global $dbh;
    $query = "select count(*) AS total from tblscore where testid=? && answerid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid, $optid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}

function get_question_fail_count($qid, $tid, $options)
{
    global $dbh;
    $copt = 0;
    foreach ($options as $opt) {
        if ($opt['correctness'] == 1) {
            $copt = $opt['answerid'];
            break;
        }
    }

    $query = "select count(distinct candidateid) as pres from tblscore where testid=? && questionid=? && answerid<>?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid, $qid, $copt));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['pres'] > 0) {
        return $row['pres'];
    } else
        return 0;
}

function get_full_question($qid)
{
    global $dbh;
    $output = "";
    $query = "select title from tblquestionbank where questionbankid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($qid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output = $row['title'];
        return $output;
    }
    return $output;
}

function get_candidate_presented_count($qid, $tid)
{
    global $dbh;
    $query = "select count(distinct candidateid) as pres from tblpresentation where testid=? && questionid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid, $qid));
    $numrow = $stmt->rowCount();
    if ($numrow > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['pres'];
    } else
        return 0;
}

//get RegNo of candidate
function get_RegNo($candidate)
{
    global $dbh;
    $query = "select RegNo from tblscheduledcandidate where candidateid= ?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($candidate));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return strtoupper(trim($row['RegNo']));
    }
    return "";
}

/* /get the name of a programme
  function get_programme_name($progid) {
  $sql = "select name from tblprogramme where programmeid = '$progid'";
  $query = mysql_query($sql) or die("Error in function (get_programme_name)");
  while ($row = mysql_fetch_assoc($query)) {
  return $row['name'];
  }
  return "";
  }
 */

function get_programme_name($progid)
{
    global $dbh;
    $query = "select name from tblprogramme where programmeid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($progid));
    $numrow = $stmt->rowCount();
    if ($numrow > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['name'];
    } else
        return "";
}

//get the biodata of candidate
function get_candidate_biodata($candidate, $type = 'REGULAR')
{
    global $dbh;
    $bio = array();
    $regno = get_RegNo($candidate);
    if ($regno == "")
        return $bio;
    if ($type == 'REGULAR') {
        $query = "select * from tblstudents where matricnumber =? ";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($regno));
        $numrow = $stmt->rowCount();

        if ($numrow > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $bio['surname'] = $row['surname'];
            $bio['firstname'] = $row['firstname'];
            $bio['othernames'] = $row['othernames'];
            $bio['gender'] = $row['gender'];
            $bio['matricnumber'] = $row['matricnumber'];
            $bio['programme'] = get_programme_name($row['programmeadmitted']);
            $bio['programmeid'] = $row['programmeadmitted'];
        }
        return $bio;
    } else
        if ($type == 'Post-UTME') {
            $query = "select * from tbljamb where RegNo =?";
            $stmt = $dbh->prepare($query);
            $stmt->execute(array($regno));
            $numrow = $stmt->rowCount();
            if ($numrow > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $nm = $row['CandName'];
                $nm_arr = explode(" ", $nm);
                $pos = 1;
                $bio['surname'] = "";
                $bio['firstname'] = "";
                $bio['othernames'] = "";

                foreach ($nm_arr as $nmar) {
                    if (isset($nmar) && trim($nmar) != "") {
                        if ($pos == 1)
                            $bio['surname'] = $nmar;
                        else
                            if ($pos == 2)
                                $bio['firstname'] = $nmar;
                            else
                                $bio['othernames'] = $nmar;
                        $pos++;
                    }
                }
                $bio['gender'] = $row['Sex'];
                $bio['matricnumber'] = $row['RegNo'];
                $bio['programme'] = $row['Course'];
                $bio['programmeid'] = $row['Course'];
            }
            return $bio;
        } else
            if ($type == 'SBRS') {
                $query = "select * from tblsbrsstudents where (sbrsno=? || oldsbrsno=?)";
                $stmt = $dbh->prepare($query);
                $stmt->execute(array($regno, $regno));
                $numrow = $stmt->rowCount();
                if ($numrow > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $bio['surname'] = strtoupper($row["surname"]);
                    $bio['firstname'] = ucfirst(strtolower($row["firstname"]));
                    $bio['othernames'] = ucfirst(strtolower($row["othernames"]));
                    $bio['gender'] = $row["gender"];
                    $bio['matricnumber'] = strtoupper($row["sbrsno"]);
                    $bio['programme'] = $row["combination"];
                    $bio['programmeid'] = $row["combination"];
                }
                return $bio;
            } else
                if ($type == 'SBRS-NEW') {
                    $query = "select * from tbljamb where RegNo =?";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute(array($regno));
                    $numrow = $stmt->rowCount();

                    if ($numrow > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $bio['surname'] = $row['surname'];
                        $bio['firstname'] = $row['firstname'];
                        $bio['othernames'] = $row['othernames'];
                        $bio['gender'] = $row['gender'];
                        $bio['matricnumber'] = $row['sbrsno'];
                        $bio['programme'] = $row['firstc'];
                        $bio['programmeid'] = $row['firstchoice'];
                    }
                    return $bio;
                }
}

function get_candidate_faculty($regnum, $stdtype)
{
    global $dbh;
    $result = array();
    if ($stdtype == "REGULAR") {
        $query = "select tblfaculty.facultyid, tblfaculty.name from tblstudents inner join tblprogramme on (tblstudents.programmeadmitted = tblprogramme.programmeid) inner join tbldepartment on (tblprogramme.departmentid = tbldepartment.departmentid) inner join tblfaculty on (tbldepartment.facultyid = tblfaculty.facultyid) where matricnumber = ? ";
    } else if ($stdtype == "Post-UTME") {
        $query = "select tblfaculty.facultyid, tblfaculty.name from tbljamb inner join tblfaculty on (tblfaculty.name = tbljamb.Faculty) where tbljamb.RegNo =?";
    }
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($regnum));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result['facultyid'] = $row['facultyid'];
        $result['facultyname'] = $row['name'];
        return $result;
    }
    return $result;
}

function get_candidate_department($regnum, $stdtype)
{
    global $dbh;
    $result = array();
    if ($stdtype == "REGULAR") {
        $query = "select tbldepartment.departmentid, tbldepartment.name from tblstudents inner join tblprogramme on (tblstudents.programmeadmitted = tblprogramme.programmeid) inner join tbldepartment on (tblprogramme.departmentid = tbldepartment.departmentid) where matricnumber =? ";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($regnum));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result['departmentid'] = $row['departmentid'];
            $result['departmentname'] = $row['name'];
            return $result;
        }
    }
    return $result;
}

function get_candidate_programme($regnum, $stdtype)
{
    global $dbh;
    $result = array();
    if ($stdtype == "REGULAR") {
        $query = "select programmeid, name from tblstudents inner join tblprogramme on (tblstudents.programmeadmitted = tblprogramme.programmeid) where matricnumber = ? ";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($regnum));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result['programmeid'] = $row['departmentid'];
            $result['programmename'] = $row['name'];
            return $result;
        }

    }
    return $result;
}

function get_candidate_state($regnum, $stdtype)
{
    global $dbh;
    $result = array();
    if ($stdtype == "REGULAR") {
        $query = "select tblstate.stateid, tblstate.statename from tblstudents inner join tblstate on (tblstudents.state = tblstate.stateid) where matricnumber = ? ";
    } else if ($stdtype == "Post-UTME") {
        $query = "select tblstate.stateid, tblstate.statename from tbljamb inner join tblstate on (tblstate.statename = tbljamb.StateOfOrigin) where tbljamb.RegNo =?";
    }
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($regnum));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result['stateid'] = $row['departmentid'];
        $result['statename'] = $row['name'];
        return $result;
    }

    return $result;
}

function get_candidate_lapse_time($candidate, $tid)
{
    global $dbh;
    $query = "select elapsed from tbltimecontrol where candidateid = ? && testid = ?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($candidate, $tid));
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['elapsed'];
    }
    return -1;
}

function get_candidate_lga($stdtype, $regnum)
{
    global $dbh;
    $result = array();
    if ($stdtype == "REGULAR") {
        $query = "select tbllga.lgaid, tbllga.lganame from tblstudents inner join tbllga on (tblstudents.lga = tbllga.lgaid) where matricnumber = ? ";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($regnum));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result['lgaid'] = $row['lgaid'];
            $result['lganame'] = $row['lganame'];
            return $result;
        }
    }
    return $result;
}

function get_mark_per_question($sid)
{
    global $dbh;
    $query = "select markperquestion from tbltestsection where testsectionid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($sid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['markperquestion'];
    }
    return 0;
}

//function get_candidate_aggregate()
function get_candidate_subject_score($tid, $candidate, $sbj)
{
    $aggregate = 0;
    $scores = get_scores_as_array($tid, $candidate, $sbj);
    foreach ($scores as $score) {
        if (is_correct($score['answerid'])) {
            $point = get_mark_per_question(get_section_id($candidate, $score['questionid'], $tid));
            //return $candidate;
            $aggregate += $point;
        }
    }
    //return count($scores);exit;
    return $aggregate;
}

function get_subject_combination_as_array($tid)
{
    global $dbh;
    $s = array();
    $query = "select subjectid from tbltestsubject where testid = ?";
    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $s[] = $row['subjectid'];
        $i++;
    }
    return $s;
}

//function get_test_total_mark() returns the total mark of the test
function get_test_total_mark($tid)
{
    global $dbh;
    $query = "select SUM(num_toanswer * markperquestion) as totalmrk from tbltestsection where testsubjectid in (select testsubjectid from tbltestsubject where testid = ?)";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['totalmrk'];
    }
    return 0;
}

function get_subject_total_mark($sbjid, $tid)
{
    global $dbh;
    $query = "select SUM(num_toanswer * markperquestion) as totalmrk from tbltestsection where testsubjectid = (select testsubjectid from tbltestsubject where testid = ? && subjectid = ?)";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid, $sbjid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['totalmrk'];
    }
    return 0;
}

//function is_correct() returns true if the answer selected by the candidate is correct
function is_correct($option)
{
    global $dbh;
    $query = "select correctness from tblansweroptions where answerid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($option));
    $numrow = $stmt->rowCount();
    if ($numrow > 0) {
        $opt = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($opt['correctness'] == '1')
            return true;
        return false;
    }
    return false;
}

//function get_section_id() returns the section id given the questionid
function get_section_id($cid, $qid, $tid)
{
    global $dbh;
    $query = "select sectionid from tblpresentation where candidateid=? && testid=? && questionid=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($cid, $tid, $qid));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['sectionid'];
    }
    return 0;
}

//function get_scores_as_array() return the answer of the candidate as array
function get_scores_as_array($tid, $candidate, $sbj)
{
    global $dbh;
    $sc = array();
    $query = "select * from tblscore where testid=$tid && candidateid=$candidate && questionid in (select questionbankid from tblquestionbank where subjectid = $sbj)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sc[] = array('candidateid' => $row['candidateid'], 'testid' => $row['testid'], 'questionid' => $row['questionid'], 'answerid' => $row['answerid']);
    }
    return $sc;
}

// function get_sessions(plan): returns all the session in which test was organised
function get_sessions($plan)
{
    global $dbh;
    $plan = trim($plan);
    $output = "";
    if ($plan == "sbrs") {
        $query = "select distinct session from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid = tbltestcode.testcodeid) where tbltestcode.testcodeid = '2' order by session desc";
    } else
        if ($plan == "post-utme") {
            $query = "select distinct session from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid = tbltestcode.testcodeid) where tbltestcode.testcodeid = '1' order by session desc";
        } else
            if ($plan == "regular") {
                $query = "select distinct session from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid = tbltestcode.testcodeid) where (tbltestcode.testcodeid <> '2' && tbltestcode.testcodeid <> '1') order by session desc";
            }
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "<option value='" . $row['session'] . "'>" . $row['session'] . "</option>";
    }
    return $output;
}

function get_testtype()
{
    global $dbh;
    $output = "";
    $query = "select * from tbltesttype";
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "<option value='" . $row['testtypeid'] . "'>" . $row['testtypename'] . "</option>";
    }
    return $output;
}

function get_active_category_as_options($type, $session)
{
    global $dbh;
    $output = "";
    if ($type == "regular")
        $query = "select distinct tbltestconfig.testcategory from tbltestconfig where session= '$session' && testcodeid<>'1' && testcodeid<>'2'";
    else
        if ($type == "post-utme")
            $query = "select distinct tbltestconfig.testcategory from tbltestconfig where session= '$session' && testcodeid='1'";
        else
            if ($type == "sbrs")
                $query = "select distinct tbltestconfig.testcategory from tbltestconfig where session= '$session' && testcodeid='2'";
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($type == 'sbrs')
            $output .= "<option value='" . $row['testcategory'] . "'>" . (($row['testcategory'] == "Single Subject") ? ("Continuous Assessment") : ("Exam")) . "</option>";
        else if ($type == 'post-utme') {
            $output .= "<option value='Multi-Subject'>Exam</option>";
        } else if ($type == 'regular') {
            $output .= "<option value='Single Subject'>Test</option>";
        }
    }
    return $output;
}

function get_active_testcodes_as_options($type, $session)
{
    global $dbh;
    $output = "";
    if ($type == "regular")
        $query = "select distinct tbltestconfig.testcodeid, testname from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid= tbltestcode.testcodeid) where (tbltestcode.testcodeid <> '2' && tbltestcode.testcodeid <> '1') && session= '$session'";
    else
        if ($type == "post-utme")
            $query = "select distinct tbltestconfig.testcodeid, testname from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid= tbltestcode.testcodeid) where (tbltestcode.testcodeid = '1') && session= '$session'";
        else
            if ($type == "sbrs")
                $query = "select distinct tbltestconfig.testcodeid, testname from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid= tbltestcode.testcodeid) where (tbltestcode.testcodeid = '2') && session= '$session'";

    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "<option value='" . $row['testcodeid'] . "'>" . $row['testname'] . "</option>";
    }
    return $output;
}

function get_active_testtype_as_options($type, $session, $code)
{
    global $dbh;
    $output = "";
    if ($type == "regular")
        $query = "select distinct tbltestconfig.testtypeid, testtypename from tbltestconfig inner join tbltesttype on (tbltestconfig.testtypeid= tbltesttype.testtypeid) where session= '$session' && testcodeid='$code'";
    else
        if ($type == "post-utme")
            $query = "select distinct tbltestconfig.testtypeid, testtypename from tbltestconfig inner join tbltesttype on (tbltestconfig.testtypeid= tbltesttype.testtypeid) where session= '$session' && testcodeid='$code'";
        else
            if ($type == "sbrs")
                $query = "select distinct tbltestconfig.testtypeid, testtypename from tbltestconfig inner join tbltesttype on (tbltestconfig.testtypeid= tbltesttype.testtypeid) where session= '$session' && testcategory='$code' && testcodeid='2'";

    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "<option value='" . $row['testtypeid'] . "'>" . $row['testtypename'] . "</option>";
    }
    return $output;
}

function get_active_testsemester_as_options($type, $session, $code, $ttype)
{
    global $dbh;
    $output = "";
    if ($type == "regular")
        $query = "select distinct semester from tbltestconfig where session= '$session' && testcodeid='$code' && testtypeid='$ttype'";
    else
        if ($type == "post-utme")
            return "";
        else
            if ($type == "sbrs")
                $query = "select distinct semester from tbltestconfig where session= '$session' && testcodeid='2' && testcategory='$code' && testtypeid='$ttype'";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sem = $row['semester'];
        if ($type == "regular") {

            $semname = (($sem == 1) ? ("First") : (($sem == 2) ? ("Second") : ("Third")));

            $output .= "<option value='$sem'>$semname</option>";
        }

        if ($type == "sbrs") {
            if ($ttype == 1)
                $semname = (($sem == 0) ? ("Entrance Exam") : (($sem == 1) ? ("Mid Term") : ("Final Term")));
            if ($ttype != 1)
                $semname = (($sem == 1) ? ("First Term") : ("Second Term"));
            $output .= "<option value='$sem'>$semname</option>";
        }
    }
    return $output;
}

function get_state_as_filter_checkbox($statesArray = array())
{
    global $dbh;
    $output = "<label class='filter-label'><input type='checkbox' name='filter-state[]' class='filter-state-all' checked value=''/> All</label>";
    $query = "select * from tblstate";
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (count($statesArray)) {
            foreach ($statesArray as $st) {
                if ($row['stateid'] == $st) {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-state[]' class='filter-state' checked='checked' value='" . $row['stateid'] . "'/> " . $row['statename'] . "</label>";
                } else {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-state[]' class='filter-state' value='" . $row['stateid'] . "'/> " . $row['statename'] . "</label>";
                }
            }
        } else {
            $output .= "<label class='filter-label'><input type='checkbox' name='filter-state[]' checked class='filter-state' value='" . $row['stateid'] . "'/> " . $row['statename'] . "</label>";
        }
    }
    return $output;
}

function get_department_as_checkbox($dids)
{
    global $dbh;
    $did = trim(implode(",", $dids), ", ");
    $output = "";
    $query = "select * from tbldepartment where departmentid in ($did)";
    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $output .= "<label class='filter-label'><input type='checkbox' name='filter-dept[]' data-facid='" . $row['facultyid'] . "' class='filter-dept' checked value='" . $row['departmentid'] . "'/> " . $row['name'] . "</label>";
    }
    return $output;
}

function get_faculty_as_checkbox($fids)
{
    global $dbh;
    $fid = trim(implode(",", $fids), ", ");
    $output = "";
    $query = "select * from tblfaculty where facultyid in ($fid)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $output .= "<label class='filter-label'><input type='checkbox' name='filter-faculty[]' class='filter-faculty' checked value='" . $row['facultyid'] . "'/> " . $row['name'] . "</label>";
    }
    return $output;
}

function get_faculty_as_checkbox2($regnos)
{
    global $dbh;
    $regno = array();
    foreach ($regnos as $rg) {
        $regno[] = "'" . $rg . "'";
    }

    $fid = trim(implode(",", $regno), ", ");
    $output = "";
    $query = "select distinct Faculty from tbljamb where RegNo in ($fid) order by Faculty asc";
    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $output .= "<label class='filter-label'><input type='checkbox' name='filter-faculty[]' class='filter-faculty' checked value='" . $row['Faculty'] . "'/> " . ucfirst(strtolower($row['Faculty'])) . "</label>";
    }
    return $output;
}

function get_programme_as_checkbox($pids)
{
    global $dbh;
    $pid = trim(implode(",", $pids), ", ");
    $output = "";
    $query = "select * from tblprogramme where programmeid in ($pid)";

    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $output .= "<label class='filter-label'><input type='checkbox' name='filter-prog[]' data-deptid='" . $row['departmentid'] . "' class='filter-prog' checked value='" . $row['programmeid'] . "'/> " . $row['name'] . "</label>";
    }
    return $output;
}

function get_programme_as_checkbox2($regnums)
{
    global $dbh;
    $regnos = array();
    while ($rg = each($regnums)) {
        $regnos[] = "'" . $rg[1] . "'";
    }

    $regno = trim(implode(",", $regnos), ", ");
    $output = "";
    $query = "select distinct Course from tbljamb where RegNo in ($regno)";
    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $output .= "<label class='filter-label'><input type='checkbox' name='filter-prog[]' data-deptid='" . $row['Course'] . "' class='filter-prog' checked value='" . $row['Course'] . "'/> " . $row['Course'] . "</label>";
    }
    return $output;
}

function get_param_programme_as_filter_checkbox($testid, $scheme = 3)
{
    global $dbh;
    $output = "<label class='filter-label'><input type='checkbox' name='filter-prog-all' class='filter-prog-all' checked value='all'/> All</label>";
    if ($scheme == 3) {
        $cids = get_all_candidate_as_array($testid);
        $regnos = get_regnum_as_array($cids);
        $pids = get_programme_id_as_array($regnos);
        $progs = get_programme_as_checkbox($pids);
        //$depts=get_department_as_checkbox($dids);
        if ($progs == "")
            return "No Programme";
        else {
            return ($output . $progs);
        }
    } else
        if ($scheme == 1) {
            $cids = get_all_candidate_as_array($testid);
            $regnos = get_regnum_as_array($cids);
            $progs = get_programme_as_checkbox2($regnos);
            //$depts=get_department_as_checkbox($dids);
            if ($progs == "")
                return "No Programme";
            else {
                return ($output . $progs);
            }
        }
}

function get_param_faculty_as_filter_checkbox($testid, $scheme = 3)
{
    global $dbh;
    $output = "<label class='filter-label'><input type='checkbox' name='filter-faculty-all' class='filter-faculty-all' id='filter-faculty-all' checked value='all'/> All</label>";

    if ($scheme == 3) {
        $cids = get_all_candidate_as_array($testid);
        $regnos = get_regnum_as_array($cids);
        $pids = get_programme_id_as_array($regnos);
        $dids = get_department_id_as_array($pids);
        $fids = get_faculty_id_as_array($dids);
        $facs = get_faculty_as_checkbox($fids);
        //$depts=get_department_as_checkbox($dids);
        if ($facs == "")
            return "No faculty";
        else {
            return ($output . $facs);
        }
    } else
        if ($scheme == 1) {
            $cids = get_all_candidate_as_array($testid);
            $regnos = get_regnum_as_array($cids);
            $facs = get_faculty_as_checkbox2($regnos);
            if ($facs == "")
                return "No faculty";
            else {
                return ($output . $facs);
            }
        }
}

function get_param_department_as_filter_checkbox($testid)
{
    global $dbh;
    $output = "<label class='filter-label'><input type='checkbox' name='filter-dept-all' class='filter-dept-all' checked value='all'/> All</label>";
    $cids = get_all_candidate_as_array($testid);
    $regnos = get_regnum_as_array($cids);
    $pids = get_programme_id_as_array($regnos);
    $dids = get_department_id_as_array($pids);
    $depts = get_department_as_checkbox($dids);
    if ($depts == "")
        return "No department";
    else {
        return ($output . $depts);
    }
}

function get_faculty_as_filter_checkbox($facArray = array())
{
    global $dbh;
    $output = "<label class='filter-label'><input type='checkbox' name='filter-faculty-all' class='filter-faculty-all' checked value='all'/> All</label>";
    $query = "select * from tblfaculty";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (count($facArray)) {
            foreach ($facArray as $st) {
                if ($row['facultyid'] == $st) {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-faculty[]' class='filter-faculty' checked='checked' value='" . $row['facultyid'] . "'/> " . $row['name'] . "</label>";
                } else {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-faculty[]' class='filter-faculty' value='" . $row['facultyid'] . "'/> " . $row['name'] . "</label>";
                }
            }
        } else {
            $output .= "<label class='filter-label'><input type='checkbox' name='filter-faculty[]' class='filter-faculty' checked='checked' value='" . $row['facultyid'] . "'/> " . $row['name'] . "</label>";
        }
    }
    return $output;
}

function get_department_as_filter_checkbox($deptArray = array(), $deptSet = array())
{
    global $dbh;
    $did = trim(implode(",", $deptSet), ", ");
    $output = "<label class='filter-label'><input type='checkbox' name='filter-dept-all' class='filter-dept-all' value='all'/> All</label>";
    $query = "select * from tbldepartment where departmentid in ($did)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (count($deptArray)) {
            foreach ($deptArray as $st) {
                if ($row['departmentid'] == $st) {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-dept[]' data-facid='" . $row['facultyid'] . "' class='filter-dept' checked value='" . $row['departmentid'] . "'/> " . $row['name'] . "</label>";
                } else {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-dept[]' data-facid='" . $row['facultyid'] . "' class='filter-dept' value='" . $row['departmentid'] . "'/> " . $row['name'] . "</label>";
                }
            }
        } else {
            $output .= "<label class='filter-label'><input type='checkbox' name='filter-dept[]' data-facid='" . $row['facultyid'] . "' class='filter-dept' value='" . $row['departmentid'] . "'/> " . $row['name'] . "</label>";
        }
    }
    return $output;
}

function get_programme_as_filter_checkbox($progArray = array(), $progSet = array())
{
    global $dbh;
    $pid = trim(implode(",", $progSet), ", ");
    $output = "<label class='filter-label'><input type='checkbox' name='filter-prog-all' class='filter-prog-all' value='all'/> All</label>";
    $query = "select * from tblprogramme where programmeid in ($pid)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (count($progArray)) {
            foreach ($progArray as $st) {
                if ($row['programmeid'] == $st) {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-prog[]' data-deptid='" . $row['departmentid'] . "' class='filter-prog' checked value='" . $row['programmeid'] . "'/> " . $row['name'] . "</label>";
                } else {
                    $output .= "<label class='filter-label'><input type='checkbox' name='filter-prog[]' data-deptid='" . $row['departmentid'] . "' class='filter-prog' value='" . $row['programmeid'] . "'/> " . $row['name'] . "</label>";
                }
            }
        } else {
            $output .= "<label class='filter-label'><input type='checkbox' name='filter-prog[]' data-deptid='" . $row['departmentid'] . "' class='filter-prog' value='" . $row['programmeid'] . "'/> " . $row['name'] . "</label>";
        }
    }
    return $output;
}

function get_allschedule_as_array($tid)
{
    global $dbh;
    $schd = array();
    $query = "select * from tblscheduling where testid = ?";
    $i = 0;
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($tid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $schd[] = $row['schedulingid'];
    }
    return $schd;
}

function get_candidate_id_as_array($schds)
{
    global $dbh;
    $sch = trim(implode(",", $schds), ", ");
    $cid = array();
    $query = "select distinct candidateid from tblcandidatestudent where scheduleid in ($sch)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cid[] = $row['candidateid'];
    }
    return $cid;
}

function get_regnum_as_array($cids)
{
    global $dbh;
    $cds = trim(implode(",", $cids), ", ");
    $rgno = array();
    $query = "select distinct RegNo from tblscheduledcandidate where candidateid in ($cds)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rgno[] = $row['RegNo'];
    }
    return $rgno;
}

function get_programme_id_as_array($regnums, $deptlist = "")
{
    global $dbh;
    $regnos = array();
    while ($rg = each($regnums)) {
        $regnos[] = "'" . $rg[1] . "'";
    }
    $regno = trim(implode(",", $regnos), ", ");
    $pid = array();
    $query = "select distinct programmeadmitted from tblstudents where matricnumber in ($regno)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($deptlist != null && $deptlist != "") {
            $progid = $row['programmeadmitted'];
            $query2 = "select departmentid from tblprogramme where programmeid =?";
            $stmt = $dbh->prepare($query2);
            $stmt->execute(array($progid));
            $numrow = $stmt->rowCount();
            if ($numrow > 0) { //echo $sql2; exit;
                $didrow = $stmt->fetch(PDO::FETCH_ASSOC);
                $did = $didrow['departmentid'];
                if (in_array($did, $deptlist))
                    $pid[] = $row['programmeadmitted'];
            }
        } else
            $pid[] = $row['programmeadmitted'];
    }
    return $pid;
}

function get_department_id_as_array($pids, $faclist = "")
{
    global $dbh;
    $pid = trim(implode(",", $pids), ", ");
    $did = array();
    $query = "select distinct departmentid from tblprogramme where programmeid in ($pid)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($faclist != null && $faclist != "") {
            $progid = $row['departmentid'];
            $query2 = "select facultyid from tbldepartment where departmentid=?";
            $stmt = $dbh->prepare($query2);
            $stmt->execute(array($progid));
            $numrow = $stmt->rowCount();
            if ($numrow > 0) {
                $fidrow = $stmt->fetch(PDO::FETCH_ASSOC);
                $fid = $fidrow['facultyid'];
                if (in_array($fid, $faclist))
                    $did[] = $row['departmentid'];
            }
        } else
            $did[] = $row['departmentid'];
    }
    return $did;
}

function get_faculty_id_as_array($dids)
{
    global $dbh;
    $did = trim(implode(",", $dids), ", ");
    $fid = array();
    $query = "select distinct facultyid from tbldepartment where departmentid in ($did)";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fid[] = $row['facultyid'];
    }
    return $fid;
}

/* * Functions for displaying the presentation* */

function get_presentation($candidateid, $testid, $subjectid)
{
    displayallquestion($candidateid, $testid, $subjectid);
}

function displayallquestion($candidateid, $testid, $subjectid)
{
    //create the questions
    global $dbh;
    $query = "SELECT distinct(questionid) AS questionid,tblpresentation.candidateid AS pcandid,tblpresentation.testid AS ptid,tblpresentation.sectionid AS psid,
		tblquestionbank.title AS qtitle, tbltestsection.title AS ttitle,tbltestsection.instruction from tblpresentation 
		INNER JOIN tbltestsection on tblpresentation.sectionid=tbltestsection.testsectionid
		INNER JOIN tblquestionbank on tblpresentation.questionid=tblquestionbank.questionbankid
		INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid		
		WHERE(tblpresentation.candidateid=? and tblpresentation.testid=?
		and tbltestsubject.subjectid=?)";

    $stmt = $dbh->prepare($query);
    $stmt->execute(array($candidateid, $testid, $subjectid));
    $numrow = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($numrow > 0) {

        $sectiontitle = $row['ttitle'];
        $sectioninstruction = $row['instruction'];
        //create new section
        $curentsectionid = $row['psid'];//mysql_result($resultquestion, 0, 'sectionid');
        opensectiondiv($sectiontitle, $sectioninstruction);
        $counter = 0;
        $i = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sectionid = $row['psid'];
            if (($sectionid != $curentsectionid) && $i > 0) {
                $curentsectionid = $sectionid;
                //close the previous section and create new section
                $sectiontitle = $row['ttitle'];;
                $sectioninstruction = $row['instruction'];
                echo "</div><hr style='border-width:2px; border-style:dotted;' />"; //close the section div;
                opensectiondiv($sectiontitle, $sectioninstruction);
            }
            $questionid = $row['questionid'];
            $questiontitle = $row['qtitle'];// mysql_result($resultquestion, $i, 'title');
            $questiontitle = stripslashes($questiontitle);
            $counter = $i + 1;
            $divid = "questiondiv" . $questionid;
            ?>
            <div class="questionanswerdiv" id="<?php echo $divid ?>">
            <div class="qadiv"style="background-color:#ffffff">
            <div class="questiondiv">
                <?php echo " <b>Question $counter: </b>"; ?><?php echo html_entity_decode($questiontitle, ENT_QUOTES); ?>
            </div>
            <div class="answerdiv">
            <?php
            showquestion($questionid, $testid, $candidateid, $questiontype = "OBJ");
            echo "</div>";
            echo "	</div></div>";
            $i = $i + 1;
        }//endfor
        //closing questionanswerdiv and answerdiv
        $stmt->closeCursor();
    }
}

function showquestion($questionid, $testid, $candidateid, $questiontype = "OBJ")
{
    global $dbh;
    $tblscoreanswerid = 0;
    if ($questiontype == "OBJ") {
        //objectve question
        $query = "SELECT tblpresentation.questionid AS pqid,tblpresentation.answerid AS paid,
		correctness,test FROM tblpresentation
		INNER JOIN tblansweroptions ON tblpresentation.answerid=tblansweroptions.answerid
		where(tblpresentation.questionid=? and 
		tblpresentation.testid=? and tblpresentation.candidateid=?
		)";

        $stmt = $dbh->prepare($query);
        $stmt->execute(array($questionid, $testid, $candidateid));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $numrow = $stmt->rowCount();
        if ($numrow > 0) {
            //get the answer in score if any
            $query2 = "SELECT tblscore.answerid FROM tblscore where(questionid=? and testid=? and candidateid=? )";
            //$resultscore = mysql_query($queryscore);
            $stmt2 = $dbh->prepare($query2);
            $stmt2->execute(array($questionid, $testid, $candidateid));
            $numrow = $stmt2->rowCount();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($numrow > 0) {
                $tblscoreanswerid = $row2['answerid'];
                if ($tblscoreanswerid == "")
                    $tblscoreanswerid = 0;
            }
            $optioncounter = 0;
            echo "<ol class='ansopt'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $answerid = $row['paid'];
                $answertext = $row['test'];;
                $answertext = stripslashes($answertext);
                //$tblscoreanswerid=mysql_result($resultquest,$k,'tblscore.answerid');
                //$answerid=mysql_result($resultquest,$k,'answerid');
                //$answerid=mysql_result($resultquest,$k,'answerid');
                //$optioncounter=integerToRoman($k+1);
                if ($answerid == $tblscoreanswerid) {
                    echo "<li class='answer'>
					<label class='optionlabel'><table class='answertb'><tr><td>" . html_entity_decode($answertext, ENT_QUOTES) . "</td><td><img src='" . siteUrl("assets/img/tickIcon.png") . "' /></td></tr></table></label> </li>";
                } else {
                    echo "<li>  <label class='optionlabel'>" . html_entity_decode($answertext, ENT_QUOTES) . "</label> </li>";
                }
            }//endfor
            echo "</ol>";
            $code = $questionid . "code";
            echo "<input type='hidden' name='$code' id='$code' value='$tblscoreanswerid'>";
            $stmt->closeCursor();
        }
    } else {
        //specify another questiontype
    }
}

function opensectiondiv($sectionname, $instruction)
{
    ?>
    <div class="sectiondiv">
        <div id="sectioninfodiv" style="font-weight:normal;">
    <?php
    if ($sectionname != "") {
        echo "	<div style='text-align:center; font-size:1.2em; text-decoration:underline;'><b>SECTION: </b>" . strtoupper($sectionname) . " </div>";
    }
    if (trim($instruction) != "") {
        echo "<div style='text-align:center; font-size:1.2em;'><b>INSTRUCTION: </b>$instruction </div>";
    }
    echo "</div>";
}

/*                     * **********end************************* */
?>