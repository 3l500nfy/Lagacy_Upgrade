
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
$matricnum = $_SESSION['matricnumber'];

$cid = get_candidate_id($matricnum, 3);
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
            
            .center{
                text-align: center;
            }
        </style>
       
        <script type="text/javascript" src="../assets/js/jquery-1.7.2.min.js"></script>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>
            
             <?php require_once 'schedule_header.php'; ?>
            <center><h2> EXAMINATION SLIP</h2></center>  
                        

                       <?php
            if (!isset($report)) {
                echo"<div class='center'>Sorry, the operation was not successfull please try again.</div>";
            } else {
                if ($report == "full") {
                    echo"<div class='center'>Sorry, the operation was not successfull because the venue is full. Please select another venue</div>";
                } else {
                    echo"<div class='center'>You have been successfully scheduled. click <a href='regular_exam_slip.php?tid=" . $tid . "&vid=" . $venueid . "'>here</a> to print your exams slip.</div>";
                }
            }
            ?>
            <script type ="text/javascript">
                /*   $("#centre").live("change", function(){
                    // alert("HERE");
                    $("#venue").html("<option value=''>loading...</option>").load("getVenue_Scheduleinfo.php", {centreid: $(this).val()});
                });
                $("#proceed").live('click',function(){
                    if($("#venue").val()=="")
                    {
                        alert("Select a venue!");
                        return false;
                    }
                    else return true;
             
                });
                 */                  
            </script>
    </body>
</html>

