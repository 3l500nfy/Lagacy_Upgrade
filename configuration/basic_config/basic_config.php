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

$test_config = get_test_config_param_as_array($testid);
$unique = $test_config['session'] . " /" . $test_config['testname'] . " /" . $test_config['testtypename'] . " /" . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
            $ = window.top.$;
            
            $(document).ready(function() {
                $("#contentframe").height($(document).height());
            });
        </script>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Basic Configurations:</h2><br />
            <div id="msg"></div>
            <form class="style-frm" id="basic-config-frm" action="#" method="Post" ><input type="hidden" name="tid" value="<?php echo $test_config['testid']; ?>" />
                <fieldset><legend>Duration/Starting Mode</legend>
                    <label><b>Test Duration (min): </b> <input type="text" class="numeric-input" name="duration" id="testduration" placeholder="Numeric" value="<?php echo trim($test_config['duration'] - 0); ?>"/></label><br />
                    <label><b>Time Padding (min): </b> <input type="text" class="numeric-input" name="tpad" id="tpad" placeholder="Numeric" value="<?php echo trim($test_config['time_padding'] - 0); ?>"/></label><br /><br />
                    <b>Starting Mode: </b> <label><input type="radio" name="startmode" value="on login" <?php
                        if ($test_config['startmode'] == 'on login')
                            echo "checked"
                            ?> />On login  </label> &nbsp; <label><input type="radio" name="startmode" value="on starttime" <?php
                                                             if ($test_config['startmode'] == 'on starttime')
                                                                 echo "checked"
                                                                 ?>  />On Start Time  </label><br />
                    <b>Invigilator's Endorsement: </b> &nbsp; <label><select name="signature" id="signature"><option value="yes">Required</option><option value="no" <?php if ($test_config['endorsement'] == 'no') echo "selected"; ?>>Not required</option></select></label><br />
                    <b>Test Availability: </b> &nbsp; <label><select name="availability" id="availability"><option value="1">Available</option><option value="0" <?php if ($test_config['status'] == '0') echo "selected"; ?>>Unavailable</option></select></label><br />
                    <b>Computer Registration Key: </b> &nbsp; <label><input placeholder="3 chars only" type="text" value="<?php echo trim($test_config['passkey']); ?>" id="pkey" name="pkey" /></label>
                </fieldset><br />
                <fieldset><legend>Test Pattern</legend>
                    <b>Question Display: </b> <label><input type="radio" name="qdisplay" value="All" <?php
                            if ($test_config['displaymode'] == 'All')
                                echo "checked"
                                ?>  />All at once </label> &nbsp; <label><input type="radio" name="qdisplay" value="single question" <?php
                        if ($test_config['displaymode'] == 'single question')
                            echo "checked"
                            ?> />Step by step  </label><br /><br />
                    <b>Question Administration: </b> <label><input type="radio" name="qadmin" value="linear" <?php
                        if ($test_config['questionadmin'] == 'linear')
                            echo "checked"
                            ?> />Uniform  </label> &nbsp; <label><input type="radio" name="qadmin" value="random" <?php
                            if ($test_config['questionadmin'] == 'random')
                                echo "checked"
                                ?> />Random  </label><br /><br />
                    <b>Option Administration: </b> <label><input type="radio" name="optadmin" value="linear" <?php
                            if ($test_config['optionadmin'] == 'linear')
                                echo "checked"
                                ?> />Uniform  </label> &nbsp; <label><input type="radio" name="optadmin" value="random" <?php
                            if ($test_config['optionadmin'] == 'random')
                                echo "checked"
                                ?>  />Random  </label><br /><br />
                </fieldset>
                <fieldset><legend>Calculator</legend>
                    <b>Allow Use of CBT Calculator: </b> <label><input type="radio" name="allowcalc" value="1" <?php
                            if ($test_config['allow_calc'] == '1')
                                echo "checked"
                                ?>  />Allow </label> &nbsp; <label><input type="radio" name="allowcalc" value="0" <?php
                        if ($test_config['allow_calc'] == '0')
                            echo "checked"
                            ?> />Disable Calculator</label><br /><br />
                </fieldset>
                <input type="submit" id="save_btn" value="Save Changes"/>
            </form>
        </div>


        <script type="text/javascript">
            $(document).scrollTop();
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());
            $('#save_btn', document).click(function(event) {
                $("#msg", document).removeClass("alert-error alert-success alert-notice").html("<i style='color:orange; padding:100px;'>Processing...</i>");
                if ($.trim($("#testduration", document).val()) == "" || ($.trim($("#testduration", document).val()) - 0) < 1)
                {
                    $("#msg", document).html("Please Specify Test Duration").addClass('alert-error');
                    $("#testduration", document).focus();
                    return false;
                }
                
                var pkey = $.trim($("#pkey",document).val());
                //alert(pkey);
                if (pkey.length != 3) {
                    $("#msg", document).html("Computer Registration Key must be 3 characters in length").addClass('alert-error');
                    $("#pkey", document).focus();
                    return false;
                }
                
                $.ajax({
                    type: 'POST',
                    url: 'basic_config/basic_config_exec.php',
                    error: function() {
                        alert("Server Error! Please try again");
                    },
                    data: $("#basic-config-frm", document).serialize()
                }).done(function(msg) {
                    
                    //alert(msg);
                    if (($.trim(msg) - 0) == 0)
                    {
                        $("#msg", document).html("Operation was unsuccessfull.").addClass("alert-error");
                    }
                    else
                    if (($.trim(msg) - 0) == 1)
                    {
                        $("#msg", document).html("Basic configurations was modified successfully.").addClass("alert-success");
                    }
                    else
                    if (($.trim(msg) - 0) == -1)
                    {
                        $("#msg", document).html("Invalid parameter(s) supplied.").addClass("alert-error");
                    }
                    else
                    if (($.trim(msg) - 0) == -2)
                    {
                        $("#msg", document).html("Test date already exceeded.").addClass("alert-error");
                    }
                    else
                    if (($.trim(msg) - 0) == -3)
                    {
                        $("#msg", document).html("Insufficient privilege.").addClass("alert-error");
                    }
                    else
                    if (($.trim(msg) - 0) == -4)
                    {
                        $("#msg", document).html("Database Error.").addClass("alert-error");
                    }
                    else
                    if (($.trim(msg) - 0) == -5)
                    {
                        $("#msg", document).html("Computer Registration Key must be 3 characters in length.").addClass("alert-error");
                    }
                    $("#contentframe", top.document).height(0).height($(document).height());
                    $(window.top.document).scrollTop(0);
                    $(document).scrollTop();
                });
                return false;
            });

        </script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
    </body>
</html>
