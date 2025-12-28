<?php

if (!isset($_SESSION))
    session_start();

require_once 'globals.php';
require_once 'security.php';

//returns an array containing testid initiated by a user
function get_super_admin_test_as_array($limit = 100)
{
    global $dbh;
    $tests = array();
    $limit = intval(clean($limit));
    $uid = $_SESSION['MEMBER_USERID'];

    $query = "select testid from tbltestconfig where (initiatedby <> ?) order by dateinitiated desc limit $limit";
    $result = $dbh->prepare($query);
    $result->execute(array($uid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tests[] = $row['testid'];
    }

    return $tests;
}

function get_test_initiated_as_array($limit = -1)
{
    global $dbh;
    $tests = array();
    $limit = intval(clean($limit));
    $uid = $_SESSION['MEMBER_USERID'];
    if ($limit != -1 && $limit)
        $query = "select testid from tbltestconfig where (initiatedby = ?) order by dateinitiated desc limit $limit";
    else
        $query = "select testid from tbltestconfig where (initiatedby= ?) order by dateinitiated desc limit 100";
    $result = $dbh->prepare($query);
    $result->execute(array($uid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tests[] = $row['testid'];
    }
    return $tests;
}

// returns an array containing testid of which the user is a compositor
function get_compositor_test_as_array($limit = -1)
{
    global $dbh;
    $tests = array();
    $limit = intval(clean($limit));
    $uid = $_SESSION['MEMBER_USERID'];
    if ($limit != -1 && $limit)
        $query = "select distinct testid from tbltestcompositor where (userid = ?) order by testid desc limit $limit";
    else
        $query = "select distinct testid from tbltestcompositor where (userid =? ) order by testid desc limit 100";
    $result = $dbh->prepare($query);
    $result->execute(array($uid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tests[] = $row['testid'];
    }

    return $tests;
}

// returns an array containing testid of which the user is a compositor
function get_compositor_subject_as_array($testid, $userid)
{
    global $dbh;
    $subjects = array();
    $query = "select distinct subjectid from tbltestcompositor where (userid = ?) && testid='$testid'";
    $result = $dbh->prepare($query);
    $result->execute(array($userid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $subjects[] = $row['subjectid'];
    }

    return $subjects;
}

//returns an array containing userid which are currently a test compositor in a test
function get_test_compositors_as_array($testid, $sbjid = "")
{
    global $dbh;
    $compositors = array();
    if (trim($sbjid) == "") {
        $query = "select distinct userid from tbltestcompositor where (testid=?)";
        $result = $dbh->prepare($query);
        $result->execute(array($testid));

    } else {
        $query1 = "select distinct userid from tbltestcompositor where (testid=? && subjectid=?)";

        $result = $dbh->prepare($query1);
        $result->execute(array($testid, $sbjid));
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $compositors[] = $row['userid'];
        }
        return $compositors;
    }
}

//returns an array of userid which are currently the invigilators of this test
function get_test_invigilators_as_array($testid, $schdid = "")
{
    global $dbh;
    $invigilators = array();
    if (trim($schdid) == "")
        $query = "select distinct userid from tbltestinvigilator where (testid=?)";
    else
        $query = "select distinct userid from tbltestinvigilator where (testid=? && schedulingid=?)";
//echo $sql; exit;
    $result = $dbh->prepare($query);
    $result->execute(array($testid, $schdid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $invigilators[] = $row['userid'];
    }
    return $invigilators;
}

//returns an array containing testid of which the user is an invigilator
function get_invigilator_test_as_array($limit = 100)
{
    global $dbh;
    $tests = array();
    $limit = intval(clean($limit));
    $uid = $_SESSION['MEMBER_USERID'];
    $query = "select distinct testid from tbltestinvigilator where (userid = ?) order by testid desc limit $limit";
    $result = $dbh->prepare($query);
    $result->execute(array($uid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tests[] = $row['testid'];
    }

    return $tests;
}

function is_test_administrator_of($testid = 0)
{
    global $dbh;
    $testid = clean($testid);
    $uid = $_SESSION['MEMBER_USERID'];
    $query = "select initiatedby from tbltestconfig where (testid= ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($uid == $row['initiatedby'])
        return true;
    else
        return false;
}

function is_test_administrator($tadmin)
{
    global $dbh;
    $tadmin = clean($tadmin);
    $query = "select * from userrole where (userid=? && roleid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($tadmin, '9'));
    if ($result->rowCount())
        return true;
    else
        return false;
}

function is_question_author($qauthor)
{
    global $dbh;
    $qauthor = clean($qauthor);
    $query = "select * from userrole where (userid=? && roleid=(select id from role where (name=?) limit 1 ))";
    $result = $dbh->prepare($query);
    $result->execute(array($qauthor, "Question Author"));

    if ($result->rowCount())
        return true;
    else
        return false;
}

function is_test_compositor_of($testid = 0, $uid = null, $sbjid = null)
{
    global $dbh;
    $testid = clean($testid);
    $uid = ((isset($uid)) ? (clean($uid)) : ($_SESSION['MEMBER_USERID']));
    if (isset($sbjid))
        $query = "select userid from tbltestcompositor where (testid=$testid && userid = $uid && subjectid=$sbjid)";
    else
        $query = "select userid from tbltestcompositor where (testid=$testid && userid = $uid)";

    $result = $dbh->prepare($query);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($uid == $row['userid'])
        return true;
    else
        return false;
}

function get_test_invigilator_passkeys_as_array($testid, $scheduleid, array $testinvigilators)
{
    global $dbh;
    $passkeys = array();
    foreach ($testinvigilators as $testinvigilator) {
        $query = "select passkey from tbltestinvigilator where (userid=? && testid=? && schedulingid=?)";

        $result = $dbh->prepare($query);
        $result->execute(array($testinvigilator, $testid, $scheduleid));


        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $passkeys[] = $row['passkey'];
        }
    }
    return $passkeys;
}

function get_test_passkey($testid)
{
    global $dbh;
    $passkey = "?????";

    $query = "select passkey from tbltestconfig where (testid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    if ($result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $passkey = $row['passkey'];
    }
    return $passkey;
}

function test_taken($testid)
{
    global $dbh;
    $query = "select * from tblscore where (testid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    if ($result->rowCount()) {
        return true;
    }
    return false;
}

function is_disabled($pno)
{
    global $dbh;
    $query = "select * from user where (staffno = ? && enabled = ?) limit 1";
    $result = $dbh->prepare($query);
    $result->execute(array($pno, '1'));
    if ($result->rowCount() > 0)
        return false;
    else
        return true;
}

function get_test_admins_as_array()
{
    global $dbh;
    $tadmin = array();
    $query = "select distinct user.id from user inner join userrole on (user.id = userrole.userid) inner join role on (userrole.roleid = role.id) where (role.name=?) order by userrole.id desc";
    $result = $dbh->prepare($query);
    $result->execute(array('Admin'));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tadmin[] = $row['id'];
    }
    return $tadmin;
}

function get_pc_registrars_as_array()
{
    global $dbh;
    $pcreg = array();
    $query = "select distinct user.id from user inner join userrole on (user.id = userrole.userid) inner join role on (userrole.roleid = role.id) where (role.name=?) order by userrole.id desc";
    $result = $dbh->prepare($query);
    $result->execute(array("PC Registrar"));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $pcreg[] = $row['id'];
    }
    return $pcreg;
}

