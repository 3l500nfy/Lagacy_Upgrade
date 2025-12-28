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

if (!isset($_GET['subjid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$sbjid = clean($_GET['subjid']);

if (!isset($_GET['tid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
$tid = clean($_GET['tid']);
if (!is_test_administrator_of($tid)) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_schedules = get_test_schedule_as_array($tid);
$test_schedules[] = 0;
$test_schedules_str = trim(implode(",", $test_schedules), ",");

$sql = "select distinct candidateid from tblcandidatestudent where subjectid = ? && scheduleid in (?)";
$stmt=$dbh->prepare($sql);
$stmt->execute(array($sbjid,$test_schedules_str));

$scheduledcount = $stmt->rowCount();

//$scheduledcount = 500;
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
            <form class="style-frm" id="reschedule-frm"><input type="hidden" name="sbjid" id="sbjid" value="<?php echo $sbjid; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" <?php if (isset($_GET['safemode']) && $_GET['safemode'] != "") echo"checked"; ?>/> safe mode</label></div>
                <?php echo $scheduledcount . " candidate(s) may be affected. "; ?>
                <input type="hidden" name="tid" id="tid" value="<?php echo $tid; ?>" />

                <fieldset id="sbj-action"><legend>Select an action:</legend>
                    <ul>
                        <li><a id="cancel-del" href="javascript:void(0);"> Cancel operation.</a></li>
                        <li><a id="ok-del" href="javascript:void(0);">Remove subject from test and ignore affected candidate(s).</a></li>
                        <li><a id="remove-sch" href="javascript:void(0);">Completely remove affected candidate(s) from this test schedule*.</a></li>
                    </ul>
                    <br />

                </fieldset>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());

            $(document).on('click', '#cancel-del', function(event) {
                window.location.replace("test_subject.php?tid=<?php echo $tid; ?>");
            });

            $(document).on('click', '#ok-del', function(event) {

                $.ajax({
                    type: 'POST',
                    url: 'test_subject_delete.php?displace=1'+sbjid,
                    data: {sbjid: $("#sbjid").val(), tid: $("#tid").val(), safemode:(($("#safemode").prop("checked")==true)?("1"):(""))}
                }).done(function(msg) {
                    msg = ($.trim(msg) - 0);
                    if (msg == 0)//server error
                    {
                        alert("Server Error! Please try again.");
                    } else
                    if (msg == 1)//success
                    {
                        alert("Subject was successfully removed");
                        window.location.replace("test_subject.php?tid=<?php echo $tid; ?>");
                    } else
                    if (msg == 2)//invalid permission
                    {
                        alert("Permission denied");
                    } else
                    if (msg == 4)// schedule data passed
                    {
                        alert("Test has already been taken.");
                    } else
                    if (msg == 5)// schedule not selected
                    {
                        alert("No schedule selection.");
                    }
                    return;
                });

            });

            $(document).on('click', '#remove-sch', function(event) {
                $.ajax({
                    type: 'POST',
                    url: 'test_subject_delete.php?displace=2'+sbjid+(($("#safemode").prop("checked")==true)?("&safemode=1"):("")),
                    data: {sbjid: $("#sbjid").val(), tid: $("#tid").val()}
                }).done(function(msg) {//alert(msg);
                    msg = ($.trim(msg) - 0);
                    if (msg == 0)//server error
                    {
                        alert("Server Error! Please try again.");
                    } else
                    if (msg == 1)//success
                    {
                        alert("Subject was successfully removed");
                        window.location.replace("test_subject.php?tid=<?php echo $tid; ?>");
                    } else
                    if (msg == 2)//invalid permission
                    {
                        alert("Permission denied");
                    } else
                    if (msg == 4)// schedule data passed
                    {
                        alert("Test has already been taken.");
                    } else
                    if (msg == 5)// schedule not selected
                    {
                        alert("No schedule selection.");
                    }
                    return;
                });

            });

            function refresh_schedule_list()
            {
                $.ajax({
                    type: 'GET',
                    url: 'getters/refresh_schedule_list.php',
                    data: {testid: $("#tid").val()}
                }).done(function(msg) {
                });
            }
        </script>
    </body>
</html>