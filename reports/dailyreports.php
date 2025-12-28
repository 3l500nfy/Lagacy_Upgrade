<?php
//echo 'am here1';exit;
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("test_report_function.php");

openConnection();
global $dbh;
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")) && !has_roles(array("Admin"))&& !has_roles(array("Super Admin")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Test Reports";
$navindex = 5;
if (!isset($_POST['tdate']))
    header("Location:home.php");
$testdate = $_POST['tdate'];
$recperpage = 50;

if (!has_roles(array("Admin")) && !has_roles(array("Super Admin"))) {
    //    header("Location:home.php");
}

$list = array();
$query = "SELECT testid FROM tblscheduling WHERE date = ?";
$result = $dbh->prepare($query);
$result->execute(array($testdate));
$count = $result->rowCount();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $list[] = $row['testid'];
}

$param = !empty($list) ? implode(",", $list) : "0";
$_SESSION['tdate'] = $_POST['tdate'];
require_once '../partials/cbt_header.php';

//$query= "select tbltestsubject.subjectid,subjectcode from tbltestsubject inner join tblsubject on tbltestsubject.subjectid=tblsubject.subjectid where testid in(?)";
//$stmt = $dbh->prepare($query);
//$stmt->execute(array($param));
//$row=$stmt->fetch(PDO::FETCH_ASSOC);
////echo $numrow= $stmt->rowCount(); exit;
//$fsubj=$row['subjectcode'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/reportstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    <script type="text/javascript">
    </script>
    <style type="text/css" rel="stylesheet">
        #slider-info{
            font-style:italic;
        }
        .tohide{
            display:none;
        }
        .pgnav:hover{
            background-color:#bdefba;
        }
        .pgnav{
            border-style:solid;
            border-width:1px;
            border-color:#333333;
            padding:2px;
            padding-left: 5px;
            padding-right: 5px;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            margin:2px;
            margin-left: 3px;
            margin-right: 3px;
            cursor: pointer;
        }
        .active-pgnav
        {
            color:#cccccc;
            cursor: default;
            border-style:solid;
            border-width:1px;
            border-color:#cccccc;
            padding:2px;
            padding-left: 5px;
            padding-right: 5px;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            margin:2px;
            margin-left: 3px;
            margin-right: 3px;

        }

        #pagination{
            vertical-align: middle;
            text-align: center;
            padding-top: 10px;
        }
        .current{
            font-weight: bold; color:black;
        }
    </style>
</head>
<body style="background-image: url('../img/bglogo2.jpg');">
<div style="text-align:left;"><a href="javascript:void(0);" id="print-ctr">Print</a> | <a href="javascript:void(0);" id="excel-ctr">Excel</a>
    </div>
<form class="style-frm" id="test-report-frm" method="post" target="_blank" action="">
    <table id="report-summary-table" class="style-tbl">
        <thead id="report-summary-header">
        <tr>
            <th class="f0">S/N</th>
            <th>Reg No.</th>
            <th class='f1'>Surname</th>
            <th class='f2'>First Name</th>
            <th class='f3'>Othername</th>
            <th class="f4">Programme</th>
            <th class="f5">Code</th>
            <th class="f6">Score</th>
        </tr>
        </thead>
        <?php
        $rec_count = 1;
//PUTME
        /*$query = "SELECT tblscore.`candidateid`, tbljamb.RegNo, CandName, `StateOfOrigin`, `LGA`, `Sex`, `Age`, `Faculty`, `Course`, `testid`, tblsubject.subjectid, subjectcode, count(`questionid`) AS score
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
ORDER BY tbljamb.RegNo ASC";*/
        //regular
        $query = "SELECT tblscore.`candidateid`, 
                        tblstudents.matricnumber as matric, 
                        tblstudents.surname as sname, 
                        tblstudents.`firstname` as fname, 
                        tblstudents.`othernames` as oname, 
                        tblprogramme.name as pname, 
                        tblscore.`testid`, 
                        tblsubject.subjectid, 
                        subjectcode, 
                        count(`questionid`) AS score
                FROM `tblscore`
                INNER JOIN tblansweroptions
                INNER JOIN tblquestionbank ON tblscore.`questionid` = tblquestionbank.questionbankid
                INNER JOIN tblsubject ON tblquestionbank.subjectid = tblsubject.subjectid
                INNER JOIN tblscheduledcandidate ON tblscheduledcandidate.candidateid = tblscore.candidateid
                INNER JOIN tblstudents ON tblstudents.matricnumber = tblscheduledcandidate.RegNo
                INNER JOIN tblprogramme ON tblstudents.programmeadmitted = tblprogramme.programmeid
                WHERE (`questionid` = tblansweroptions.questionbankid
                    AND correctness = 1
                    AND tblscore.answerid = tblansweroptions.answerid
                    AND tblscore.testid in ($param)
                ) 
                GROUP BY tblstudents.matricnumber
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

<script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