function get_test_administrators_as_array()
{
    global $dbh;
    $tadmin = array();
    $query = "select distinct user.id from user inner join userrole on (user.id = userrole.userid) inner join role on (userrole.roleid = role.id) where (role.name=?) order by userrole.id desc";
    $result = $dbh->prepare($query);
    $result->execute(array("Test Administrator"));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tadmin[] = $row['id'];
    }
    return $tadmin;
}

function get_question_authors_as_array()
{
    global $dbh;
    $qauthor = array();
    $query = "select distinct user.id from user inner join userrole on (user.id = userrole.userid) inner join role on (userrole.roleid = role.id) where (role.name=?) order by userrole.id desc";
    $result = $dbh->prepare($query);
    $result->execute(array("Question Author"));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $qauthor[] = $row['id'];
    }
    return $qauthor;
}

function is_pc_registrar($uid)
{
    global $dbh;
    $query = "select user.id from user inner join userrole on (user.id = userrole.userid) inner join role on (userrole.roleid = role.id) where (role.name=? && user.id=?)";
    $result = $dbh->prepare($query);
    $result->execute(array("PC Registrar", $uid));
    if ($result->rowCount() > 0)
        return true;
    else
        return false;
}

function is_admin($uid)
{
    global $dbh;
    $query = "select user.id from user inner join userrole on (user.id = userrole.userid) inner join role on (userrole.roleid = role.id) where (role.name=? && user.id=?)";
    $result = $dbh->prepare($query);
    $result->execute(array("Admin", $uid));
    if ($result->rowCount() > 0)
        return true;
    else
        return false;
}

function is_test_invigilator_of($schdid = 0, $uid = null)
{
    global $dbh;
    $schdid = clean($schdid);
    $uid = ((isset($uid)) ? (clean($uid)) : ($_SESSION['MEMBER_USERID']));
    $query = "select userid from tbltestinvigilator where (schedulingid=? && userid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($schdid, $uid));

    if ($result->rowCount() > 0)
        return true;
    else
        return false;
}

function get_staff_pno($userid)
{
    global $dbh;
    $query = "select staffno from user where (id=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($userid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['staffno'];
}

function get_staff_userid($pno)
{
    global $dbh;
    $pno = str_replace(".", "", $pno);
    $pno = trim($pno);
//return $pno;
    $query = "select id from user where (staffno=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($pno));
    if ($result->rowCount() == 0)
        return 0;
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['id'];
}

function get_staff_biodata($pno)
{
    global $dbh;
    $pno = str_replace(".", "", $pno);
    $staff = array();
    $query = "select firstname, surname, othernames, tblemployee.departmentid, tbldepartment.name as dname, tblemployee.gender from tblemployee inner join tbldepartment on (tblemployee.departmentid = tbldepartment.departmentid) where( personnelno=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($pno));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $staff['firstname'] = $row['firstname'];
    $staff['surname'] = $row['surname'];
    $staff['othernames'] = $row['othernames'];
    $staff['departmentid'] = $row['departmentid'];
    $staff['departmentname'] = $row['dname'];
    $staff['gender'] = $row['gender'];
    return $staff;
}

function get_test_id_from_schedule($schid)
{
    global $dbh;
    $schid = clean($schid);
    $query = "select testid from tblscheduling where (schedulingid = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($schid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['testid'];
}

