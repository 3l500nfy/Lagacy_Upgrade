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

$testdate = $_SESSION['tdate'];
$recperpage=50;

if (!has_roles(array("Admin"))&& !has_roles(array("Super Admin")))
//    header("Location:home.php");

$query = "select * from  tblscheduling where date='$testdate'";
$result = $dbh->prepare($query);
$result->execute();
$count=$result->rowCount();
$i=0;;
for ($i=0; $i<=$count; $i++){
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $testid = $row['testid'];
    $list[]=$testid;
}
$param=trim(implode(",", $list),',');
$unique = $testdate;
//var_dump($list); exit;

header("Content-Disposition: attachment; filename=\"$unique.xls\"");
header("Content-Type: application/vnd.ms-excel");
//header('Content-type: application/vnd.ms-excel');
?>

<html>
<head>
    <title>Result Summary</title>
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/reportstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <style type="text/css" >
        @media print{
            .print-btn{ display:none;}
        }
        .filtered{
            display: none;
        }
    </style>

</head>
<body>
<table id="report-summary-table" class="style-tbl">
    <thead id="report-summary-header" >
    <tr>
        <th class="f0">S/N</th>
        <th>Reg No.</th>
            <th class="f1">Surname</th>
            <th class="f2">First Name</th>
           <th class="f3">Other Names</th>
            <th class="f7">Gender</th>
            <th class="f4">Programme</th>
            <th class="f5">Code</th>
            <th class="f6">Score</th>
            <th class="f8">%</th>

    </tr>
    </thead>
    <?php
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
        AND testid in ($param)
        ) group by tblstudents.matricnumber
        ORDER BY matric ASC";

    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $numrow = $stmt->rowCount();
    //        echo $numrow;exit;
    if ($numrow) { ?>
        <tfoot id="report-summary-footer">
        <tr>
            <td class='f0'>S/N</td>
            <td>Reg.No</td>
            <td class='f1'>Surname</td>
            <td class='f2'>First Name</td>
            <td class='f3'>Othername</td>
            <td class='f4'>Programme</td>
            <td class='f5'>Code</td>
            <td class="f6">Score</td>
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
//                    foreach ($subjectscore as $key=>$scr) {
//
//                            if ($fsubj != "") {
//                                if (($scr !== "-") && !($scr >= $minrange && $scr <= $maxrange))
//                                    $toecho = false;
//                            }
//                            $rw .= "<td class='fsb" . $key . " " . ((!in_array("subjscore", $fields) || ($fsubj != "" && $key != $fsubj)) ? ("filtered") : ("")) . "'>$scr </td>";
//                    }

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
            $rw .="<td class='f0'>$rec_count</td><td> " . strtoupper($row['matric']) . "</td><td class='f1'>" . $row['sname'] . "</td><td class='f2'>" . $row['fname'] . "</td><td class='f3'>" . $row['oname'] . "</td><td class='f4'>" . intelligentStr($row['pname'], 30) . "</td><td class='f5'>" . $row['subjectcode'] . "</td><td class='f6'>" . $row['score'] . "</td>";
        }
        //for each test subject get individual score
        //$aggregate2 = 0;
        //$totaltestmark = 0;

        /* for($i = 0; $i < count($tsubj); $i++){
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
         }*/

    }

    //        if (!$firstrec) {
    //            $rw .= "<td style='padding:5px;' class='f5 " . ((!in_array("aggre", $fields)) ? ('filtered') : ('')) . "'>" . $aggregate . "</td>";
    //            $rw .= "</tr>";
    //        }

    ?>
    </tbody>
</table>
</form>
<?php
if ($rec_count == 1)
    echo"No Matching Record Found.";
?>
</body>
</html>