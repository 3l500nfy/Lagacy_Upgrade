
<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
openConnection();
require_once '../lib/candid_scheduling_func.php';
$venueid=$_POST['venue'];
$tid=$_SESSION['tid'];
$matric=$_SESSION['matricnumber'];
$cid=  get_candidate_id($matric, 3);
$schd=  get_schedule_id($tid, $venueid);
$sbj=  get_subject_combination_as_array($tid);
$sbj=$sbj[0];

$batch= get_batch($schd);
if($batch==-1)
    $report="full";
else{
$query="INSERT INTO tblcandidatestudent (scheduleid, candidateid, subjectid) VALUES ($schd, $cid, $sbj)";
$stmt = $dbh->prepare($query);
$result = $stmt->execute();
$numrows = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($result)
{
    $report="success";
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
        </style>
        <?php require_once("../partials/cssimports.php") ?>
        <script type="text/javascript" src="../assets/js/jquery-1.7.2.min.js"></script>

    </head>
    <body>
        <div id="container" class="container">
            <div class="page-header">
                <img class="banner" src="<?php echo siteUrl('../assets/img/banner.png') ?>" alt="banner" >

            </div>
            <center><h2>VENUE SELECTION</h2></center>  
            <div>
                <?php
                if (!isset($report))
                    echo"Error in processing your action!";
                else {
                    if($report=="full")
                        echo"<div>The selected venue is filled up.</div>";
                    else
                        echo"<div>You have been successfully scheduled.<br />
                            Here are your schedule details:
                            </div>";
                }
                ?>

            </div>
            <script type ="text/javascript">
        $("#centre").live("change", function(){
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
                                 
            </script>
    </body>
</html>

