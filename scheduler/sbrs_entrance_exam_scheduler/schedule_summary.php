
<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
require_once("../../lib/candid_scheduling_func.php");
openConnection();

$venueid = $_POST['venue'];
$tid = $_SESSION['tid'];


if (isset($_POST['subj'])) {

    $subjectid = $_POST['subj'];

    $subjid1 = $subjectid[0];
    $subjid2 = $subjectid[1];
    $subjid3 = $subjectid[2];
    $subjid4 = $subjectid[3];
    $subjid5 = $subjectid[4];
}

//echo $subjectid;

$ApplicationID = $_SESSION['ApplicationID'];

//$_SESSION['subjids'] = $subjectid;


$cid = get_candidate_id($ApplicationID, 4);
//echo $cid;
$schid = get_schedule_id($tid, $venueid);
//echo $schid;


$schdstatus = is_scheduled(array($schid), $cid, $subjectid);
//echo $schdstatus;
if ($schdstatus == false) {

    $batch = get_batch($schid);

    if ($batch == -1) {
        $report = "full";
    } else {

        $query = "INSERT INTO tblcandidatestudent (scheduleid, candidateid, subjectid) VALUES ($schid, $cid,$subjid1),($schid, $cid,$subjid2 ),($schid, $cid, $subjid3),($schid, $cid, $subjid4),($schid, $cid, $subjid5)";
        $stmt = $dbh->prepare($query);
        $stmt->execute();


        if ($stmt) {
            $report = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SBRS ENTRANCE EXAMINATION SLIP</title>
        <script>
            function printpage(){
                
                window.print();
            }
            
        
        </script>
        <style>
            .modaldialog{
                display: none;
            }

            .center { text-align: center;}
        </style>
        <?php require_once("../../partials/cssimports.php") ?>
        <script type="text/javascript" src="jquery-1.7.2.min.js"></script>

    </head>
    <body>

        <div id="container" class="container">
            <div class="header">
                <img class="banner" src="<?php echo siteUrl('assets/img/banner.png') ?>" alt="banner" >


            </div>
            <center><h2>SBRS ENTRANCE EXAMINATION SLIP</h2></center>

            <?php
            if (!isset($report)) {
                echo"<div class='center'>Sorry, the operation was not successfull please try again.</div>";
            } else {
                if ($report == "full") {
                    echo"<div class='center'>Sorry, the operation was not successfull because the venue is full. Please select another venue</div>";
                } else {
                    echo"<div class='center'>You have been successfully scheduled. click <a href='sbrs_entrance_exam_slip.php?tid=" . $tid . "&vid=" . $venueid . "'>here</a> to print your exams slip.</div>";
                }
            }
            ?>
            <script type ="text/javascript">
                                   
            </script>
    </body>
</html>

