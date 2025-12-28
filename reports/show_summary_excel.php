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
    header("Location:" . siteUrl("403.php"));
    exit();
}

$tid = $_POST['tid'];
if (!is_test_administrator_of($tid) && !is_test_compositor_of($tid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($tid);
$minrange = $_POST['min-range'];
$maxrange = $_POST['max-range'];
$fsubj = (isset($_POST['tsbjs']) ? ($_POST['tsbjs']) : (""));
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
$allcandidates = get_all_candidate_as_array($tid, $option);

//echo $fields['1'];
$unique = $test_config['session'] . "_" . $test_config['testname'] . "_" . $test_config['testtypename'] . "_" . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));

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
                    <?php if (in_array("sname", $fields)) { ?>
                        <th class="f1">Surname</th>
                        <?php
                    }
                    if (in_array("fname", $fields)) {
                        ?>
                        <th class="f2">First Name</th>
                        <?php
                    }
                    if (in_array("oname", $fields)) {
                        ?>
                        <th class="f3">Other Names</th> <?php
                    }
                    if (in_array("gender", $fields)) {
                        ?>
                        <th class="f7">Gender</th><?php
                    }
                    if (in_array("prog", $fields)) {
                        ?>
                        <th class="f4">Programme</th>
                        <?php
                    }
                    for ($i = 0; $i < count($tsubj); $i++) {
                        echo ((in_array("subjscore", $fields) && (($fsubj != "" && $tsubj[$i] == $fsubj) || $fsubj == "")) ? ("<th class='fsb" . $tsubj[$i] . "'>" . get_subject_code($tsubj[$i]) . "</th>") : (""));
                    }
                    if (in_array("aggre", $fields)) {
                        ?>
                        <th class="f5">Aggr.</th>
                        <?php
                    }
                    if (in_array("percent", $fields)) {
                        ?>
                        <th title="Percent" class="f6" >%</th>
                    <?php } if (in_array("lapse", $fields)) {
                        ?> 
                        <th class="f8">Lapse</th> 
                    <?php } ?>
                </tr>
            </thead>
            <?php if (count($allcandidates)) { ?>
                <tfoot id="report-summary-footer">
                    <tr>
                        <td class='f0'></td><td></td> 
                        <?php if (in_array("sname", $fields)) { ?>
                            <td class='f1'></td> 
                            <?php
                        }
                        if (in_array("fname", $fields)) {
                            ?>
                            <td class='f2'></td> 
                            <?php
                        }
                        if (in_array("oname", $fields)) {
                            ?>
                            <td class='f3'></td> 
                            <?php
                        }
                        if (in_array("gender", $fields)) {
                            ?>
                            <td class="f7"></td> 
                            <?php
                        }
                        if (in_array("prog", $fields)) {
                            ?>
                            <td class='f4'></td>
                            <?php
                        }
                        for ($i = 0; $i < count($tsubj); $i++) {
                            echo ((in_array("subjscore", $fields) && (($fsubj != "" && $tsubj[$i] == $fsubj) || $fsubj == "")) ? ("<td class='fsb" . $tsubj[$i] . "'></td> " ) : (""));
                        }
                        if (in_array("aggre", $fields)) {
                            ?>
                            <td class='f5'></td>
                            <?php
                        }
                        if (in_array("percent", $fields)) {
                            ?> 
                            <td class="f6"></td> 
                            <?php
                        }
                        if (in_array("lapse", $fields)) {
                            ?> 
                            <td class="f8"></td> 
                        <?php } ?>
                    </tr>
                </tfoot>
            <?php } ?>
            <tbody>
                <?php
                $rec_count = 1;
                $testmark = get_test_total_mark($tid);
                foreach ($allcandidates as $candidate) {
            $lapsetime = get_candidate_lapse_time($candidate, $tid);
            $lapsetime = $lapsetime-0;
            $toecho = true;
            if ($lapsetime < 0) {
                $toecho = false;
                $lapsetime=0;
            }
            
            if($lapsetime>=60)
            $mins = (($lapsetime-($lapsetime % 60))/60);
            else
                $mins=0;
            $mins= $mins-0;
            if($mins >=60)
            $hrs = (($mins -($mins % 60))/60);
            else
                $hrs =0;
            $hrs =$hrs-0;
            $hrs = (($hrs < 10) ? ("0" . $hrs) : ($hrs));
            $min = $mins % 60;
            $min = (($min < 10) ? ("0" . $min) : ($min));
            
                    $candreg = get_RegNo($candidate);
                    $total = 0;
                    $rsubj = get_subject_registered_as_array($tid, $candidate);
                    $studtype = (($test_config['testname'] == 'Post-UTME') ? ("Post-UTME") : (($test_config['testname'] == 'SBRS') ? ("SBRS") : (($test_config['testname'] == 'SBRS-NEW') ? ("SBRS-NEW") : ("REGULAR"))));
                    $biodata = get_candidate_biodata($candidate, $studtype); //check this out later
                    //   if (!(in_array($biodata['programmeid'], $progids) && ((trim(strtoupper($biodata['gender']) == trim(strtoupper($gender))) || $gender == 'all'))))
                    //     continue;
                    $rw = "";

                    $rw .="<tr class='rec'>";
                    $rw .="<td class='f0'>$rec_count</td><td>" . strtoupper($biodata['matricnumber']) . "</td> " . ((in_array("sname", $fields)) ? ("<td class='f1'>" . strtoupper($biodata['surname']) . "</td>") : ('')) . ((in_array("fname", $fields)) ? ("<td class='f2'>" . ucfirst(strtolower($biodata['firstname'])) . "</td>") : ('')) . ((in_array("oname", $fields)) ? ("<td class='f3'> " . ucfirst(strtolower($biodata['othernames'])) . "</td>" ) : ('')) . ((in_array("gender", $fields)) ? ("<td class='f7'> " . ucfirst(strtolower($biodata['gender'])) . "</td>" ) : ('')) . ((in_array("prog", $fields)) ? ("<td class='f4'> " . intelligentStr($biodata['programme'], 30) . "</td>" ) : (''));
                    //for each test subject get individual socre
                    if ($gender != "all") {
                        $g = trim(strtolower($biodata['gender']));
                        //echo $g;
                        //echo"/".$gender;
                        if (($g == "m" || $g == "male") && $gender != "m")
                            $toecho = false;
                        else
                        if (($g == "f" || $g == "female") && $gender != "f")
                            $toecho = false;
                        else
                        if (($g == "") && $gender != "unspecified")
                            $toecho = false;
                    }
                    if ($studtype == "REGULAR" && $category=="ps") {

                        if ($filter_fac != "") {
                            $candfac = get_candidate_faculty($candreg, $studtype);
                            if ($candfac['facultyid'] != $filter_fac) {
                                $toecho = false;
                            } else {
                                if ($filter_dept != "") {
                                    $canddept = get_candidate_department($candreg, $studtype);
                                    if ($canddept['departmentid'] != $filter_dept) {
                                        $toecho = false;
                                    } else {
                                        if ($filter_prog != "") {
                                            $candprog['programmeid'] = get_candidate_programme($candreg, $studtype);
                                            if ($candprog != $filter_prog) {
                                                $toecho = false;
                                            } else {
                                                if ($filter_state != "") {
                                                    $candstate = get_candidate_state($candreg, $studtype);
                                                    if ($candstate['stateid'] != $filter_state) {
                                                        $toecho = false;
                                                    } else {
                                                        if ($filter_lga != "") {
                                                            $candlga = get_candidate_lga($candreg, $studtype);
                                                            if ($candlga['lgaid'] != $filter_lga) {
                                                                $toecho = false;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($studtype == "Post-UTME" && $category=="ps") {
                        if ($filter_fac != "") {
                            $candfac = get_candidate_faculty($candreg, $studtype);
                            if ($candfac['facultyid'] != $filter_fac) {
                                $toecho = false;
                            } else {
                                if ($filter_state != "") {
                                    $candstate = get_candidate_state($candreg, $studtype);
                                    if ($candstate['stateid'] != $filter_state) {
                                        $toecho = false;
                                    }
                                }
                            }
                        }
                    }
                    $aggregate2 = 0;
                    $totaltestmark = 0;
                    foreach ($tsubj as $sbj) {
                        if (in_array($sbj, $rsubj)) {
                            //echo "en ".$sbj;
                            $aggregate = get_candidate_subject_score($tid, $candidate, $sbj);
                            $aggregate2 += $aggregate;
                            $totaltestmark +=get_subject_total_mark($sbj, $tid);
                            if ($fsubj != "") {
                                if (!($aggregate >= $minrange && $aggregate <= $maxrange))
                                    $toecho = false;
                            }
                            $total = $total + $aggregate;
                            $rw .=((in_array("subjscore", $fields) && (($fsubj != "" && $sbj == $fsubj) || $fsubj == "")) ? ("<td class='fsb" . $sbj . "'>$aggregate</td>") : (""));
                        } else {
//                            if ($fsubj != "") {
//                                $toecho = false;
//                            }
                            $rw .=((in_array("subjscore", $fields) && (($fsubj != "" && $sbj == $fsubj) || $fsubj == "")) ? ("<td class='fsb" . $sbj . "'> - </td>") : (""));
                        }
                    }
                    $percent = number_format((($aggregate2 / $totaltestmark) * 100), 2, '.', ',');
                    $rw .=((in_array("aggre", $fields)) ? ("<td style='padding:5px;' class='f5'> " . $total . "</td>" ) : ('')) . ((in_array("percent", $fields)) ? ("<td style='padding:5px;' class='f6'>" . $percent . "%</td>") : ('')) . ((in_array("lapse", $fields)) ? ("<td style='padding:5px;' class='f8'>" . $hrs . ":" . $min . "</td>") : (''));

                    $rw .="</tr>";
                    if ($fsubj == "") {
                        if (!($percent >= $minrange && $percent <= $maxrange))
                            continue;
                        else if ($toecho) {

                            echo $rw;
                            $rec_count++;
                        }
                    } else {
                        if ($toecho) {
                            echo $rw;
                            $rec_count++;
                        } else
                            continue;
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
        if ($rec_count == 1)
            echo"No Matching Record Found.";
        ?>
    </body>
</html>