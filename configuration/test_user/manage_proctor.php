<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_GET['tid'])) {
    echo"Incomplete parameter!";
    exit();
}
$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid)) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($testid);
$test_schedules = get_test_schedule_as_array($testid);

if (count($test_schedules) == 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No schedule available yet! <br /> Click <a href='../test_schedule/test_schedule.php?tid=$testid'>here</a> to add schedules.</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
        </script>
        <style type="text/css">
            .anchor{color:#999999;}
            .anchor:hover{color:black;}
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Invigilators</h2><br />
            [<a class="anchor"href="manage_compositor.php?tid=<?php echo $testid; ?>">Manage Compositors</a>] | [<a class="anchor" href="manage_proctor.php?tid=<?php echo $testid; ?>">Manage Invigilators</a>] | [<a class="anchor" href="manage_previewer.php?tid=<?php echo $testid; ?>">Manage Previewers</a>]
            <form class="style-frm" id="test-proctor-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <br />
<?php
 if (true || !date_exceeded($testid, -300, "highest")) {
foreach ($test_schedules as $schedule) {
    $schedule_config = get_schedule_config_param_as_array($schedule);
    $tdate = new DateTime($schedule_config['date'] . " 00:00:00");
    $tstime = new DateTime("000-00-00 " . $schedule_config['dailystarttime']);
    $tetime = new DateTime("000-00-00 " . $schedule_config['dailyendtime']);

    //check to see if date is passed

    $now = new DateTime();
    $schid_date = new DateTime($schedule_config['date'] . " " . $schedule_config['dailyendtime']);
    $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
    
    if ($intaval <= 0) {
   
        echo"<h2 class='cooltitle3' style='text-decoration:underline;'>" . $tdate->format("D, M d, Y") . " (" . $tstime->format("h:i a") . "-" . $tetime->format("h:i a") . ")/" . intelligentStr(get_venue_name($schedule_config['venueid']), 50) . "</h2>";
        $proctors = get_test_invigilators_as_array($testid, $schedule);
        if (count($proctors) == 0) {
            echo"No Invigilator registered yet. click <a href='new_proctor_frm.php?tid=$testid" . "&schdid=$schedule'>here</a> to add.";
        } else {
            echo"<table class='style-tbl'><tr><th>S/N</th><th>P.No.</th><th>FULL NAME</th><th>Department</th><th>ACTION</th></tr>";
            $c = 1;
            foreach ($proctors as $proctor) {
                $pno = get_staff_pno($proctor);
                $biodata = get_staff_biodata($pno);
                $fullname = ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames']));
                echo "<tr><td>" . $c++ . "</td><td>" . strtoupper($pno) . "</td><td>" . $fullname . "</td><td>" . ucwords(strtolower($biodata['departmentname'])) . "</td><td>[<a data-tid='$testid' data-uid='$proctor' data-schdid='$schedule' data-fname='$fullname' class='remove_proctor' href='javascript:void(0)'>Remove</a>]</td></tr>";
            }
            echo"<tr><td colspan='5'>Click <a href='new_proctor_frm.php?tid=$testid" . "&schdid=$schedule'>here</a> to add.</td></tr>";
            echo"</table>";
        }
        echo "<br /><br />";
    } else
        continue;
}
}else echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test date exceeded.</div>";
?>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());
            
            $(document).on('click','#load',function(event){
                $("#staff-detail").html("Loading... ");
                //alert($("#pno").val());
                if($.trim($("#pno").val())=="")
                {
                    $("#staff-detail").html("No record found.");
                    return false;
                }
                    
                $.ajax({
                    type:'POST',
                    url:'get_compositor_frm.php',
                    data:{pno:$("#pno").val(), tid:$("#tid").val()}
                }).done(function(msg){
                    $("#staff-detail").html(msg);
                    $("#contentframe", top.document).height(0).height($(document).height());
                });
                return false;
            });
            
            $(document).on('click','.remove_proctor',function(event){ 
                var dis=$(this);
                var tid=dis.attr("data-tid");
                var uid=dis.attr("data-uid");
                var schdid=dis.attr("data-schdid");
                var fname=dis.attr("data-fname");
                if(window.confirm("Are you sure you want to remove \""+fname+"\" from existing test invigilator(s)."))
                {
            
                    $.ajax({
                        type:'POST',
                        url:'remove_proctor.php',
                        data:{tid:tid, uid:uid, schdid:schdid}
                    }).done(function(msg){ //alert(msg);
                        msg=($.trim(msg)-0);
                        if(msg==0)
                        {
                            alert("Operation was not successful!");
                        }
                        if(msg==1)//success
                        {
                            alert("User was removed successfully!");
                            window.location.reload(true);
                        }
                        if(msg==2)// access denied
                        {
                            alert("Access denied");
                        }
                        if(msg==3)//date passed
                        {
                            alert("Test date exceeded");
                        }
                        if(msg==4)// invalid input
                        {
                            alert("Invalid input!");
                        }
                        if(msg==5)//
                        {
                            alert("No schedule selected!");
                        }
                        $("#submit-info").html("");
                    });
                }
                return false;
            });

        </script>
    </body>
</html>