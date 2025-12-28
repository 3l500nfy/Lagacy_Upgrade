<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
require_once("../../lib/config_functions.php");
openConnection();
authorize();

if (!isset($_GET['tid']))
    header("Location:" . siteUrl("403.php"));

$testid = $_GET['tid'];

if (!is_test_administrator_of($testid)) {
    header("Location:" . siteUrl("configuration/home.php"));
    exit();
}

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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Versions:</h2><br />
            <form class="style-frm" id="tversions"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>
                <div id="msg"></div>
                <input type="hidden" name="oldversioncount" id="oldversioncount" value="<?php echo $test_config['versions']; ?>" />
                <b>Version count: </b> <input type="text" name="versions" id="versions" class="numeric-input" data-min="1" data-old="<?php echo $test_config['versions']; ?>" value="<?php echo $test_config['versions']; ?>"/><br /><br />
                <b>Active version: </b> 
                <?php
                $version_opt = "";
                for ($i = 1; $i <= $test_config['versions']; $i++) {
                    if (validateversion($testid, $i)) {
                        if ($i == 1)
                            $version_opt.='<select name="active-v" id="active-v">';
                        if ($test_config['activeversion'] == $i)
                            $version_opt.="<option value='$i' selected >$i</option>";
                        else
                            $version_opt.="<option value='$i'>$i</option>";
                    }
                }
                if ($version_opt != "") {
                    $version_opt.="</select>";
                    echo $version_opt;
                } else
                    echo"No version meet the minimum requirement.";
                ?>
                <br />
                <input name="submit" id="submit" type="submit" value="Save Changes"/>
            </form>
        </div>

        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());

            $(document).on("change", "#versions", function(event) {
                var vdiff = ($(this).val() - 0);
                var activev = ($("#active-v").val() - 0);
                var opts = "";
                for (var i = 1; i <= vdiff; i++)
                {
                    if (activev == i)
                        opts += "<option value='" + i + "' selected >" + i + "</option>";
                    else
                        opts += "<option value='" + i + "' >" + i + "</option>";
                }
                $("#active-v").html(opts);
            });

            $(document).on('click', '#submit', function(event) {
                $.ajax({
                    type: 'POST',
                    error: function() {
                        alert("error");
                    },
                    url: 'test_version_modify.php',
                    data: $("#tversions").serialize()
                }).done(function(msg) { //alert(msg);
                    msg = ($.trim(msg) - 0);

                    if (msg == 0)//Server error
                    {
                        alert("Server Error! Please try again.");
                    } else
                    if (msg == 1)//success
                    {
                        alert("Version was modified successfully.");
                    } else
                    if (msg == 2)//Permission denied
                    {
                        alert("Access Denied!");
                    } else
                    if (msg == 3)//issues
                    {
                        //alert("");
                        window.location = "resolve_version_control.php?tv=" + $("#versions").val() + "&av=" + $("#active-v").val() + "&tid=" + $("#tid").val()+ (($("#safemode").prop("checked")==true)?("&safemode=" + $("#safemode").val()):(""));
                    } else
                    if (msg == 4)//date passed
                    {
                        alert("Test date exceeded");
                    } else
                    if (msg == 5)//insufficient input
                    {
                        alert("Input error!");
                    } else
                    if (msg == 6)//insufficient input
                    {
                        alert("Test date exceeded. You can only change the active version.");
                    }
                });
                return false;
            });
        </script>
    </body>
</html>
