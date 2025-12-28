
<?php
if(!isset($_SESSION)) session_start();

require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();

$venueid = $_POST['venue'];
$centerid = $_POST['centre'];
$tid = $_SESSION['tid'];
$subj2 = ((isset($_POST['subj2']))?($_POST['subj2']):(""));
$subj3 = ((isset($_POST['subj3']))?($_POST['subj3']):(""));
$subj4 = ((isset($_POST['subj4']))?($_POST['subj4']):(""));
$jambno = $_SESSION['RegNo'];
 

$cid = get_candidate_id($jambno, 1);
$schid = get_schedule_id($tid, $venueid);
$schdstatus = is_scheduled(array($schid), $cid, array($subj2, $subj3, $subj4));
if ($schdstatus == false) {
    $batch = get_batch($schid);
    if ($batch == -1)
        $report = "full";
    else {
        
        $query = "INSERT INTO tblcandidatestudent (scheduleid, candidateid, subjectid) VALUES ($schid, $cid,'2' ),($schid, $cid, $subj2),($schid, $cid, $subj3),($schid, $cid, $subj4)";
        $stmt = $dbh->prepare($query);
        $result = $stmt->execute();
        if ($result) {
            $report = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>POST UTME EXAMINATION SLIP</title>
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

        <script type="text/javascript" src="jquery-1.7.2.min.js"></script>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>

        <div id="container" class="container">
            
               <?php require_once 'schedule_header.php'; ?>


            
            <center><h2>POST UTME EXAMINATION SLIP</h2></center>
            
            <?php
            if(!isset($report))
            {
                echo"<div class='center'>Sorry, the operation was not successfull please try again.</div>";
            }
 else {
                if($report=="full")
                {
                  echo"<div class='center'>Sorry, the operation was not successfull because the venue is full. Please select another venue</div>";  
                }
 else {
                    echo"<div class='center'>You have been successfully scheduled. click <a href='putme_exam_slip.php?tid=".$tid."&centid=".$centerid."&vid=".$venueid."'>here</a> to print your exams slip.</div>";
}
}
            ?>
        <script type ="text/javascript">
                                   
        </script>
    </body>
</html>

