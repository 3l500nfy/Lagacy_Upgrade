
<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
openConnection();
require_once '../lib/candid_scheduling_func.php';

//$tid = ((isset($_POST['test'])) ? ($_POST['test']) : (0));
//echo $tid;
$venueid = $_POST['venue'];
$tid = $_SESSION['tid'];
$matricnum = $_SESSION['matricnum'];
$combination= $_SESSION['combination'];

$cid = get_candidate_id($matricnum, 2);
$schid = get_schedule_id($tid, $venueid);
//$schd = get_schedule_ids_as_array($tid);
//echo "enes".implode($schd);
$subs = get_subject_combination_as_array($tid);
$schdstatus = is_scheduled(array($schid), $cid, $subs);
if ($schdstatus == false) {
    $batch = get_batch($schid);
    if ($batch == -1)
        $report = "full";
    else {

        $query = "INSERT INTO tblcandidatestudent (scheduleid, candidateid, subjectid) VALUES ($schid, $cid,$subs[0])";
        $stmt = $dbh->prepare($query);
        $result = $stmt->execute();
        $numrows = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $report = "success";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SBRS EXAMINATION SLIP</title>
        <script>
            function printpage(){
                
                window.print();
            }
        
        </script>
        <style>
            .modaldialog{
                display: none;
            }
            
            .center{
                text-align: center;
            }
        </style>
        <script type="text/javascript" src="../assets/js/jquery-1.7.2.min.js"></script>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>

        <div id="container" class="container">
             <?php require_once 'schedule_header.php'; ?>

            <center><h2> EXAMINATION SLIP</h2></center>  
            <div>
         

            </div>
            <?php
            if (!isset($report)) {
                echo"<div class='center'>Sorry, the operation was not successfull please try again.</div>";
            } else {
                if ($report == "full") {
                    echo"<div class='center'>Sorry, the operation was not successfull because the venue is full. Please select another venue</div>";
                } else {
                    echo"<div class='center'>You have been successfully scheduled. click <a href='sbrs_exam_slip.php?tid=" . $tid . "&vid=" . $venueid . "'>here</a> to print your exams slip.</div>";
                }
            }
            ?>
        </div>
    </body>
</html>

