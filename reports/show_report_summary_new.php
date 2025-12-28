<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
//require_once('../../lib/candid_scheduling_func.php');
require_once("test_report_function.php");
openConnection();
global $dbh;
authorize();

//get testid
if (!isset($_POST['tid'])) {
    //header("Location:" . siteUrl("403.php"));
   // exit();
}

$tid = 2274;//$_POST['tid'];
if (!is_test_administrator_of($tid) && !is_test_compositor_of($tid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($tid);
$minrange = $_POST['min-range'];
$maxrange = $_POST['max-range'];
$fsubj = (isset($_POST['tsubjs']) ? ($_POST['tsubjs']) : (""));
//$allcandidates = get_all_candidate_as_array($tid);
$progids = (isset($_POST['filter-prog']) ? ($_POST['filter-prog']) : (array()));
$fields = (isset($_POST['disp-field']) ? ($_POST['disp-field']) : (array()));
$tsubj = get_subject_combination_as_array($tid);
$gender = clean($_POST['gender']);
$category = $_POST['category'];
if ($category == 'individual') {
    $filter_regno = clean($_POST['regno']);
    $option['category'] = 'regno';
    $option['regno'] = $filter_regno;
} else if ($category == 'schedule') {
    $filter_schedule = clean($_POST['schedule']);
    $option['category'] = 'schedule';
    $option['schedule'] = $filter_schedule;
} else if ($category == 'ps') {
    $filter_fac = $_POST['fac'];
    $filter_dept = $_POST['dept'];
    $filter_prog = $_POST['prog'];
    $filter_state = $_POST['state'];
    $filter_lga = $_POST['lga'];
    $option['category'] = 'ps';
    $option['fac'] = $filter_fac;
    $option['dept'] = $filter_dept;
    $option['prog'] = $filter_prog;
    $option['state'] = $filter_state;
    $option['lga'] = $filter_lga;
}
/*
$sql = "select tbltestsubject.subjectid,subjectcode from tbltestsubject inner join tblsubject on tbltestsubject.subjectid=tblsubject.subjectid where testid='$tid'";
$query = mysql_query($sql) or die("Error (get_subject_selection_as_array)");
$subjects=mysql_fetch_assoc($query);
//$sbj[]=$subjects['subjectcode'];
$numrow= mysql_num_rows($query);*/


$query= "select tbltestsubject.subjectid,subjectcode from tbltestsubject inner join tblsubject on tbltestsubject.subjectid=tblsubject.subjectid where testid=?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($tid));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
$numrow= $stmt->rowCount();

if($numrow > 1){

$query="SELECT tblscore.`candidateid` , tbljamb.RegNo, CandName, `StateOfOrigin` , `LGA` , `Sex` , `Age` , `Faculty` , `Course` , `testid` , subjectcode, count( `questionid` ) AS score
FROM `tblscore`
INNER JOIN tblansweroptions
INNER JOIN tblquestionbank ON tblscore.`questionid` = tblquestionbank.questionbankid
INNER JOIN tblsubject ON tblquestionbank.subjectid = tblsubject.subjectid
INNER JOIN tblscheduledcandidate ON tblscheduledcandidate.candidateid = tblscore.candidateid
INNER JOIN tbljamb ON tbljamb.RegNo = tblscheduledcandidate.RegNo
WHERE ( `questionid` = tblansweroptions.questionbankid
    AND correctness =1
    AND tblscore.answerid = tblansweroptions.answerid
    AND testid =$tid
)
GROUP BY `candidateid` , subjectcode";

    $stmt = $dbh->prepare($query);
    $stmt->execute(array(1,"tblansweroptions.answerid",$tid));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    $numrow= $stmt->rowCount();

/*$query="SELECT tblscore.`candidateid`, tblstudents.matricnumber, concat(tblstudents.surname, tblstudents.`firstname` , tblstudents.`othernames`) as fname , tblprogramme.name as programme, `testid` , subjectcode, SUM(markperquestion) AS aggregate, count( `questionid` ) AS score
FROM `tblscore`
INNER JOIN tblansweroptions
INNER JOIN tblquestionbank ON tblscore.`questionid` = tblquestionbank.questionbankid
INNER JOIN tblsubject ON tblquestionbank.subjectid = tblsubject.subjectid
INNER JOIN tblscheduledcandidate ON tblscheduledcandidate.candidateid = tblscore.candidateid
INNER JOIN tblstudents ON tblstudents.matricnumber = tblscheduledcandidate.RegNo
INNER JOIN tblprogramme ON tblstudents.programmeadmitted = tblprogramme.programmeid
INNER JOIN tbltestquestion on tbltestquestion.questionbankid = tblscore.questionid
INNER join tbltestsection on tbltestsection.testsectionid = tbltestquestion.testsectionid
WHERE ( `questionid` = tblansweroptions.questionbankid
AND correctness =1
AND tblscore.answerid = tblansweroptions.answerid
AND testid =$tid) group by tblstudents.matricnumber, subjectcode";*/
        ?>
<table>
    <tr>
        <th>SN</th>
        <th>Name</th>
        <th>Programme</th>
        <?php
        do {
            echo "<th>".$row['subjectcode']."</th>";
			
        }while($row = $stmt->fetch(PDO::FETCH_ASSOC));
        ?>

    </tr>

</table>

<?php }else{

$query="SELECT tblscore.`candidateid`, tblstudents.matricnumber as regno, concat_ucase(tblstudents.surname,' ', tblstudents.`firstname`,' ', tblstudents.`othernames`) as fname , tblprogramme.name as programme, `testid` , subjectcode, SUM(markperquestion) AS aggregate, count( `questionid` ) AS score
FROM `tblscore`
INNER JOIN tblansweroptions
INNER JOIN tblquestionbank ON tblscore.`questionid` = tblquestionbank.questionbankid
INNER JOIN tblsubject ON tblquestionbank.subjectid = tblsubject.subjectid
INNER JOIN tblscheduledcandidate ON tblscheduledcandidate.candidateid = tblscore.candidateid
INNER JOIN tblstudents ON tblstudents.matricnumber = tblscheduledcandidate.RegNo
INNER JOIN tblprogramme ON tblstudents.programmeadmitted = tblprogramme.programmeid
INNER JOIN tbltestquestion on tbltestquestion.questionbankid = tblscore.questionid
INNER join tbltestsection on tbltestsection.testsectionid = tbltestquestion.testsectionid
WHERE ( `questionid` = tblansweroptions.questionbankid
AND correctness =?
AND tblscore.answerid = ?
AND testid =?) group by tblstudents.matricnumber";

$stmt = $dbh->prepare($query);
$stmt->execute(array(1,"tblansweroptions.answerid",$tid));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
$numrow= $stmt->rowCount();
?>
<html>

<head>

</head>
<body>
<table>
        <tr>
            <th>SN</th>
            <th>Reg. No</th>
            <th>Name</th>
            <th>Programme</th>
            <th><?php echo $row['subjectcode']; ?></th>
        </tr>
        <?php
        $counter = 1;

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)); {
            ?>
            <tr>
                <td><?php echo $counter; ?></td>
                <td><?php echo $row['fname']; ?></td>
                <td><?php echo $row['regno']; ?></td>
                <td><?php echo $row['programme']; ?></td>
                <td><?php echo $row['aggregate']; ?></td>
            </tr>
            <?php
            $counter++;
        }

    }?>

</table>
</body>
</html>
