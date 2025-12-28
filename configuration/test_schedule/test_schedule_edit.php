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
    header("Location:".siteUrl("403.php"));
$schid = $_GET['schid'];
$testid = get_test_id_from_schedule($schid);

if (!is_test_administrator_of($testid))
    header("Location:".siteUrl("403.php"));

$test_config = get_test_config_param_as_array($testid);
$schd_config = get_schedule_config_param_as_array($schid);

$now = new DateTime();
$schid_date = new DateTime($schd_config['date'] . " " . $schd_config['dailystarttime']);
$intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());

if ($intaval >= 0 && isset($_GET['safemode']) && $_GET['safemode']!="") {
    echo 4;
    exit();
}

$unique = $test_config['session'] . " /" . $test_config['testname'] . " /" . $test_config['testtypename'] . " /" . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Modify Test Schedule:</h2> <br /><a href="test_schedule.php?tid=<?php echo $testid; ?>">Back to test schedules...</a><br /><br />
            
            <form class="style-frm" id="edit-schedule-frm"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" <?php if (isset($_GET['safemode']) && $_GET['safemode'] != "") echo"checked"; ?>/> safe mode</label></div>
                <div id="msg"></div>
                <input type="hidden" name="dur" id="dur" value="<?php echo $test_config['duration']; ?>" />
                <input type="hidden" name="schid" id="schid" value="<?php echo $schd_config['schedulingid']; ?>" />
                <b>Test Date: </b> <select id="tdate" name="tdate" >
                    <?php
                    $testdate = get_test_dates_as_array($test_config['testid']);
                    foreach ($testdate as $td) {
                        $td2 = new DateTime($td . " 00:00:00");
                        if ($td == $schd_config['date'])
                            echo"<option value='$td' selected >" . $td2->format("D, M d, Y") . "</option>";
                        else
                            echo"<option value='$td'>" . $td2->format("D, M d, Y") . "</option>";
                    }
                    ?>
                </select>
                <br /><br /><br />
                <div id="venuediv" >
                    <b>Center: </b> <select name="tcenter" id="tcenter"><option value="">--Select--</option><?php echo get_center_as_options($schd_config['centerid']); ?></select> <b>Venue: </b>  <select name="tvenue" id="tvenue"><option value="">--Select--</option><?php echo get_venue_as_options(array('centerid' => $schd_config['centerid'], 'venueid' => $schd_config['venueid'])); ?></select>
                </div>
                <div id="timediv">
                    <?php
                    $venuecapacity = get_venue_capacity($schd_config['venueid']);
                    echo"<br /><br />
    <div><table>
    <tr><td><b>No of batches for this schedule: </b> </td><td><input type='text' class='numeric-input' name='batchcount' id='batchcount' value='" . (($schd_config['maximumbatch'] == -1) ? ("") : ($schd_config['maximumbatch'])) . "' placeholder='Ignore if no limit' /><br /></td></tr>
    
    <tr><td><b>No of candidates per batch: </b> </td><td><input type='text' data-max='" . $venuecapacity . "' data-old='" . $schd_config['noperbatch'] . "' class='numeric-input' name='noperbatch' id='noperbatch' value='" . $schd_config['noperbatch'] . "' placeholder='Maximum of " . $venuecapacity . "' /><br /></td></tr>
    
    <tr><td><b>Daily Start Time: </b> </td><td><input type='text' name='dailystarttime' placeholder='hh:mm' id='dailystarttime' value='" . substr($schd_config['dailystarttime'], 0, (strlen($schd_config['dailystarttime']) - 3)) . "'/><br /></td></tr>
    
    <tr><td><b>Daily End Time: </b> </td><td><input type='text' placeholder='hh:mm' name='dailyendtime' id='dailyendtime'value='" . substr($schd_config['dailyendtime'], 0, (strlen($schd_config['dailyendtime']) - 3)) . "'/><br /></td></tr>
    
    </table>
    <div id='clashdiv'></div>
    </div>";
                    ?>
                </div>
                <input type="submit" name="save-btn" id="save-btn" value="Save Changes"/><br />
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script src="<?php echo siteUrl('assets/js/jquery-ui-timepicker-addon.js'); ?>"></script>

        <script type="text/javascript">
            $(document).scrollTop();
            $(window.top.document).scrollTop(0);//.scrollTop();
            // Destroy any existing timepicker instances first to prevent conflicts
            try {
                $("#dailystarttime, #dailyendtime", document).timepicker('destroy');
            } catch(e) {
                // Ignore if no instance exists
            }
            // Initialize timepicker - timeOnly prevents date parsing errors
            // Date is handled separately via #tdate dropdown
            $("#dailystarttime, #dailyendtime", document).timepicker({
                timeFormat:"HH:mm", 
                pickerTimeFormat:"hh:mm TT",
                timeOnly: true,
                showButtonPanel: false,
                stepMinute: 1
            });
            $("#contentframe", top.document).height($(document).height()); 
            //alert($("#contentframe", top.document).height());
            
            $(document).on('change',"#tdate",function(event){ //alert("changed");                
                $("#contentframe", top.document).height($(document).height());
                return;                 
            });
            
            $(document).on('change',"#tcenter",function(event){ //alert("changed");
                $("#tvenue", document).html("<option value=''>Loading...</option>");
                $("#save-btn", document).hide();
                $("#msg").removeClass('alert-success').html("");
                $.ajax({
                    type:'POST',
                    error:function(){alert("error");},
                    url:'../getters/get_venue.php',
                    data:{cid:$(this).val()}
                }).done(function(msg){ 
                    $("#tvenue", document).html(msg).trigger("change");
                    
                    $("#save-btn", document).show();
                    $("#contentframe", top.document).height($(document).height());
                });
               
                $("#contentframe", top.document).height($(document).height());
                return; 
                
            });
        
            $(document).on('change','#tvenue',function(event){
                $("#save-btn", document).hide();
                $("#msg").removeClass('alert-success').html("");
                {
                
                    $("#timediv",document).html("<i>Loading...</i>").show();
                    
                    $.ajax({
                        type:'POST',
                        url:'../getters/get_schedule_widget.php',
                        data:{tid:$("#tid",document).val(), vid:$("#tvenue", document).val(), tdt:$("#tdate",document).val(), schid:$("#schid",document).val()}
                    }).done(function(msg){ //alert(msg);
                        $("#timediv", document).html(msg);
                        // Destroy any existing timepicker instances first to prevent conflicts
                        try {
                            $("#dailystarttime, #dailyendtime", document).timepicker('destroy');
                        } catch(e) {
                            // Ignore if no instance exists
                        }
                        // Initialize timepicker - timeOnly prevents date parsing errors
                        // Date is handled separately via #tdate dropdown
                        $("#dailystarttime, #dailyendtime", document).timepicker({
                            timeFormat:"HH:mm", 
                            pickerTimeFormat:"hh:mm TT",
                            timeOnly: true,
                            showButtonPanel: false,
                            stepMinute: 1
                        });
                        $("#save-btn", document).show();
                        $("#contentframe", top.document).height($(document).height());
                    });
                }
                $("#contentframe", top.document).height($(document).height());
            });

            $(document).on('click','#save-btn',function(event){
                $("#msg").removeClass('alert-success').html("");
                var sdv=$.trim($('#dailystarttime', document).val());
                sdv=sdv.split(':');
                //var sdv1= new Date(0,0,0,sdv[0],sdv[1]);
                var sdv1= ((sdv[0]-0)*60) + (sdv[1]-0);
                var edv=$.trim($('#dailyendtime', document).val());
                edv=edv.split(':');
                //var edv1= new Date(0,0,0,edv[0],edv[1]);
                var edv1= ((edv[0]-0)*60) + (edv[1]-0);
                //alert(sdv1.toLocaleString());
                // alert(sdv1);
                //alert(edv1);
                if(sdv1>edv1)
                {
                    alert("Invalid date range");
                    return false;
                }
                
                if((edv1-sdv1)-($("#dur").val()-0)<60)
                {
                    alert("Time Range must be at least 1 hour greater than test duration ("+$("#dur").val()+" min)");
                    return false;
                }
                $("#clashdiv").removeClass('alert-error alert-success').html("<br /><i style='color:orange;'> <b>Please wait...</b> checking for possible issues</i><br /><br />"); 
                $("#msg").removeClass('alert-success');
                 
                modify_schedule(false);
                 
                $("#contentframe", top.document).height($(document).height());
                return false;
            });
            
            function modify_schedule(displace)
            {
                if(displace==true)
                    addr="modify_schedule.php?displace=1";
                else 
                    addr="modify_schedule.php";
                
                result="";
                result2=0;
                $("#save-btn").hide();
                $.ajax({
                    type:'POST',
                    url:addr,
                    data:$("#edit-schedule-frm").serialize()
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                     
                    $result="";
                    if(msg==0)
                        result="Server error! Please try again.";
                    else
                        if(msg==1)
                    {
                        $("#clashdiv").html("Schedule was modified successfully.").addClass("alert-success").removeClass("alert-error");                                
                        return;
                    }
                    else
                        if(msg==2)
                            result="Error! Invalid form input.";
                    else
                        if(msg==3)
                            result="Error! Invalid time range.";
                    else
                        if(msg==4)
                            result="Error! Time range is not sufficient for all the possible batches.";
                    else
                        if(msg==5)
                            result="Error! There is possible clash with other existing test(s) scheduled for "+$("#tdate").val();
                    else
                        if(msg==6)
                    {
                        result2=6;
                        if(window.confirm("New Schedule setting will displace some candidate.\n Click on \"Cancel\" or \"No\" if you dont want this to happen."))
                        {
                            modify_schedule(true);
                        }
                        else
                            $("#clashdiv").html("").removeClass("alert-success").removeClass("alert-error");
                        return;
                    }
                    else
                        if(msg==7)
                    {
                        result="No schedule selection!";
                    }
                    else
                        if(msg==8)
                    {
                        result="Permission Denied!";
                    }                    
                    
                    else
                        if(msg==9)
                    {
                        result="Date exceeded!";
                    }                    
                    
                    $("#clashdiv").html(result).addClass("alert-error");
                });
                
                $("#save-btn").show();
            }            
        </script>
    </body>
</html>