//get configuration parameters of a given test in array
function get_test_config_param_as_array($testid = 0)
{
    global $dbh;
    $testid = clean($testid);
    $param = array();
    $query = "select * from tbltestconfig inner join tbltestcode on (tbltestconfig.testcodeid = tbltestcode.testcodeid) inner join tbltesttype on (tbltestconfig.testtypeid = tbltesttype.testtypeid) where (testid=?)";

    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $testcategory = $row['testcategory'];
    $testtypeid = $row['testtypeid'];
    $testtypename = $row['testtypename'];
    $testcodeid = $row['testcodeid'];
    $testname = $row['testname'];
    $totalmark = $row['totalmark'];
    $session = $row['session'];
    $semester = $row['semester'];
    $initiatedby = $row['initiatedby'];
    $dateinitiated = $row['dateinitiated'];
    $status = $row['status'];
    $versions = $row['versions'];
    $activeversion = $row['activeversion'];
    $duration = $row['duration'];
    $optionadmin = $row['optionadministration'];
    $questionadmin = $row['questionadministration'];
    $startmode = $row['startingmode'];
    $displaymode = $row['displaymode'];
    $endorsement = $row['endorsement'];
    $time_padding = $row['timepadding'];
    $allow_calc = $row['allow_calc'];
    $passkey = $row['passkey'];

    $param['testid'] = $testid;
    $param['testcategory'] = $testcategory;
    $param['testtypeid'] = $testtypeid;
    $param['testtypename'] = $testtypename;
    $param['totalmark'] = $totalmark;
    $param['session'] = $session;
    $param['semester'] = $semester;
    $param['initiatedby'] = $initiatedby;
    $param['dateinitiated'] = $dateinitiated;
    $param['status'] = $status;
    $param['versions'] = $versions;
    $param['activeversion'] = $activeversion;
    $param['duration'] = $duration;
    $param['optionadmin'] = $optionadmin;
    $param['questionadmin'] = $questionadmin;
    $param['startmode'] = $startmode;
    $param['displaymode'] = $displaymode;
    $param['testcodeid'] = $testcodeid;
    $param['testname'] = $testname;
    $param['endorsement'] = $endorsement;
    $param['time_padding'] = $time_padding;
    $param['allow_calc'] = $allow_calc;
    $param['passkey'] = $passkey;
    return $param;
}

function get_schedule_config_param_as_array($schid = 0)
{
    global $dbh;
    $schid = clean($schid);
    $param = array();
    $query = "select * from tblscheduling inner join tblvenue on (tblvenue.venueid = tblscheduling.venueid) where (tblscheduling.schedulingid=?)";

    $result = $dbh->prepare($query);
    $result->execute(array($schid));
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $schedulingid = $row['schedulingid'];
    $testid = $row['testid'];
    $venueid = $row['venueid'];
    $venuename = $row['name'];
    $batchcount = $row['maximumBatch'];
    $noperbatch = $row['noPerschedule'];
    $date = $row['date'];
    $dailystarttime = $row['dailystarttime'];
    $dailyendtime = $row['dailyendtime'];
    $centreid = get_centre_id($venueid);
    $centrename = get_centre_name($centreid);

    $param['testid'] = $testid;
    $param['schedulingid'] = $schedulingid;
    $param['venueid'] = $venueid;
    $param['venuename'] = $venuename;
    $param['maximumbatch'] = $batchcount;
    $param['noperbatch'] = $noperbatch;
    $param['dailystarttime'] = $dailystarttime;
    $param['dailyendtime'] = $dailyendtime;
    $param['date'] = $date;
    $param['centerid'] = $centreid;
    $param['centername'] = $centrename;
    return $param;
}

function get_candidate_schedule($candidateid, $testid)
{
    global $dbh;
    $query = "select scheduleid from tblcandidatestudent inner join tblscheduling on (tblscheduling.schedulingid = tblcandidatestudent.scheduleid) where (tblscheduling.testid=? && tblcandidatestudent.candidateid = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($testid, $candidateid));

    if ($result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['scheduleid'];
    } else
        return null;
}

function get_schedule_freeslot($schid)
{
    $occupied = get_candidate_scheduled_count($schid);
    $capacity = get_schedule_capacity($schid);

    if ($capacity < 0)
        return -1;
    else
        if ($occupied > $capacity)
            return 0;
        else
            return ($capacity - $occupied);
}

function candid_exist($matric, $stdtype = "REGULAR")
{
   return true;
    if ($stdtype == "REGULAR")
        $query = "select * from tblstudents where (UPPER(matricnumber) =?)";
    else
        if ($stdtype == "Post-UTME")
            $query = "select * from tbljamb where (UPPER(RegNo) = ?)";
        else
            if ($stdtype == "SBRS")
                $query = "select * from tblsbrsstudents where (UPPER(sbrsno)=? || UPPER(oldsbrsno)=?)";
            else
                if ($stdtype == "SBRS-NEW")
                    $query = "select * from tbljamb where (UPPER(RegNo) = ?)";

    $result = $dbh->prepare($query);
    $result->execute(array(strtoupper(trim($matric))));

    if ($result->rowCount() > 0) {
        return true;
    }
    return false;
}

function is_scheduled($testid, $matric, $stdtype = "REGULAR")
{
    global $dbh;
    //echo count($schd);
    $schds = get_test_schedule_as_array($testid);
    $schds[] = 0;
    $schds_str = trim(implode(",", $schds), " ,");
    //var_dump($schds_str); exit;
    $cid = get_candidate_id($matric, (($stdtype == "REGULAR") ? (3) : (($stdtype == "Post-UTME") ? (1) : (($stdtype == "SBRS") ? (2) : (4)))));
    $query = "select * from tblcandidatestudent where scheduleid in ($schds_str) && candidateid=$cid ";
    $result = $dbh->prepare($query);
    $result->execute();
    if ($result->rowCount() > 0)
        return true;
    else
        return false;
}