<script type="text/javascript">

    $(window.top.document).scrollTop(0);//.scrollTop();
    $("#contentframe", top.document).height(0).height($(document).height());
    $(document).on("click", "#print-ctr", function(event) {
        $("#test-report-frm").attr('action', 'show_summary_print.php').submit();
    });

    $(document).on("click", "#excel-ctr", function(event) {
        $("#test-report-frm").attr('action', 'daily_summary_excel.php').submit();
    });
    $("#slider-range").slider({
        range: true,
        min: 0,
        max: 100,
        values: [0, 100],
        slide: function(event, ui) {
            minv = ui.values[ 0 ];
            maxv = ui.values[ 1 ];
            $("#min-range").val(minv);
            $("#max-range").val(maxv);
            if (minv == maxv)
            {
                $("#slider-info").html(minv + "% (" + compute_standard_grade(minv) + ")");
            }
            else
            {
                $("#slider-info").html(minv + "% (" + compute_standard_grade(minv) + ") - " + maxv + "% (" + compute_standard_grade(maxv) + ") inclusive");
            }
        }
    });

    function compute_standard_grade(v)
    {
        if (v >= 70)
            return "A";
        if (v >= 60)
            return "B";
        if (v >= 50)
            return "C";
        if (v >= 45)
            return "D";
        if (v >= 40)
            return "E";
        if (v >= 39)
            return "F1";
        if (v >= 38)
            return "F2";
        if (v >= 35)
            return "F3";
        if (v >= 30)
            return "F4";
        return "F5";
    }

    $(document).on('click', '#load-report', function(event) {
        $("#summary-result").html("<div style='margin-left:auto; margin-right:auto; text-align:center; padding-top:20px;'><img src='<?php echo siteUrl("assets/img/preloader.gif"); ?>' /> <br /><i>Loading...</i></div>");
        $.ajax({
            type: 'POST',
            url: 'show_summary.php',
            data: $("#test-report-frm").serialize()
        }).done(function(msg) {
            $("#contentframe", top.document).height(0);
            $("#summary-result").html(msg);
            $("#contentframe", top.document).height($(document).height());
            $("#contentframe", top.document).width($(document).width());
            //alert($("#report-summary-table").offset().top);
            $(top.document).scrollTop($("#report-summary-table").offset().top);

        });
        return false;
    });
    $(document).on('change', '#tsbjs', function(event) {
        if ($(this).val() != "")
        {
            $("#disp-aggre").prop("checked", false).prop("disabled", true);
        }
        else
        {
            $("#disp-aggre").prop("disabled", false);
        }


    });
    $(document).on('click', '.pgnav', function(event) {
        $(".active-pgnav").removeClass("active-pgnav").addClass("pgnav");
        $(".rec").addClass("tohide");
        var strec = $(this).attr("data-start");
        var stoprec = $(this).attr("data-stop");
        $(".rec").each(function() {
            if ($(this).index() >= strec && $(this).index() <= stoprec)
                $(this).removeClass("tohide");
        });
        $(window.top.document).scrollTop(150);//.scrollTop();
        $("#contentframe", top.document).height(0).height($(document).height());

        $(this).addClass("active-pgnav").removeClass(".pgnav");

    });

    $(document).on('change', '#fac', function(event) {
        var dis = $(this);
        $("#prog").html("<option value=''> All </option>");
        $("#dept").html("<option value=''> Loading... </option>");
        if (dis.val() == "")
        {
            $("#dept").html("<option value=''> All </option>");
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'getters/get_dept.php',
            data: {facid: dis.val()}
        }).done(function(msg) {
            $("#dept").html("<option value=''> All </option>" + msg);
        });
        return false;
    });

    $(document).on('change', '#dept', function(event) {
        var dis = $(this);
        $("#prog").html("<option value=''> Loading... </option>");
        if (dis.val() == "")
        {
            $("#prog").html("<option value=''> All </option>");
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'getters/get_prog.php',
            data: {deptid: dis.val()}
        }).done(function(msg) {
            $("#prog").html("<option value=''> All </option>" + msg);
        });
        return false;
    });

    $(document).on('change', '#state', function(event) {
        var dis = $(this);
        $("#lga").html("<option value=''> Loading... </option>");
        if (dis.val() == "")
        {
            $("#lga").html("<option value=''> All </option>");
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'getters/get_lga.php',
            data: {stateid: dis.val()}
        }).done(function(msg) {
            $("#lga").html("<option value=''> All </option>" + msg);
        });
        return false;
    });

    $(document).on('click', '#adv-ctr', function(event) {
        $("#advance-filter").toggle();
        $("#contentframe", top.document).height($(document).height());
        $("#contentframe", top.document).width($(document).width());

    });

    $(document).on('click', '.category', function(event) {
        var v = $(this).val();
        $(".category1").hide();

        if (v == "individual") {
            $("#individual1").show();
        } else if (v == "schedule") {
            $("#schedule1").show();
        } else {
            $("#ps1").show();
            $("#ps2").show();
        }

        $("#contentframe", top.document).height($(document).height());
        $("#contentframe", top.document).width($(document).width());

    });
</script>
</body>
</html>