<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("test_report_function.php");

openConnection();
global $dbh;
authorize();
//get testid
if (!isset($_POST['tid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$tid = $_POST['tid'];
if (!is_test_administrator_of($tid) && !is_test_compositor_of($tid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($tid);
$recperpage = $_POST['perpage'];
$minrange = $_POST['min-range'];
$maxrange = $_POST['max-range'];
$fsubj = (isset($_POST['tsbjs']) ? ($_POST['tsbjs']) : (""));
//var_dump($fsubj);exit;
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
$allcandidates = get_all_candidate_as_array($tid, $option);
//echo $fields['1'];

?>


    <div style="text-align:left;"><a href="javascript:void(0);" id="print-ctr">Print</a> | <a href="javascript:void(0);"
                                                                                              id="excel-ctr">Excel</a>
    </div>
    <table id="report-summary-table" class="style-tbl">
        <thead id="report-summary-header">
        <tr>
            <th class="f0">S/N</th>
            <th>Reg No.</th>
            <th class='f1'>Surname</th>
            <th class='f2'>First Name</th>
            <th class='f3'>Othername</th>
            <th class="f7 <?php
            if (!in_array("gender", $fields))
                echo "filtered";
            ?>">Gender
            </th>
            <th class="f4 <?php
            if (!in_array("prog", $fields))
                echo "filtered";
            ?>">Programme
            </th><?php
            for ($i = 0; $i < count($tsubj); $i++)
                echo "<th class='fsb" . $tsubj[$i] . " " . ((!in_array("subjscore", $fields) || ($fsubj != "" && $tsubj[$i] != $fsubj)) ? ("filtered") : ("")) . "'>" . get_subject_code($tsubj[$i]) . "</th>";
            ?>
            <th class="f5 <?php
            if (!in_array("aggre", $fields))
                echo "filtered";
            ?> ">Aggr.
            </th>
        </tr>
        </thead>
        <?php
        $rec_count = 1;
//PUTME
        $query = "SELECT tblscore.`candidateid`, tbljamb.RegNo, CandName, `StateOfOrigin`, `LGA`, `Sex`, `Age`, `Faculty`, `Course`, `testid`, tblsubject.subjectid, subjectcode, count(`questionid`) AS score
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
GROUP BY `candidateid` , subjectcode
ORDER BY tbljamb.RegNo ASC";

/*        //regular
        $query="SELECT tblscore.`candidateid` , tblstudents.matricnumber as matric, tblstudents.surname as sname, tblstudents.`firstname` as fname, tblstudents.`othernames` as oname, tblprogramme.name as pname, `testid` , tblsubject.subjectid, subjectcode, count( `questionid` ) AS score
FROM `tblscore`
INNER JOIN tblansweroptions
INNER JOIN tblquestionbank ON tblscore.`questionid` = tblquestionbank.questionbankid
INNER JOIN tblsubject ON tblquestionbank.subjectid = tblsubject.subjectid
INNER JOIN tblscheduledcandidate ON tblscheduledcandidate.candidateid = tblscore.candidateid
INNER JOIN tblstudents ON tblstudents.matricnumber = tblscheduledcandidate.RegNo
INNER JOIN tblprogramme ON tblstudents.programmeadmitted = tblprogramme.programmeid
WHERE ( `questionid` = tblansweroptions.questionbankid
AND correctness =1
AND tblscore.answerid = tblansweroptions.answerid
AND testid =$tid
) group by tblstudents.matricnumber
ORDER BY matric ASC";
*/
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $numrow = $stmt->rowCount();
        //echo $numrow;exit;
        if ($numrow) { ?>
            <tfoot id="report-summary-footer">
            <tr>
                <td class='f0'>S/N</td>
                <td>Reg.No</td>
                <td class='f1'>Surname</td>
                <td class='f2'>First Name</td>
                <td class='f3'>Othername</td>
                <td class="f7 <?php
                if (!in_array("gender", $fields))
                    echo "filtered";
                ?>"></td>
                <td class='f4 <?php
                if (!in_array("prog", $fields))
                    echo "filtered";
                ?>'></td><?php
                for ($i = 0; $i < count($tsubj); $i++)
                    echo "<td class='fsb" . $tsubj[$i] . " " . ((!in_array("subjscore", $fields) || ($fsubj != "" && $tsubj[$i] != $fsubj)) ? ("filtered") : ("")) . "'></td>";
                ?>
                <td class='f5 <?php
                if (!in_array("aggre", $fields))
                    echo "filtered";
                ?>'></td>
            </tr>
            </tfoot>
        <?php } ?>
        <tbody>
        <?php

        $studentid;
        $rw = "";
        $firstrec = true;
        $lastaggregate = 0;
        $rec_count2=0;
        $toecho = true;
        $subjectscore = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rec_count2++;


            if (!isset($studentid) || (isset($studentid) && $studentid != $row['candidateid'])) {

                $studentid = $row['candidateid'];

                if (!$firstrec || $rec_count2 == $numrow) {
                    foreach ($subjectscore as $key=>$scr) {

                            if ($fsubj != "") {
                                if (($scr !== "-") && !($scr >= $minrange && $scr <= $maxrange))
                                    $toecho = false;
                            }
                            $rw .= "<td class='fsb" . $key . " " . ((!in_array("subjscore", $fields) || ($fsubj != "" && $key != $fsubj)) ? ("filtered") : ("")) . "'>$scr </td>";
                    }

                    $rw .= "<td style='padding:5px;' class='f5 " . ((!in_array("aggre", $fields)) ? ('filtered') : ('')) . "'>" . $lastaggregate . "</td>";
                    $rw .= "</tr>";
                    $lastaggregate = 0;
                    if ($toecho) {
                        echo $rw;
                        $rec_count++;
                    }
                    $rw = "";
                    $toecho = true;
                    $subjectscore = array();
                    //break;
                } else {
                    $firstrec = false;
                }

                $rw .= "<tr class='rec" . (($rec_count > $recperpage) ? (" tohide") : ("")) . "'>";
                //PUTME
                //$rw .= "<td class='f0'>$rec_count</td><td> " . strtoupper($row['RegNo']) . "</td><td class='f1'>" . $row['CandName'] . "</td><td class='f7 " . ((!in_array("gender", $fields)) ? ('filtered') : ('')) . "'>" . ucfirst(strtolower($row['Sex'])) . "</td><td class='f4 " . ((!in_array("prog", $fields)) ? ('filtered') : ('')) . "'>" . intelligentStr($row['Course'], 30) . "</td>";
                //Regular
                $rw .="<td class='f0'>$rec_count</td><td> " . strtoupper($row['matric']) . "</td><td class='f1'>" . $row['sname'] . "</td><td class='f2'>" . $row['fname'] . "</td><td class='f3'>" . $row['oname'] . "</td><td class='f4 " . ((!in_array("prog", $fields)) ? ('filtered') : ('')) . "'>" . intelligentStr($row['pname'], 30) . "</td>";
            }
            //for each test subject get individual score
            //$aggregate2 = 0;
            //$totaltestmark = 0;

            for($i = 0; $i < count($tsubj); $i++){
            $sbj= $tsubj[$i];
            //echo $sbj;
                if ($sbj==$row['subjectid']) {
                    $subjectscore[$sbj]= $row['score'];
                    $aggregate = $row['score'];
                    $lastaggregate = $lastaggregate + $aggregate;
                } else {
                    if(!isset($subjectscore[$sbj]))
                        $subjectscore[$sbj]= "-";
                }
            }

        }

        //        if (!$firstrec) {
        //            $rw .= "<td style='padding:5px;' class='f5 " . ((!in_array("aggre", $fields)) ? ('filtered') : ('')) . "'>" . $aggregate . "</td>";
        //            $rw .= "</tr>";
        //        }

        ?>
        </tbody>
    </table>
<?php
if ($rec_count == 1)
    echo "No Matching Record Found.";
else {
    if ($rec_count > $recperpage) {
        echo "<div id='pagination'>";
        for ($i = 1; ($i * $recperpage) < $rec_count; $i++) {
            if ($i == 1)
                echo "<div class='active-pgnav' data-start='" . (($i - 1) * $recperpage) . "' data-stop='" . (($i * $recperpage) - 1) . "'>$i</div>";
            else
                echo "<div class='pgnav' data-start='" . (($i - 1) * $recperpage) . "' data-stop='" . (($i * $recperpage) - 1) . "'>$i</div>";

            if ((($i * $recperpage) < $rec_count - 1) && ((($i + 1) * $recperpage) > $rec_count))
                echo "<div class='pgnav' data-start='" . (($i) * $recperpage) . "' data-stop='" . ((($i + 1) * $recperpage) - 1) . "'>" . ($i + 1) . "</div>";
        }
        echo "</div>";
    }
}
?>