function is_scheduled_on_subject($schd, $cid, $subs)
{
    global $dbh;
    $str1 = '';
    $schds = implode(",", $schd);
    $schds = trim($schds, ",");
    //echo "en ".$schds."<br />";
    $cont = 0;
    $cont2 = count($subs);

    foreach ($subs as $new) {
        $str1 = $str1 . $new['subjectid'] . ",";
    }


    $str1 = rtrim($str1, ", ");
    $query = "select * from tblcandidatestudent where scheduleid in($schds) && subjectid in ($str1) && candidateid=$cid ";

    $result = $dbh->prepare($query);
    $result->execute();
    $new = $result->rowCount();
    if ($new > 0)
        return true;
    else
        return false;
}

//gets or generates the candidate id given the matric number and student type (1=Ppst-UTME, 2=SBRS, 3= REGULAR, 4=SBRS-NEW)
function get_candidate_id($matric, $candtype = 1 /* 1=Post-UTME, 2=SBRS, 3= REGULAR, 4=SBRS-NEW */){
//    var_dump($candtype);exit;
    global $dbh;
    if ($candtype == 1)
        $sql = "select candidateid from tblscheduledcandidate where candidatetype='1' and UPPER(RegNo) = '" . trim(strtoupper($matric)) . "'";
    elseif ($candtype == 2)
        $sql = "select candidateid from tblscheduledcandidate where candidatetype='2' and UPPER(RegNo) = '" . trim(strtoupper($matric)) . "'";
    elseif ($candtype == 3)
        $sql = "select candidateid from tblscheduledcandidate where candidatetype='3' and UPPER(RegNo) = '" . trim(strtoupper($matric)) . "'";
    elseif ($candtype == 4)
        $sql = "select candidateid from tblscheduledcandidate where candidatetype='4' and UPPER(RegNo) = '" . trim(strtoupper($matric)) . "'";

    /*else{

    }*/
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $count = $stmt->rowCount();
    //echo $count;exit;
    if ($count) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cid = $row['candidateid'];
        //echo $cid;exit;
        return $cid;
    } else {
        //echo $candtype;exit;
        if ($candtype == 1)
            $sql1 = "insert into tblscheduledcandidate (candidatetype, RegNo) values (1, UPPER('$matric'))";
        elseif ($candtype == 2)
            $sql1 = "insert into tblscheduledcandidate (candidatetype, RegNo) values (2, UPPER('$matric'))";
        elseif ($candtype == 3)
            $sql1 = "insert into tblscheduledcandidate (candidatetype, RegNo) values (3, UPPER('$matric'))";
        elseif ($candtype == 4)
            $sql1 = "insert into tblscheduledcandidate (candidatetype, RegNo) values (4, UPPER('$matric'))";
        /* else {

        }*/
        $stmt1 = $dbh->prepare($sql1);
        $exec = $stmt1->execute();

    }

    if ($exec) {
        return get_candidate_id($matric, $candtype);
        //return "dfghjk";
    } else {
        return null;
    }
}


function test_date_exceeded($testid = 0, $minimal = true)
{
    global $dbh;
    $testid = clean($testid);
    if ($minimal == false)
        $query = "select MAX(date) from tblexamsdate where testid='$testid' && (date < CURDATE()  || date = CURDATE()) ";
    else
        $query = "select * from tblexamsdate where (testid=? && (date < CURDATE()  || date = CURDATE())) ";

    $result = $dbh->prepare($query);
    $result->execute(array($testid));

    if ($result->rowCount() > 0)
        return true;
    return false;
}

function get_schedule_datetime($schedule, $boundary = "start")
{
    global $dbh;
    $scheduledatetime = null;
    if ($boundary == 'start')
        $query = "select date, dailystarttime from tblscheduling where (schedulingid = ?)";
    else
        $query = "select date, dailyendtime from tblscheduling where (schedulingid = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($schedule));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $scheduledate = $row['date'];
    if ($boundary == "start")
        $scheduletime = $row['dailystarttime'];
    else
        $scheduletime = $row['dailyendtime'];
    $scheduledatetime = new DateTime($scheduledate . " " . $scheduletime);
    return $scheduledatetime;
}

function get_test_datetime($testid, $boundary = 'lowest')
{
    global $dbh;
    $testdatetime = null;
    if ($boundary == 'lowest')
        $query = "select date as dt, dailystarttime from tblscheduling where (testid = ?) order by date asc, dailystarttime asc";
    else
        $query = "select date as dt, dailystarttime from tblscheduling where (testid = ?) order by date desc, dailystarttime desc";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    if ($result->rowCount() == 0)
        return null;
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $testdate = $row['dt'];
    $testtime = $row['dailystarttime'];
    $testdatetime = new DateTime($testdate . " " . $testtime);
    return $testdatetime;
}

function test_date_registered($testid = 0, $dt = "0000-00-00")
{
    global $dbh;
    $testid = clean($testid);
    $dt = clean($dt);
    $query = "select * from tblexamsdate where (testid=? && date = ?) ";
    $result = $dbh->prepare($query);
    $result->execute(array($testid, $dt));
    if ($result->rowCount() > 0)
        return true;
    return false;
}

function get_subject_code($sbjid)
{
    global $dbh;
    $query = "select subjectcode from tblsubject where (subjectid = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($sbjid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return strtoupper($row['subjectcode']);
}

function get_subject_name($sbjid)
{
    global $dbh;
    $query = "select subjectname from tblsubject where (subjectid = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($sbjid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['subjectname'];
}

function date_exceeded($tid, $intaval = -300, $boundary = 'lowest')
{
    global $dbh;//boundary means the lowest schedule date. Intaval means the minimum interval before lowest boundary
    $testdatetime = get_test_datetime($tid, $boundary);
    $now = new DateTime();

    if ($testdatetime != null && ($now->getTimestamp() - $testdatetime->getTimestamp() > $intaval))
        return true;
    return false;
}

