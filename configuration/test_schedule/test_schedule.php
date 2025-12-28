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
$testid = $_GET['tid'];

if (!is_test_administrator_of($testid))
    header("Location:".siteUrl("403.php"));

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
        <link href="<?php echo siteUrl('assets/css/jquery-ui-timepicker-addon.css'); ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
        </script>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Schedule(s):</h2><br />
            <form class="style-frm" id="add-schedule-frm"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>
                <input type="hidden" name="dur" id="dur" value="<?php echo $test_config['duration']; ?>" />
                <fieldset id="add-schd"><legend>New Schedule</legend>
                    <b>Test Date: </b> <select id="tdate" name="tdate" ><option value=''>--Select--</option>
                        <?php
                        $testdate = get_test_dates_as_array($test_config['testid']);
                        foreach ($testdate as $td) {
                            $td2 = new DateTime($td . " 00:00:00");
                            echo"<option value='$td'>" . $td2->format("D, M d, Y") . "</option>";
                        }
                        ?>
                    </select> 

                    <span id="venuediv" style="display:none;">
                        <b>Center: </b> <select name="tcenter" id="tcenter"><option value="">--Select--</option><?php echo get_center_as_options(); ?></select> <b>Venue: </b>  <select name="tvenue" id="tvenue"><option value="">--Select--</option><?php ?></select>
                    </span>
                    <div id="timediv" style="display:none;">

                    </div>
                    <input type="submit" name="save-btn" id="save-btn" value="Save Changes" style="display:none;" /><br />
                </fieldset>

                <?php
                $schedules = get_schedule_ids_as_array($test_config['testid']);
                if (count($schedules) > 0) {
                    ?>
                    <br />

                    <fieldset id="schd-list" ><legend>Existing Schedules</legend>

                        <div id="existing-schedule">
                            <table class="style-tbl" style="margin-left: auto; margin-right: auto;"><tr><th>S/N</th><th>Date</th><th>Venue</th><th>Total Possible Batch</th><th>No. Per Batch</th><th>Start Time</th><th>End Time</th><th>Action</th></tr>
                                <?php
                                $c = 1;
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
                                    $schid_date = new DateTime($row['date'] . " " . $row['dailystarttime']);
                                    $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
                                    if ($intaval > 0) {
                                        echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>[date elapsed]</td></tr>";
                                    }else
                                        echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>[<a href='test_schedule_edit.php?schid=" . $row['schedulingid'] . "&safemode=1'>edit</a>] [<a class='del-schd' data-schd='" . $row['schedulingid'] . "' href='javascript:void(0);'>remove</a>]</td></tr>";
                                }
                                ?>
                            </table>
                        </div><br /></fieldset>
                    <?php
                }
                ?>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script src="<?php echo siteUrl('assets/js/jquery-ui-timepicker-addon.js'); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
                $("#contentframe", top.document).height(0).height($(document).height());

            $(document).on('change',"#tdate",function(event){ //alert("changed");
                $("#msg").removeClass('alert-success').html("");
                if($.trim($(this).val())=="")
                {
                    $("#tvenue", document).html("<option value=''>--Select--</option>");
           
                    $("#tcenter, #tvenue", document).val("");
                    
                    $("#timediv, #venuediv",document).hide();
                }
                else
                    $("#timediv, #venuediv",document).show();
             
                $("#contentframe", top.document).height($(document).height());
                return;                 
            });
            
            
            $(document).on('change',"#tcenter",function(event){ //alert("changed");
                $("#tvenue", document).html("<option value=''>Loading...</option>");
                $("#msg").removeClass('alert-success').html("");
                $.ajax({
                    type:'POST',
                    error:function(){alert("error");},
                    url:'../getters/get_venue.php',
                    data:{cid:$(this).val()}
                }).done(function(msg){ 
                    $("#tvenue", document).html("<option value=''>--Select--</option>"+msg);
                    $("#contentframe", top.document).height($(document).height());
                });
                
                $("#timediv",document).html("");
                $("#save-btn",document).hide();
                $("#contentframe", top.document).height($(document).height());
                return; 
                
            });
        
            $(document).on('change','#tvenue',function(event){
                $("#save-btn", document).hide();
                $("#msg").removeClass('alert-success').html("");
                if($(this).val()=="")
                {
                    
                    $("#timediv",document).html("").hide();
                }
                else
                {
                
                    $("#timediv",document).html("<i>Loading...</i>").show();
                    
                    $.ajax({
                        type:'POST',
                        url:'../getters/get_schedule_widget.php',
                        data:{tid:$("#tid",document).val(), vid:$("#tvenue", document).val(), tdt:$("#tdate",document).val()}
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
                            showButtonPanel: true,
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

                var sdv1= ((sdv[0]-0)*60) + (sdv[1]-0);
                var edv=$.trim($('#dailyendtime', document).val());
                edv=edv.split(':');
                var edv1= ((edv[0]-0)*60) + (edv[1]-0);
                if(sdv1>edv1)
                {
                    alert("Invalid date range");
                    return false;
                }

                $("#msg").removeClass('alert-success');
                $("#clashdiv").removeClass('alert-error').html("<br /><i style='color:orange;'> <b>Please wait...</b> checking for possible issues</i><br /><br />");
                 
                // Debug: Log what's being sent
                var formData = $("#add-schedule-frm").serialize();
                console.log("Form data being sent:", formData);
                console.log("Start time value:", $('#dailystarttime', document).val());
                console.log("End time value:", $('#dailyendtime', document).val());
                
                $.ajax({
                    type:'POST',
                    url:'add_schedule.php',
                    data:formData
                }).done(function(msg){ //alert(msg);
                    console.log("Server response:", msg);
                    msg=($.trim(msg)-0);
                    result="";
                     
                    if(msg==0)
                        result="Server error! Please try again.";
                    else
                        if(msg==1)
                    {
                        $("#msg").html("Schedule was created successfully.").addClass("alert-success");
                        $("#clashdiv").html("").removeClass("alert-error");
                        $(document).scrollTop();
                        refresh_schedule_list();
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
                            result="Test date/date selected exceeded.";
                    else
                        if(msg==7)
                            result="Access denied.";
                    
                    $("#clashdiv").html(result).addClass("alert-error");
                });
                $("#contentframe", top.document).height($(document).height());
                return false;
            });
            
            $(document).on('click','.del-schd', function(event){ //alert("");
                if(!window.confirm("Are you sure you want to delete this schedule?"))
                {
                    return;
                }
                else
                {
                    var schid=$(this).attr("data-schd");
                   
                    $.ajax({
                        type:'POST',
                        url:'test_schedule_delete.php',
                        data:{schid:schid, safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                    }).done(function(msg){ //alert(msg);
                        msg=($.trim(msg)-0);
                             
                        if(msg==0)//server error
                        {
                            alert("Server Error! Please try again.");
                        }else
                            if(msg==1)//success
                        {
                            alert("Schedule was successfully removed.");
                            refresh_schedule_list();
                        }else
                            if(msg==2)//invalid permission
                        {
                            alert("Permission denied");
                        }else
                            if(msg==3)//possible displacement
                        {
                            window.location="resolve_schedule_displacement.php?schid="+schid+ (($("#safemode").prop("checked")==true)?("&safemode=" + $("#safemode").val()):(""));
                            
                        }else
                            if(msg==4)// schedule date passed
                        {
                            alert("Test has already been taken for the selected schedule.");
                        }else
                            if(msg==5)// schedule not selected
                        {
                            alert("No schedule selection.");
                        }
                        return;
                                 
                    });
                }
            });
            
            $(document).on('click','#safemode',function(event){
                refresh_schedule_list();
            });
            function refresh_schedule_list()
            {
                $.ajax({
                    type:'GET',
                    url:'../getters/refresh_schedule_list.php',
                    data:{testid:$("#tid").val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg){
                    $("#add-schd").nextAll().remove();
                    $("#add-schedule-frm").append(msg);
                    $("#contentframe", top.document).height($(document).height());
                });
            }
        </script>
    </body>
</html>