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
if (!isset($_GET['tid']))
    header("Location:".siteUrl("403.php"));
$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid))
    header("Location:".siteUrl("403.php"));

$test_config = get_test_config_param_as_array($testid);
if (strtoupper(trim($test_config['testcodeid'])) == "1") {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Candidate list upload not supported on Post-UTME Test!</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/jquery-ui-timepicker-addon.css'); ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
        </script>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Upload Candidate List (excel format):</h2><br />
            <form class="style-frm" id="upload-frm" action="upload_exec.php" target="_blank" method="POST" enctype="multipart/form-data"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />

                <fieldset id="upload-list"><legend>Browse file:</legend>
                    <div class="alert-notice">
                        <b style="color:red;">NOTE:</b> You can upload from Excel workbook 97-2003(.xls) or 2007-2013(.xlsx). click <a  style="color:orange; font-style: italic;" target="_blank" href="<?php echo siteUrl("assets/file/student_list_template.xlsx"); ?>">here</a> to download template
                    </div><br />
                    <b>Browse File: </b> <input type="file" name="candidate-list" id="candidate-list" /><br />
                    <b>Sheet No.:</b><input type="text" class="numeric-input" data-old="1" data-min="1" id="sheet" name="sheet" value="1"  /> &nbsp; <b>Column:</b><select id="column" name="column"><?php
for ($i = 65; $i <= 90; ++$i)
    echo "<option value='" . chr($i) . "'>" . chr($i) . "</option>";
?></select><br />

                </fieldset>
                <fieldset><legend>Select Schedule:</legend>
                    <table class="style-tbl" style="margin-left: auto; margin-right: auto;"><tr><th>S/N</th><th>Date</th><th>Venue</th><th>Total Possible Batch</th><th>No. Per Batch</th><th>Space</th><th>Start Time</th><th>End Time</th><th>Selection </th></tr>
                        <?php
                        $c = 1;
                        $c2 = 1;
                        $schedules = get_test_schedule_as_array($testid);
                        foreach ($schedules as $schedule) {
                            $query = "select * from tblscheduling where schedulingid='$schedule'";
                            $stmt=$dbh->prepare($query);
                            $stmt->execute(array($schedule));
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            $tdate = new DateTime($row['date'] . " 00:00:00");
                            $tstime = new DateTime("000-00-00 " . $row['dailystarttime']);
                            $tetime = new DateTime("000-00-00 " . $row['dailyendtime']);

                            //check to see if date is passed
                            $now = new DateTime();
                            $schid_date = new DateTime($row['date'] . " " . $row['dailyendtime']);
                            $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
                            $freespace = get_schedule_freeslot($schedule);
                            if ($intaval >= -(60 * 5)) { // at least five minutes to end time
                                echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . (($freespace == -1) ? ("Unlimited") : ($freespace)) . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>[date elapsed]</td></tr>";
                            } else {
                                echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . (($freespace == -1) ? ("Unlimited") : ($freespace)) . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td><input type='radio' name='schd[]' class='schd' value='" . $schedule . "' " . (($c2 == 1) ? ("checked") : ("")) . "/></td></tr>";
                                $c2++;
                            }
                        }
                        if ($c == 1)
                            echo "<tr><td colspan='9'>No schedule available yet.</td></tr>";
                        ?>
                    </table>

                    <input type="submit" name="save-btn" id="save-btn" value="Upload list" /><br />
                </fieldset>

            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
                $("#contentframe", top.document).height(0).height($(document).height());
            
            //alert($("#contentframe", top.document).height());            
            $(document).on('change',"#checkall",function(event){ //alert("changed");
                if($(this).prop("checked"))
                {
                    $(".schd").prop('checked',true);
                }
                else
                {
                    $(".schd").prop('checked',false);
                }
            });
            
            $(document).on('change','.schd',function(event){
                $("#checkall").prop('checked',false);
            });            

            $(document).on('click','#save-btn',function(event){
                if(trim($("#sheet").val())=="")
                    $("#sheet").val(1);
            });            

        </script>
    </body>
</html>