function get_test_version_used_as_array($sectionid, $qid, $exclude = 0)
{
    global $dbh;
    $v = array();
    $query = "select version from tbltestquestion where (testsectionid=? && version <> ? && questionbankid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($sectionid, $exclude, $qid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $v[] = $row['version'];
    }
    return $v;
}

function get_num_diff_lvl($sectionid, $lvl = "easy")
{
    global $dbh;
    if ($lvl == "easy")
        $query = "select numofeasy as difflvl from tbltestsection where (testsectionid=$sectionid)";
    else
        if ($lvl == "moderate")
            $query = "select numofmoderate as difflvl from tbltestsection where (testsectionid=$sectionid)";
        else
            if ($lvl == "difficult")
                $query = "select numofdifficult as difflvl from tbltestsection where (testsectionid=$sectionid)";

    $result = $dbh->prepare($query);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['difflvl'];
}

function get_faculty_id($deptid)
{
    global $dbh;
    $query = "select facultyid from tbldepartment where (departmentid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($deptid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['facultyid'];
}

function get_num_diff_lvl_count($sectionid, $version = 1, $lvl = "easy")
{
    global $dbh;
    if ($lvl == "easy")
        $query = "select count(tbltestquestion.questionbankid) as difflvl from tbltestquestion inner join tblquestionbank on (tblquestionbank.questionbankid= tbltestquestion.questionbankid) where tbltestquestion.version=? && tbltestquestion.testsectionid=? && tblquestionbank.difficultylevel='simple'";
    else
        if ($lvl == "moderate")
            $query = "select count(tbltestquestion.questionbankid) as difflvl from tbltestquestion inner join tblquestionbank on (tblquestionbank.questionbankid= tbltestquestion.questionbankid) where tbltestquestion.version=? && tbltestquestion.testsectionid=? && tblquestionbank.difficultylevel='difficult'";
        else
            if ($lvl == "difficult")
                $query = "select count(tbltestquestion.questionbankid) as difflvl from tbltestquestion inner join tblquestionbank on (tblquestionbank.questionbankid= tbltestquestion.questionbankid) where (tbltestquestion.version=? && tbltestquestion.testsectionid=? && tblquestionbank.difficultylevel='moredifficult')";

    $result = $dbh->prepare($query);
    $result->execute(array($version, $sectionid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['difflvl'];
}

function get_topics_as_options($subj, $topic = "", $general = true)
{
    global $dbh;
    $query = "select * from tbltopics where (subjectid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($subj));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if (strtoupper($row['topicname']) == "GENERAL" && $general == false)
            continue;
        echo "<option value='" . $row['topicid'] . "' " . (($row['topicid'] == $topic) ? ("selected='selected'") : ("")) . ">" . $row['topicname'] . "</option>";
    }
}

function get_test_sections_as_array($testid, $subjectid = "all")
{
    global $dbh;
    $sections = array();
    if ($subjectid == 'all') {
        $query = "select testsectionid from tbltestsubject inner join tbltestsection on (tbltestsubject.testsubjectid = tbltestsection.testsubjectid) where (tbltestsubject.testid=?)";
    } else {
        $query = "select testsectionid from tbltestsubject inner join tbltestsection on (tbltestsubject.testsubjectid = tbltestsection.testsubjectid) where(tbltestsubject.testid=? && tbltestsubject.subjectid=?)";
    }

    $result = $dbh->prepare($query);
    $result->execute(array($testid, $subjectid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $sections[] = $row['testsectionid'];
    }
    return $sections;
}

function get_test_dates_as_array($testid = 0)
{
    global $dbh;
    $testid = clean($testid);
    $tdt = array();
    $query = "select * from tblexamsdate where (testid=?) order by date asc";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tdt[] = $row['date'];
    }
    return $tdt;
}

function candidate_schedule_count($testid = 0, $dt = "0000-00-00")
{
    global $dbh;
    $testid = clean($testid);
    $dt = clean($dt);
    $count = 0;
    $query = "select distinct candidateid from tblcandidatestudent where (scheduleid in (select schedulingid from tblscheduling where(testid=? && date=?)))";
    $result = $dbh->prepare($query);
    $result->execute(array($testid, $dt));
    $count = $result->rowCount();
    return $count;
}

function get_schedule_capacity($schdid)
{
    $schdid = clean($schdid);
    $schd_config = get_schedule_config_param_as_array($schdid);
    if ($schd_config['maximumbatch'] == -1)
        return -1;
    else {
        return ($schd_config['maximumbatch'] * $schd_config['noperbatch']);
    }
}

function get_centre_id($venueid = 0)
{
    global $dbh;
    $venueid = clean($venueid);
    $venueid = intval($venueid);
    $query = "select centreid from tblvenue where (venueid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($venueid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['centreid'];
}

function get_centre_name($cid = 0)
{
    global $dbh;
    $cid = clean($cid);
    $cid = intval($cid);
    $query = "select name from tblcentres where (centreid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($cid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['name'];
}

function get_center_as_options($cid = "")
{
    global $dbh;
    $output = "";
    $cid = clean($cid);
    $query = "select * from tblcentres";
    $result = $dbh->prepare($query);
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $ct = $row['name'];
        $ctid = $row['centreid'];
        if ($ctid == $cid)
            $output .= "<option value='$ctid' selected >$ct</option>";
        else
            $output .= "<option value='$ctid'>$ct</option>";
    }
    return $output;
}

