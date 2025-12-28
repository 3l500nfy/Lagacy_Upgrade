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
    header("Location:../home.php");
$tid = clean($_GET['tid']);

if (!is_test_administrator_of($tid))
    header("Location:" . siteUrl("403.php"));

if (!isset($_GET['tv']))
    header("Location:" . siteUrl("403.php"));
$tv = clean($_GET['tv']);

if (!isset($_GET['av']))
    header("Location:" . siteUrl("403.php"));
$av = clean($_GET['av']);

if (date_exceeded($tid, 0) && isset($_GET['safemode']) && $_GET['safemode'] != "") {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test date exceeded!</div>";
    exit();
}
$test_config = get_test_config_param_as_array($tid);
$test_version = $test_config['versions'];
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
            <form class="style-frm" id="vcontrol-frm"><input type="hidden" name="versions" id="versions" value="<?php echo $tv; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" <?php if(isset($_GET['safemode']) && $_GET['safemode']!="") echo"checked"; ?>/> safe mode</label></div>
                Questions under version 
                <?php
                for ($i = $tv + 1; $i <= $test_config['versions']; $i++) {
                    if ($i == $tv + 1)
                        echo $i;
                    else
                    if ($i == $test_config['versions'])
                        echo " and $i";
                    else
                        echo ", $i";
                }
                ?> 
                will be removed from this test
                <input type="hidden" name="active-v" id="active-v" value="<?php echo $av; ?>" />
                <input type="hidden" name="tid" id="tid" value="<?php echo $tid; ?>" />

                <fieldset id="v-action"><legend>Select an action:</legend>
                    <ul>
                        <li><a id="cancel-del" href="javascript:void(0);"> Cancel operation.</a></li>
                        <li><a id="ok-del" href="javascript:void(0);">Remove affected questions and truncate test version.</a></li>
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

            //alert($("#contentframe", top.document).height());            

            $(document).on('click', '#cancel-del', function(event) {
                window.location.replace("test_version.php?tid=<?php echo $tid; ?>");
            });

            $(document).on('click', '#ok-del', function(event) {

                $.ajax({
                    type: 'POST',
                    url: 'test_version_modify.php?displace=1',
                    data: {tid: $("#tid").val(), versions: $("#versions").val(), activev: $("#active-v").val() + (($("#safemode").prop("checked") == true) ? ("&safemode=" + $("#safemode").val()) : (""))}
                }).done(function(msg) { //alert(msg);
                    msg = ($.trim(msg) - 0);
                    if (msg == 0)//Server error
                    {
                        alert("Server Error! Please try again.");
                    } else
                    if (msg == 1)//success
                    {
                        alert("Version was modified successfully.");
                        window.location = "test_version.php?tid=" + $("#tid").val();
                    } else
                    if (msg == 2)//Permission denied
                    {
                        alert("Access Denied!");
                    } else
                    if (msg == 4)//date passed
                    {
                        alert("Test date exceeded");
                    } else
                    if (msg == 5)//insufficient input
                    {
                        alert("No test selection.");
                    }
                    return;
                });

            });

        </script>
    </body>
</html>