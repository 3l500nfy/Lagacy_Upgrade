<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_GET['schid']))
    header("Location:" . siteUrl("403.php"));
$schid = $_GET['schid'];
$schedule_config = get_schedule_config_param_as_array($schid);
if (!is_test_administrator_of($schedule_config['testid']))
    header("Location:" . siteUrl("403.php"));


$scheduledcount = get_candidate_scheduled_count($schid);
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Displacement issues:</h2><br />
            <form class="style-frm" id="reschedule-frm"><input type="hidden" name="schid" id="schid" value="<?php echo $schedule_config['schedulingid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" <?php if (isset($_GET['safemode']) && $_GET['safemode'] != "") echo"checked"; ?>/> safe mode</label></div>
                <?php echo $scheduledcount . " candidate(s) will be displaced. "; ?>
                <input type="hidden" name="tid" id="tid" value="<?php echo $schedule_config['testid']; ?>" />

                <fieldset id="add-schd"><legend>Select an action:</legend>
                    <ul>
                        <li><a id="cancel-del" href="javascript:void(0);"> Cancel operation.</a></li>
                        <li><a id="ok-del" href="javascript:void(0);">Remove affected candidate(s) and delete schedule.</a></li>
                        <li><a id="change-sch" href="javascript:void(0);">Reschedule affected candidate(s) and delete schedule.</a></li>
                    </ul>

                    <br />

                    <?php
                    $schedules = get_schedule_ids_as_array($schedule_config['testid']);
                    $schedules[] = 0;
                    if (count($schedules) > 2) {
                        ?>
                        <div id="existing-schedule" style="display:none;">
                            <span style="text-decoration: underline; font-style: italic;">Select a schedule for <b><?php echo $scheduledcount; ?></b> candidate(s):</span>
                            <table class="style-tbl" style="margin-left: auto; margin-right: auto; min-width: 500px;"><tr><th>S/N</th><th>Date</th><th>Venue</th><th>Total Possible Batch</th><th>No. Per Batch</th><th>Start Time</th><th>End Time</th><th>Free Space</th><th>Action</th></tr>
                                <?php
                                $c = 1;
                                $c1 = 1;
                                foreach ($schedules as $schedule) {
                                    if ($schedule_config['schedulingid'] == $schedule)
                                        continue;
                                    $scheduledcount2 = get_candidate_scheduled_count($schedule);
                                    $schedulecapacity = get_schedule_capacity($schedule);
                                    $freeslot = $schedulecapacity;
                                    if ($schedulecapacity != -1) {
                                        $freeslot = $schedulecapacity - $scheduledcount2;
                                    }
                                    $query = "select * from tblscheduling where schedulingid=?";
                                    $stmt=$dbh->prepare($query);
                                    $stmt->execute(array($schedule));
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                    $tdate = new DateTime($row['date'] . " 00:00:00");
                                    $tstime = new DateTime("000-00-00 " . $row['dailystarttime']);
                                    $tetime = new DateTime("000-00-00 " . $row['dailyendtime']);

                                    //check if the date is passed
                                    $now = new DateTime();
                                    $schid_date = new DateTime($row['date'] . " " . $row['dailystarttime']);
                                    $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
                                    if ($intaval > 0 && isset($_GET['safemode']) && $_GET['safemode'] != "") {
                                        echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>" . (($schedulecapacity == -1) ? ("Unlimited") : ($freeslot)) . "</td><td>[date elapsed]</td></tr>";
                                        continue;
                                    }

                                    if ($freeslot < $scheduledcount) {
                                        echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>" . (($schedulecapacity == -1) ? ("Unlimited") : ($freeslot)) . "</td><td>[insufficient space]</td></tr>";
                                        continue;
                                    }
                                    $c1++;
                                    echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>" . (($schedulecapacity == -1) ? ("Unlimited") : ($freeslot)) . "</td><td><input type='radio' name='schd-select' " . (($c1 == 2) ? ("checked") : ("")) . " class='schd-select' value='" . $row['schedulingid'] . "' /></td></tr>";
                                }
                                if ($c1 > 1)
                                    echo"<tr><td colspan='9' style='text-align:right;'><input type='submit' name='schd-commit' id='schd-commit' value='Reschedule' /></td></tr>";
                                ?>
                            </table>
                            <?php
                            if ($c == 1)
                                echo "<div style='max-width:400px; margin-left: auto; font-style:italic; margin-right: auto;'>Available schedules cannot assimilate displaced candidate(s).</div>";
                            ?>
                            <br />
                        </div>
                        <?php
                    } else {
                        ?>
                        <div id="existing-schedule" style="display:none;">
                            No schedule available.
                        </div>
                        <?php
                    }
                    ?>
                </fieldset>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());

            //alert($("#contentframe", top.document).height());            

            $(document).on('click', '#cancel-del', function(event) {
                window.location.replace("test_schedule.php?tid=<?php echo $schedule_config['testid']; ?>");
            });

            $(document).on('click', '#ok-del', function(event) {

                $.ajax({
                    type: 'POST',
                    url: 'test_schedule_delete.php?displace=1',
                    data: {schid: $("#schid").val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg) {
                    msg = ($.trim(msg) - 0);
                    if (msg == 0)//server error
                    {
                        alert("Server Error! Please try again.");
                    } else
                    if (msg == 1)//success
                    {
                        alert("Schedule was successfully removed and all candidate(s) displaced.");
                        window.location.replace("test_schedule.php?tid=<?php echo $schedule_config['testid']; ?>");
                    } else
                    if (msg == 2)//invalid permission
                    {
                        alert("Permission denied");
                    } else
                    if (msg == 4)// schedule data passed
                    {
                        alert("Test has already been taken for the selected schedule.");
                    } else
                    if (msg == 5)// schedule not selected
                    {
                        alert("No schedule selected.");
                    }
                    return;
                });

            });

            $(document).on('click', '#change-sch', function(event) {
                $("#existing-schedule").show('fade');
            });

            $(document).on('click', '#schd-commit', function(event) {

                $.ajax({
                    type: 'POST',
                    url: 'reschedule.php',
                    data: $("#reschedule-frm").serialize()
                }).done(function(msg) { //alert(msg); return;
                    msg = ($.trim(msg) - 0);

                    if (msg == 88)//server error
                    {
                        alert("Server Error! Please try again.kkkk");
                    } else
                    if (msg == 0)//server error
                    {
                        alert("Server Error! Please try again.");
                    } else
                    if (msg == 1)// success
                    {
                        alert("Rescheduling was successful!");
                        window.location.replace("test_schedule.php?tid=<?php echo $schedule_config['testid']; ?>");
                    } else
                    if (msg == 2)//privilede
                    {
                        alert("Permission Denied.");
                    } else
                    if (msg == 3)//date exceeded
                    {
                        alert("Schedule date elapsed.");
                    } else
                    if (msg == 4)//insufficient space
                    {
                        alert("Insufficent space on the selected schedule.");
                        window.location.reload(true);
                    } else
                    if (msg == 5)//schedule not selected
                    {
                        alert("No schedule selection.");
                    }

                });
                return false;
            });

            $(document).on('click','#safemode',function(event){
                refresh_schedule_list();
            });

            function refresh_schedule_list()
            {
                $.ajax({
                    type: 'GET',
                    url: 'getters/refresh_schedule_list.php',
                    data: {testid: $("#tid").val(), safemode:(($("#safemode").prop("checked")==true)?(1):("")) }
                }).done(function(msg) {
                });
            }
        </script>
    </body>
</html>