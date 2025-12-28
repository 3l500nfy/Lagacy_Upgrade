<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
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

$reg = clean($_POST['candidate-reg']);


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
        $schedules = $_POST['schd'];
        $schedules = array_values($schedules);

        $test_config = get_test_config_param_as_array($testid);

        if (strtoupper(trim($test_config['testcodeid'])) == "1") {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Candidate list upload not supported on Post-UTME Test!</div>";
            exit();
        }

        $activeSchedule = 0;
        $test_subjects = get_test_subjects_as_array($testid);

        if (count($test_subjects) == 0) {// found no subject in the test
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test has no subject registered!</div>";
            exit();
        }
        $test_subjects_str = trim(implode(",", $test_subjects), " ,");

        #schedule individual candidate in the list

            $matric = $reg;
            $testtype = (($test_config['testcodeid'] != "2" && $test_config['testcodeid'] != "12" && $test_config['testcodeid'] != "1") ? ("REGULAR") : ($test_config['testname']));

            if (!candid_exist($matric, $testtype)) {
                echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>" . strtoupper($matric) . " Student does not exist!</div>";
                echo'<a href="single_candidate_upload.php?tid='.$testid.'" target="contentframe">Back to single candidate registration form.</a>';
                exit;
            }

            if (is_scheduled($testid, $matric, $testtype)) {
                echo"<div class='alert-notice' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>" . strtoupper($matric) . " Student is already scheduled!</div>";
                echo'<a href="single_candidate_upload.php?tid='.$testid.'" target="contentframe">Back to single candidate registration form.</a>';
                exit;
            }

            $schedule = $schedules[$activeSchedule];

            if (count($schedules) == 0) {
                echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>All selected schedules are filled up!</div>";
                exit();
            } else {
                // do the scheduling
                $candidateid = get_candidate_id($matric, (($testtype == "REGULAR") ? (3) : (($testtype == "Post-UTME") ? (1) : (($testtype == "SBRS") ? (2) : (4)))));
               
                foreach ($test_subjects as $tsbj) {
                    // First insert into tblcandidatestudent and get the auto-increment id
                    // Insert with placeholder candidateid=0 (required field, will be updated immediately)
                    $query = "insert into tblcandidatestudent (scheduleid, subjectid, candidateid) values (?,?,0)";
                    $stmt = $dbh->prepare($query);
                    $result1 = $stmt->execute(array($schedule, $tsbj));
                    
                    if (!$result1) {
                        echo "<div class='alert-error'>Error inserting into tblcandidatestudent: " . implode(", ", $stmt->errorInfo()) . "</div>";
                        exit();
                    }
                    
                    // Get the auto-increment id
                    $new_id = $dbh->lastInsertId();
                    
                    // Update the candidateid with the new id
                    $query = "update tblcandidatestudent set candidateid = ? where id = ?";
                    $stmt = $dbh->prepare($query);
                    $result1 = $stmt->execute(array($new_id, $new_id));
                    
                    if (!$result1) {
                        echo "<div class='alert-error'>Error updating candidateid in tblcandidatestudent: " . implode(", ", $stmt->errorInfo()) . "</div>";
                        exit();
                    }
                    
                    // Update the existing record in tblscheduledcandidate with the new id
                    // Find the record by RegNo and candidatetype (created by get_candidate_id)
                    $candtype_val = (($testtype == "REGULAR") ? (3) : (($testtype == "Post-UTME") ? (1) : (($testtype == "SBRS") ? (2) : (4))));
                    $query = "update tblscheduledcandidate set scheduleid = ?, subjectid = ?, add_index = 0, testid = ?, candidateid = ? where UPPER(RegNo) = UPPER(?) and candidatetype = ?";
                    $stmt = $dbh->prepare($query);
                    
                    $result2 = $stmt->execute(array(
                        $schedule,       // scheduleid
                        $tsbj,          // subjectid
                        $testid,        // testid
                        $new_id,        // candidateid (using the new id from tblcandidatestudent)
                        $matric,        // RegNo (for WHERE clause)
                        $candtype_val   // candidatetype (for WHERE clause)
                    ));
                    
                    if (!$result2) {
                        echo "<div class='alert-error'>Error updating tblscheduledcandidate: " . implode(", ", $stmt->errorInfo()) . "</div>";
                        exit();
                    }
                }
                echo"<div class='alert-success' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>" . strtoupper($matric) . " Student was scheduled successfully!</div>";
            }
        ?>
        <a href="single_candidate_upload.php?tid=<?php echo $testid; ?>" target="contentframe">Back to single candidate registration form.</a>
        
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
        <script type="text/javascript">

        </script>
    </body>
</html>