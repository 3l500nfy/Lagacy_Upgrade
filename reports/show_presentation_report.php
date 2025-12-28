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
$fsubj = ((isset($_POST['tsbjs']) && trim($_POST['tsbjs'] != "")) ? (array($_POST['tsbjs'])) : (get_subject_combination_as_array($tid)));
if (count($fsubj) == 0) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No subject registered on this test</div>";
    exit();
}
//$_POST['candidall']="all";
$candidates = (isset($_POST['candid']) ? ($_POST['candid']) : (null));
$candidateall = (isset($_POST['candidall']) ? ($_POST['candidall']) : (null));
if ($candidateall != null) {
    $candidates = get_all_candidate_as_array($tid);
}

if ($candidates == null && $candidateall == null) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No candidate selected!</div>";
    exit();
}

//echo $fields['1'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Presentation Report</title>
    <link href="<?php echo siteUrl("assets/css/presentationstyle.css"); ?>" rel="stylesheet" type="text/css"></link>
    <style type="text/css">
        @media print {
            #print-ctr {
                display: none;
            }
        }
    </style>
</head>
<body>
<div>
    <?php
    $schdid = get_schedule_ids_as_array($tid);
    //$schdstr = trim(implode(",", $schdid), " ,");//
    //echo trim(implode(",",$candidates)," ,");
    $c = 1;
    foreach ($candidates as $candidate) {
        $studtype = (($test_config['testname'] == 'Post-UTME') ? ("Post-UTME") : (($test_config['testname'] == 'SBRS') ? ("SBRS") : (($test_config['testname'] == 'SBRS-NEW') ? ("SBRS-NEW") : ("REGULAR"))));
        $biodata = get_candidate_biodata($candidate, $studtype);
        $matric = strtoupper($biodata['matricnumber']);
        $surname = strtoupper($biodata['surname']);
        $fname = ucfirst(strtolower($biodata['firstname']));
        $oname = ucfirst(strtolower($biodata['othernames']));
        foreach ($fsubj as $subject) {
            $subs = array(array('subjectid' => $subject));
            if (is_scheduled_on_subject($schdid, $candidate, $subs)) {
                if ($c++ == 1)
                    $presentation = "<div class='presentation'>";
                else
                    $presentation = "<div class='presentation' style='page-break-before:always;'>";
                $presentation .= "<div class='presentation-title'><h2>Presentation Report for " . strtoupper(get_subject_code($subject)) . " - " . get_subject_name($subject) . "</h2>";
                $presentation .= "<div class='fullname'><span id='fn'>Full Name:</span> $surname, $fname $oname</div>";
                $presentation .= "<div class='matric'><span id='mat'>Registration Number:</span> $matric</div></div>";
                echo $presentation;
                get_presentation($candidate, $tid, $subject);
                echo "</div></div>";
            }
        }
    }
    ?>
</div>
<div id="print-ctr"><a href="javascript:window.print();">Print</a></div>
</body>
</html>
