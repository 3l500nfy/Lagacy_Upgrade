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
    header("Location:" . siteUrl("admin.php"));
$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid))
    header("Location:" . siteUrl("403.php"));
//if(date_exceeded($testid, 0, "highest")){
//    echo"Test Date Exceeded!";
//    exit();
//}

$test_config = get_test_config_param_as_array($testid);
if (strtoupper(trim($test_config['testcodeid'])) == "1")
    $action = "single_candidate_upload_exec2.php";
else
    $action = "single_candidate_upload_exec.php";
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;"><?php if ($test_config['testcodeid'] == "1") {
    echo "Manual";
} else {
    echo "Single";
} ?> Candidate Registration:</h2><br />
            <form class="style-frm" id="upload-frm" action="<?php echo $action; ?>" target="contentframe" method="POST" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>
                <?php if (strtoupper(trim($test_config['testcodeid'])) == "1") { ?>
                    <fieldset id="upload-list"><legend>Registration Number:</legend>
                        <b>Jamb Reg.No.: </b> <input type="text" name="candidate-reg" id="candidate-reg" required/><br />
                        <div class="alert-notice">For more than one entry, separate each Reg. No. with a comma (,)</div>
                    </fieldset>
    <?php
} else {
    ?>
                    <fieldset id="upload-list"><legend>Registration Number:</legend>
                        <b>Registration No.: </b> <input type="text" name="candidate-reg" id="candidate-reg" required/><br />
                    </fieldset>
                            <?php
                        }
                        ?>
                <div id="schd">
                <fieldset id="sel-schd"><legend>Select Schedule:</legend>
                    <table class="style-tbl" style="margin-left: auto; margin-right: auto;"><tr><th>S/N</th><th>Date</th><th>Venue</th><th>Total Possible Batch</th><th>No. Per Batch</th><th>Space</th><th>Start Time</th><th>End Time</th><th>Selection </th></tr>
                        <?php
                        $c = 1;
                        $c2 = 1;
                        $schedules = get_test_schedule_as_array($testid);
                        foreach ($schedules as $schedule) {
                            $query = "select * from tblscheduling where schedulingid=?";
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
                            if ($intaval > 0) {
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

                    <input type="submit" name="save-btn" id="save-btn" value="Register candidate" /><br />
                </fieldset>
                </div>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

            //alert($("#contentframe", top.document).height());            
            $(document).on('change', "#checkall", function(event) { //alert("changed");
                if ($(this).prop("checked"))
                {
                    $(".schd").prop('checked', true);
                }
                else
                {
                    $(".schd").prop('checked', false);
                }
            });

            $(document).on('change', '.schd', function(event) {
                $("#checkall").prop('checked', false);
            });

            $(document).on('click','#safemode',function(event){
                refresh_schedule_list();
            });

            function refresh_schedule_list()
            {
                $.ajax({
                    type:'GET',
                    url:'../getters/refresh_upload_schedule_list.php',
                    data:{testid:$("#tid").val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg){
                    $("#schd").html(msg);
                    $("#contentframe", top.document).height($(document).height());
                });
            }

            $(document).on('click', '#save-btn', function(event) {
                if (trim($("#sheet").val()) == "")
                    $("#sheet").val(1);
            });

        </script>
    </body>
</html>