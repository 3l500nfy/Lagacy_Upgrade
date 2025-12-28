<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_GET['tid']))
    header("Location:" . siteUrl("403.php"));
$testid = $_GET['tid'];

if (!is_test_administrator_of($testid))
    header("Location:" . siteUrl("403.php"));

$test_config = get_test_config_param_as_array($testid);
$unique = $test_config['session'] . " /" . $test_config['testname'] . " /" . $test_config['testtypename'] . " /" . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
            $ = window.top.$;
        </script>

    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Dates:</h2><br />
            <form class="style-frm" id="add-date-frm"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>
                <div id="msg"></div>
                <div class="style-div" id="dts-div" style="min-height: 120px;"><br />
                    <div style="padding-left: 10px; font-style: italic;"><a class="anchor" href="javascript:void(0);" id="add-date">[Add more date...]</a></div><div id="dt-dialog"></div><br />
                    <?php
                    $testdate = get_test_dates_as_array($test_config['testid']);
                    foreach ($testdate as $td) {
                        echo"<div class='dts'><span class='dtvl'>$td</span><div class='dt-action dt-action1 ui-icon ui-icon-pencil'></div><div class='dt-action dt-action2 ui-icon ui-icon-closethick'></div></div>";
                    }
                    ?>
                </div>
            </form>
        </div>

        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
        <script type="text/javascript">
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

            $(document).on('mouseover', ".dts", function(event) {
                $(".dt-action", $(this)).show().css('display', 'inline-block');

            });

            $(document).on('mouseout', ".dts", function(event) {
                $(".dt-action", $(this)).hide();

            });

            $(document).on('click', '#add-date', function(event) {
                var y = $(this).offset().top + 200;//not used
                var x = $(this).offset().left + 400;//not used
                $("#dt-dialog", document).datepicker("dialog", new Date(), function(dateText, ins) {
                    add_date(dateText);
                }, {showButtonPanel: true, showOtherMonths: true, changeMonth: true, changeYear: true, yearRange: 'c-0:c+4', dateFormat: 'yy-mm-dd', minDate: new Date()},
                [10, 10]);
            });

            $(document).on('click', '.dt-action1', function(event) {
                var y = $(this).offset().top;
                var x = $(this).offset().left;
                $("#dt-dialog").datepicker("dialog", new Date(), function(dateText, ins) {
                    modify_date(dateText);
                }, {showButtonPanel: true, showOtherMonths: true, changeMonth: true, changeYear: true, yearRange: 'c-0:c+4', dateFormat: 'yy-mm-dd', minDate: new Date()},
                [x, y]);
            });

            $(document).on('click', '.dt-action2', function(event) {
                $("<div id='info-dialog'>Processing...</div>").dialog({modal: true, resizable: false, closeOnEscape: false});

                var dis = $(this);
                var dt = $(".dtvl", dis.parent()).text();
                var st = remove_date(dt, false);
                //alert(st);
                if (st == 1)
                {

                    dis.parent().remove();
                }
                $("#info-dialog").dialog('close').remove();
                //alert("enesi")
            });

            function add_date(dt)
            {
                //alert($("#safemode").prop("checked"))
                $.ajax({
                    type: 'POST',
                    url: 'add_date.php',
                    data: {dt: dt, tid: $("#tid", document).val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg) { //alert(msg);
                    if (($.trim(msg)) - 0 == 0)
                    {
                        alert("Operation was not successfull. Please try again.");
                    }
                    else
                    if (($.trim(msg)) - 0 == 1)
                    {
                        $("#dts-div", document).append("<div class='dts'><span class='dtvl'>" + dt + "</span><div class='dt-action dt-action1  ui-icon ui-icon-pencil'></div><div class='dt-action dt-action2 ui-icon ui-icon-closethick'></div></div>");
                    }
                    else
                    if (($.trim(msg)) - 0 == -1)
                    {
                        alert("Test has already been taken.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -2)
                    {
                        alert("Insufficient privilege to perform this operation.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -4)
                    {
                        alert("Test date already registered.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -3)
                    {
                        alert("Insufficient parameter(s) supplied.");
                    }
                });
            }

            function modify_date(dt)
            {
                $.ajax({
                    type: 'POST',
                    url: 'add_date.php',
                    data: {dt: dt, tid: $("#tid", document).val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg) { //alert(msg);
                    if (($.trim(msg)) - 0 == 0)
                    {
                        alert("Operation was not successfull. Please try again.");
                    }
                    else
                    if (($.trim(msg)) - 0 == 1)
                    {
                        $("#dts-div", document).append("<div class='dts'><span>" + dt + " </span><div class='dt-action dt-action1  ui-icon ui-icon-pencil'></div><div class='dt-action dt-action2 ui-icon ui-icon-closethick'></div></div>");
                    }
                    else
                    if (($.trim(msg)) - 0 == -1)
                    {
                        alert("Test has already been taken.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -2)
                    {
                        alert("Insufficient privilege to perform this operation.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -4)
                    {
                        alert("Test date already registered.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -3)
                    {
                        alert("Insufficient parameter(s) supplied.");
                    }
                });
            }


            function remove_date(dt, displace)
            {
                if (displace == true)
                    var addr = 'remove_date.php?displace=1'
                else
                    var addr = 'remove_date.php';

                var result = 0;

                $.ajax({
                    type: 'POST',
                    url: addr,
                    async: false,
                    data: {dt: dt, tid: $("#tid", document).val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg) { //alert(msg);
                    result = ($.trim(msg)) - 0;
                    if (($.trim(msg)) - 0 == 0)
                    {
                        alert("Operation was not successfull. Please try again.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -1)
                    {
                        alert("Test has already been taken.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -2)
                    {
                        alert("Insufficient privilege to perform this operation.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -4)
                    {
                        alert("Test date not registered.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -3)
                    {
                        alert("Insufficient parameter(s) supplied.");
                    }
                    else
                    if (($.trim(msg)) - 0 == -5)
                    {
                        if (window.confirm("Student are already scheduled for this date. \n Do you want to displace them?"))
                        {
                            result = remove_date(dt, true);
                        }

                    }
                });

                return result;
            }
        </script>
    </body>
</html>