function get_venue_as_options($param = null)
{
    global $dbh;
    $output = "";
    $vid = "";
    if (!isset($param))
        $query = "select * from tblvenue";
    else {
        $query = "select * from tblvenue where (centreid = ?)";
        if (isset($param['venueid']))
            $vid = clean($param["venueid"]);
    }

    $result = $dbh->prepare($query);
    $result->execute(array(clean($param['centerid'])));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $vn = $row['name'];
        $cvid = $row['venueid'];
        if ($cvid == $vid)
            $output .= "<option value='$cvid' selected>$vn</option>";
        else
            $output .= "<option value='$cvid'>$vn</option>";
    }
    return $output;
}

function get_venue_capacity($venueid)
{
    global $dbh;
    $venueid = clean($venueid);
    $query = "select capacity from tblvenue where (venueid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($venueid));

    if ($result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['capacity'];
    }
    return 0;
}

function get_schedule_ids_as_array($tid, $dt = "")
{
    global $dbh;
    $tid = clean($tid);
    $s = array();
    if ($dt == "")
        $query = "select schedulingid from tblscheduling where (testid = ?) order by date  asc, dailystarttime asc"; // echo $sql;
    else $query = "select schedulingid from tblscheduling where (testid = ?) order by date asc, dailystarttime asc"; // echo $sql;

    $result = $dbh->prepare($query);
    $result->execute(array($tid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

        $s[$i] = $row['schedulingid'];
        //echo"enesi ".$s[$i]."<br />";
        $i++;
    }
    //echo "umar ".implode($s);
    return $s;
}

function get_schedule_id($tid, $venueid)
{
    global $dbh;
    $s = array();
    $query = "select schedulingid from tblscheduling where (testid = ? && venueid=?)"; // echo $sql;
    $result = $dbh->prepare($query);
    $result->execute(array($tid, $venueid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $s = $row['schedulingid'];
    return $s;
}

function get_candidate_scheduled_count($schid)
{
    global $dbh;
    $query = "select distinct candidateid from tblcandidatestudent where (scheduleid =?)";
    $result = $dbh->prepare($query);
    $result->execute(array($schid));
    return $result->rowCount();
}

function get_candidate_scheduled_as_array($testid)
{
    global $dbh;
    $candidates = array();
    $query = "select distinct candidateid from tblcandidatestudent where (scheduleid in (select schedulingid from tblscheduling where (testid = ?)))";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $candidates[] = $row['candidateid'];
    }
    return $candidates;
}

function get_venue_id($schd)
{
    global $dbh;
    $query = "select venueid from tblscheduling where (schedulingid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($schd));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return $row['venueid'];
}

function get_venue_name($venueid)
{
    global $dbh;
    $query = "select name, location from tblvenue where (venueid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($venueid));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    return ($row['name'] . " (" . $row['location'] . ")");
}

function get_department_as_option($facid = null)
{
    global $dbh;
    if (isset($facid))
        $query = "select * from tbldepartment where (facultyid=?)'";
    else
        $query = "select * from tbldepartment";
    $result = "";
    $result = $dbh->prepare($query);
    $result->execute(array($facid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $deptid = $row['departmentid'];
        $deptname = $row['name'];
        $result .= "<option value='$deptid'>$deptname</option>";
    }
    return $result;
}

function get_programme_as_option($deptid = null)
{
    global $dbh;
    if (isset($deptid))
        $query = "select * from tblprogramme where (departmentid=?)";
    else
        $query = "select * from tblprogramme";
    $result = "";

    $result = $dbh->prepare($query);
    $result->execute(array($deptid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $progid = $row['programmeid'];
        $progname = $row['name'];
        $result .= "<option value='$progid'>$progname</option>";
    }
    return $result;
}

function get_lga_as_option($stateid = null)
{
    global $dbh;
    if (isset($stateid))
        $query = "select * from tbllga where (stateid=?)";
    else
        $query = "select * from tbllga";
    $result = "";

    $result = $dbh->prepare($query);
    $result->execute(array($stateid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $lgaid = $row['lgaid'];
        $lganame = $row['lganame'];
        $result .= "<option value='$lgaid'>$lganame</option>";
    }
    return $result;
}

function get_faculty_as_option()
{
    global $dbh;
    $query = "select * from tblfaculty";
    $result = "";
    $results = $dbh->prepare($query);
    $results->execute();
    $i = 0;
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $facid = $row['facultyid'];
        $facname = $row['name'];
        $result .= "<option value='$facid'>$facname</option>";
    }
    return $result;
}

function get_state_as_option()
{
    global $dbh;
    $query = "select * from tblstate";
    $result = "";
    $results = $dbh->prepare($query);
    $results->execute();
    $i = 0;
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $stateid = $row['stateid'];
        $statename = $row['statename'];
        $result .= "<option value='$stateid'>$statename</option>";
    }
    return $result;
}

function intelligentStr($str, $len = 30)
{
    if (strlen(trim($str)) <= $len)
        return ("<span>" . trim($str) . "</span>");
    else
        return "<span title='" . addslashes($str) . "'>" . trim(substr($str, 0, ($len - 3)), " .") . "...</span>";
}

function is_registered_subject($tid, $sbj_id)
{
    global $dbh;
    $query = "select * from tbltestsubject where (subjectid= ? && testid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($sbj_id, $tid));
    if ($result->rowCount() > 0)
        return true;
    return false;
}

function get_test_subjects_as_array($testid)
{
    global $dbh;
    $result = array();
    $query = "select subjectid from tbltestsubject where (testid= ?)";
    $result1 = $dbh->prepare($query);
    $result1->execute(array($testid));
    while ($row = $result1->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row['subjectid'];
    }
    return $result;
}

function get_test_schedule_as_array($tid)
{
    global $dbh;
    $schedules = array();
    $tid = clean($tid);
    $query = "select schedulingid from tblscheduling where (testid = $tid)";
    $result = $dbh->prepare($query);
    $result->execute();
    //$i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $schedules[] = $row['schedulingid'];
       // $i++;
    }//var_dump($schedules); exit;
    return $schedules;
}

function get_previewer_subject_as_array($testid, $userid)
{
    global $dbh;
    $subjects = array();
    $query = "select distinct subjectid from tblquestionpreviewer where (userid = ? && testid=?)";
    $result = $dbh->prepare($query);
    $result->execute(array($userid, $testid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $subjects[] = $row['subjectid'];
    }

    return $subjects;
}

function get_question_previewer_as_array($testid, $sbjid = "")
{
    global $dbh;
    $previewers = array();
    if (trim($sbjid) == "")
        $query = "select distinct userid from tblquestionpreviewer where testid=?";
    else
        $query = "select distinct userid from tblquestionpreviewer where (testid=? && subjectid=?)";

    $result = $dbh->prepare($query);
    $result->execute(array($testid, $sbjid));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $previewers[] = $row['userid'];
    }
    return $previewers;
}

function is_question_viewer_of($testid = 0, $uid = null, $sbjid = null)
{
    global $dbh;
    $testid = clean($testid);
    $uid = ((isset($uid)) ? (clean($uid)) : ($_SESSION['MEMBER_USERID']));
    if (isset($sbjid))
        $query = "select userid from tblquestionpreviewer where (testid=? && userid = ? && subjectid=?)";
    else
        $query = "select userid from tblquestionpreviewer where (testid=? && userid = ?)";

    $result = $dbh->prepare($query);
    $result->execute(array($testid, $uid, $sbjid));
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($uid == $row['userid'])
        return true;
    else
        return false;
}

// there is a mistake here i suppose to name the function as get_test_versions
function get_test_versions_as_array($testid)
{
    global $dbh;
    $query = "select versions from tbltestconfig where (testid = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($testid));
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $versions = $row['versions'];
    return $versions;
}

function get_previewer_test_as_array($limit = -1)
{
    global $dbh;
    $tests = array();
    $limit = intval(clean($limit));
    $uid = $_SESSION['MEMBER_USERID'];
    if ($limit != -1 && $limit)
        $query = "select distinct testid from tblquestionpreviewer where (userid = ?) order by testid desc limit $limit";
    else
        $query = "select distinct testid from tblquestionpreviewer where (userid = ?) order by testid desc limit 100";
    $result = $dbh->prepare($query);
    $result->execute(array($uid));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $tests[] = $row['testid'];
    }

    return $tests;
}

function get_test_question_count($testid, $test_subject, $version)
{
    global $dbh;
    $query = "select * from tbltestquestion inner join tbltestsection on (tbltestsection.testsectionid = tbltestquestion.testsectionid)
        inner join tbltestsubject on (tbltestsubject.testsubjectid = tbltestsection.testsubjectid) 
        where (tbltestsubject.testid = ? && tbltestsubject.subjectid = ? && tbltestquestion.version=?)";

    $result = $dbh->prepare($query);
    $result->execute(array($testid, $test_subject, $version));
    return $result->rowCount();
}

function get_faculty_as_array()
{
    global $dbh;
    $query = "select * from tblfaculty";

    $result1 = array();
    $result = $dbh->prepare($query);
    $result->execute();
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $facid = $row['facultyid'];
        $facname = $row['name'];
        $result1[] = array("facultyid" => $facid, "facultyname" => $facname);
    }

    return $result1;
}

