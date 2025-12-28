<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!isset($_POST['tid'])) {
    header("Location:" . siteUrl("admin.php"));
    exit();
}

$testid = clean($_POST['tid']);

if (!isset($_POST['candidate-reg']) || trim($_POST['candidate-reg']) == "") {
    header("Location:" . siteUrl("single_candidate_upload.php"));
    exit();
}

$regs = clean($_POST['candidate-reg']);
$regs = explode(",", $regs);

if (!is_test_administrator_of($testid))
    header("Location:" . siteUrl("403.php"));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
        </script>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <?php
        $sched = $_POST['schd'];
        $schedules = get_test_schedule_as_array($testid);
        echo "Adamu Nsuk";exit;
        $test_config = get_test_config_param_as_array($testid);

        if (strtoupper(trim($test_config['testcodeid'])) != "1") {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Candidate list upload not supported!</div>";
            exit();
        }

        $activeSchedule = $sched[0];
        //  $test_subjects = get_test_subjects_as_array($testid);
        $output = "";
        foreach ($regs as $reg) {
            $reg = trim($reg);
            if ($reg == "") {
                continue;
            }
            $test_subjects = get_jamb_subject_combination($reg);

            if (count($test_subjects) == 0) {// found no subject in the test
                $output.="<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test has no subject registered!</div>";
                continue;
            }
            $test_subjects_str = trim(implode(",", $test_subjects), " ,");

            #schedule individual candidate in the list

            $matric = $reg;
            $testtype = (($test_config['testcodeid'] != "2" && $test_config['testcodeid'] != "12" && $test_config['testcodeid'] = "1") ? ("Post-UTME") : ($test_config['testname']));

            if (!candid_exist($matric, $testtype)) {
                $output.="<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>" . strtoupper($matric) . " Student does not exist!</div>";
                continue;
                //echo'<a href="single_candidate_upload.php?tid='.$testid.'" target="contentframe">Back to single candidate registration form.</a>';
                //exit;
            }

            $candidateid = get_candidate_id($reg, 1);

            if (is_scheduled_on_subject($schedules, $candidateid, $test_subjects)) {
                   
                $output.="<div class='alert-notice' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>" . strtoupper($matric) . " Student is already scheduled!</div>";
                continue;
                //echo'<a href="single_candidate_upload.php?tid='.$testid.'" target="contentframe">Back to single candidate registration form.</a>';
                //exit;
            }
          
            if (count($schedules) == 0) {
                $output.="<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>All selected schedules are filled up!</div>";
                continue;
                //exit();
            } else {
                $schedule = $activeSchedule;
                // do the scheduling
                //$candidateid = get_candidate_id($matric, (($testtype == "REGULAR") ? (3) : (($testtype == "Post-UTME") ? (1) : (($testtype == "SBRS") ? (2) : (4)))));
                foreach ($test_subjects as $tsbj) {
                    $query = "insert into tblcandidatestudent (scheduleid, candidateid, subjectid)values (?,?,?)";
                    $stmt=$dbh->prepare($query);
                    $stmt->execute(array($schedule,$candidateid,$tsbj,$matric));
                }
                $output.="<div class='alert-success' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>" . strtoupper($matric) . " Candidate was scheduled successfully!</div>";
            }
        }
        echo $output;
        ?>
        <a href="single_candidate_upload.php?tid=<?php echo $testid; ?>" target="contentframe">Back to single candidate registration form.</a>

        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
        <script type="text/javascript">

        </script>
    </body>
</html>
