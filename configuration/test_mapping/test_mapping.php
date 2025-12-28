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
    header("Location:" . siteUrl("403.php"));
$testid = $_GET['tid'];

if (!is_test_administrator_of($testid))
    header("Location:" . siteUrl("403.php"));

$schedules = get_test_schedule_as_array($testid);

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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Schedule(s):</h2><br />
            <form class="style-frm" id="schedule-mapping-frm"><input type="hidden" name="tid" id="tid" value="<?php echo $testid; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>                
                <fieldset id="sel-schd"><legend>Mapping</legend>
                    <b>Test Schedule: </b> <select id="tschedule" name="tschedule" ><option value=''>--Select a schedule--</option>
                        <?php
                        foreach ($schedules as $schid) {
                            $schedule_config = get_schedule_config_param_as_array($schid);
                            $schedule_date = $schedule_config['date'];
                            $schedule_starttime = $schedule_config['dailystarttime'];
                            $schd_date = new DateTime($schedule_date . " " . $schedule_starttime);
                            $schd_date_formated = $schd_date->format("D, M d, Y h:i a");
                            $schedule_venue = $schedule_config['venuename'];
                            $schedule_center = $schedule_config['centername'];
                            $scheduleid = $schedule_config['schedulingid'];
                            echo "<option value='$scheduleid'>$schd_date_formated/$schedule_venue/$schedule_center</option>";
                        }
                        ?>
                    </select> 

                </fieldset>

                <fieldset>
                    <legend>Faculty Mappings</legend>
                    <div id="facs">

                    </div>
                </fieldset>

            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script src="<?php echo siteUrl('assets/js/jquery-ui-timepicker-addon.js'); ?>"></script>

        <script type="text/javascript">

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

            //alert($("#contentframe", top.document).height());            
            $(document).on('change', "#tschedule", function(event) { //alert("changed");
                var schd = $(this).val();
                $("#facs", document).html("Loading...");
                $.ajax({
                    type: 'POST',
                    error: function() {
                        alert("error");
                    },
                    url: '../getters/get_faculty_mappings.php',
                    data: {schd: schd}
                }).done(function(msg) {
                    $("#facs", document).html(msg);
                    $("#contentframe", top.document).height($(document).height());
                });

                $("#contentframe", top.document).height($(document).height());
                return;
            });


            $(document).on('click', '#map_fac', function(event) {
                $.ajax({
                    type: 'POST',
                    url: 'test_mapping_exec.php',
                    data: $("#schedule-mapping-frm", document).serialize()
                }).done(function(msg) { //alert(msg);
                    msg = ($.trim(msg) - 0);
                    result = "";

                    if (msg == 0)
                        result = "Server error! Please try again.";
                    else
                    if (msg == 1)
                        result = "Mapping was successfull!";
                    else
                    if (msg == 2)
                        result = "Error! Invalid form input.";
                    else
                    if (msg == 3)
                        result = "Candidates may be displaced. Consider modifying the schedule.";
                    else
                    if (msg == 6)
                        result = "Test date/schedule selected exceeded.";
                    else
                    if (msg == 7)
                        result = "Access denied.";
                    alert(result);
                });
                return false;
            });

        </script>
    </body>
</html>