function get_mapped_faculty_as_array($schd)
{
    global $dbh;

    $query = "select facultyid from tblfaculty_schedule_mapping where (schedulingid=$schd)";
    // echo $query;exit;
    $result1 = array();
    $result = $dbh->prepare($query);
    $result->execute();
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $result1[] = $row['facultyid'];
    }

    return $result1;
}

function get_schedules_mapped_to_faculty_as_array($facid, $testid)
{
    global $dbh;
    $query = "select schedulingid from tblfaculty_schedule_mapping where (facultyid=?  && schedulingid in (select schedulingid from tblscheduling where (testid = ?)))";
    $result = array();
    $result = $dbh->prepare($query);
    $result->execute(array($facid, $testid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row['schedulingid'];
    }
    return $result;
}

function get_jamb_subject_combination($RegNumb)
{
    global $dbh;
    $subjects = array();
    $query = "select * from tbljamb where (RegNo = ?) ";
    $result = $dbh->prepare($query);
    $result->execute(array($RegNumb));
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $sbjcode = array();
    $sbjcode[] = $row['Subj2'];
    $sbjcode[] = $row['Subj3'];
    $sbjcode[] = $row['Subj4'];
    $sbjcode[] = 'ENG';
    foreach ($sbjcode as $sbjcd) {
        $querysbjid = "select subjectid from tblsubject where (subjectcode=?)";
        $result = $dbh->prepare($querysbjid);
        $result->execute(array($sbjcd));

        $sbjidrow = $result->fetch(PDO::FETCH_ASSOC);
        $sbjid = $sbjidrow;
        $subjects[] = $sbjid;
    }
    return $subjects;
}

function get_jamb_candidate_details($regNo)
{
    global $dbh;
    $param = array();
    $query = "select * from tbljamb where (RegNo = ?)";
    $result = $dbh->prepare($query);
    $result->execute(array($regNo));
    if ($result->rowCount() == 0) {
        return $param;
    }
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $candname = $row['CandName'];
    $stateoforigin = $row['StateOfOrigin'];
    $lga = $row["LGA"];
    $sex = $row["Sex"];
    $age = $row["Age"];
    $engsore = $row["EngScore"];
    $subjcode2 = $row["Subj2"];
    $subjscore2 = $row["Subj2Score"];
    $subjcode3 = $row["Subj3"];
    $subjscore3 = $row["Subj3Score"];
    $subjcode4 = $row["Subj4"];
    $subjscore4 = $row["Subj4Score"];
    $totalscore = $row["TotalScore"];
    $faculty = $row["Faculty"];
    $course = $row["Course"];

    $param['candname'] = $candname;
    $param['regno'] = $regNo;
    $param['course'] = $course;
    $param['faculty'] = $faculty;
    $param['stateoforigin'] = $stateoforigin;
    $param['lga'] = $lga;
    $param['sex'] = $sex;
    $param['age'] = $age;
    $param['subjcode2'] = $subjcode2;
    $param['subjcode3'] = $subjcode3;
    $param['subjcode4'] = $subjcode4;
    $param['engscore'] = $engsore;
    $param['subjscore2'] = $subjscore2;
    $param['subjscore3'] = $subjscore3;
    $param['subjscore4'] = $subjscore4;
    $param['totalscore'] = $totalscore;
    return $param;
}

function get_jamb_candidate_schedule_details($regNo, $testid)
{
    global $dbh; //date venue starttime batch
    $param = array();
    $candidateid = get_candidate_id($regNo, 1);
    if (!is_scheduled($testid, $regNo, 'Post-UTME')) {
        return $param;
    }
    //get the timepadding and duration
    $test_config = get_test_config_param_as_array($testid);
    $param['time_padding'] = $test_config['time_padding'];
    $param['duration'] = $test_config['duration'];

    //get the venue center date and dailystarttime
    $candidateschedule = get_candidate_schedule($candidateid, $testid);
    $schedule_config = get_schedule_config_param_as_array($candidateschedule);

    $param['venuename'] = $schedule_config['venuename'];
    $param['venueid'] = $schedule_config['venueid'];
    $param['centername'] = $schedule_config['centername'];
    $param['centerid'] = $schedule_config['centerid'];
    $param['date'] = $schedule_config['date'];
    $param['dailystarttime'] = $schedule_config['dailystarttime'];
    $param['noperbatch'] = $schedule_config['noperbatch'];
    $param['totaldur'] = $param['duration'] + $param['time_padding'];
    $prequery = "select min(add_index) as ab from tblcandidatestudent where (candidateid=? and scheduleid=?) limit 1";
    $result = $dbh->prepare($prequery);
    $result->execute(array($candidateid, $candidateschedule));

    $prerow = $result->fetch(PDO::FETCH_ASSOC);
    $adind = $prerow['ab'];

    $query = "select distinct add_index from tblcandidatestudent where (scheduleid=? && candidateid <> ? && add_index < ?) group by candidateid order by add_index";
    $result = $dbh->prepare($prequery);
    $result->execute(array($candidateschedule, $candidateid, $adind));
    $numrows = $result->rowCount();

    $schd_count = $numrows;

    $batch = floor($schd_count / $param['noperbatch']) + 1;
    $param['batch'] = $batch;
    $testdateobj = new DateTime($param['date'] . " " . $param['dailystarttime']);
    $stt = $param['totaldur'] * ($batch - 1);
    $testdateobj->add(new DateInterval("PT" . $stt . "M"));
    //$startdate = $testdateobj->format("l, jS F, Y");
    //$starttime = $testdateobj->format("g:i a");
    $param['candidatestartdatetime'] = $testdateobj;
    return $param;
}


function embed_user_pic($regno)
{

    $finalURL = get_current_photo($regno);

    echo $finalURL;
}


function get_current_photo($regno)
{
    $regno = trim(strtolower($regno));
    $finalURL = '';
    $r_v = '';

    $s = '../picts/';

    $r_v = $s . $regno . '.jpeg';
    $r_v1 = $s . $regno . '.jpg';
    $r_v2 = $s . $regno . '.png';
    $r_v3 = $s . $regno . '.gif';

    if (file_exists($r_v)) {
        $finalURL = "<img src='" . $r_v . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    } else if (file_exists($r_v1)) {
        $finalURL = "<img src='" . $r_v1 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    } else if (file_exists($r_v2)) {
        $finalURL = "<img src='" . $r_v2 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    } else if (file_exists($r_v3)) {
        $finalURL = "<img src='" . $r_v3 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    }

    if ($finalURL == '') {
        $regno = trim(strtoupper($regno));
        $r_v = $s . $regno . '.jpeg';
        $r_v1 = $s . $regno . '.jpg';
        $r_v2 = $s . $regno . '.png';
        $r_v3 = $s . $regno . '.gif';

        if (file_exists($r_v)) {
            $finalURL = "<img src='" . $r_v . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
        } else if (file_exists($r_v1)) {
            $finalURL = "<img src='" . $r_v1 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
        } else if (file_exists($r_v2)) {
            $finalURL = "<img src='" . $r_v2 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
        } else if (file_exists($r_v3)) {
            $finalURL = "<img src='" . $r_v3 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
        } else {
            $finalURL = "<img src='../assets/img/photo.png' alt='image not uploaded' title='image not uploaded' width='149px' height='168px'  style='border:solid 0.5px #000000'>";
        }
    }

    return $finalURL;
}

?>
