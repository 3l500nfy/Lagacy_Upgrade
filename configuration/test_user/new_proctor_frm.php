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
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Incomplete parameters.</div>";
    exit();
}
if (!isset($_GET['schdid'])) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Incomplete parameters.</div>";
    exit();
}

$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid))
{
    header("Location:" . siteUrl("403.php"));
    exit();
}

$schdid = clean($_GET['schdid']);
$test_config = get_test_config_param_as_array($testid);
$schedule_config = get_schedule_config_param_as_array($schdid);
$tdate = new DateTime($schedule_config['date'] . " 00:00:00");
$tstime = new DateTime("000-00-00 " . $schedule_config['dailystarttime']);
$tetime = new DateTime("000-00-00 " . $schedule_config['dailyendtime']);

//check to see if date is passed

$now = new DateTime();
$schid_date = new DateTime($schedule_config['date'] . " " . $schedule_config['dailystarttime']);
$intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
if (false && $intaval > 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test date exceeded.</div>";
    exit();
}
$test_proctors= get_test_invigilators_as_array($testid, $schdid);
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Register Invigilator</h2><br />
            <?php
            echo"<h2 class='cooltitle3' style='text-decoration:underline;'>" . $tdate->format("D, M d, Y") . " (" . $tstime->format("h:i a") . "-" . $tetime->format("h:i a") . ")/" . intelligentStr(get_venue_name($schedule_config['venueid']), 50) . "</h2>";
            ?>
            [<a class="anchor"href="manage_compositor.php?tid=<?php echo $testid; ?>">Manage Compositors</a>] | [<a class="anchor" href="manage_proctor.php?tid=<?php echo $testid; ?>">Manage Invigilators</a>]
            <br /><br />
            <form class="style-frm" id="test-proctor-frm" >
                <input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <input type="hidden" name="schdid" id="schdid" value="<?php echo $schdid; ?>" />
                <fieldset><legend>Add Invigilator</legend>
                    <b>Staff P.No.: </b> <input type="text" name="pno" id="pno" /> <input type="submit" name="load" id="load" value="Generate" />
                    <div id="staff-detail">
                    </div>
                </fieldset><br />
                <fieldset id="test-proctor-list"><legend>Available test Invigilator(s)</legend>
                    <table class="style-tbl">
                        <tr><th>S/N</th><th>P.No.</th><th>FULL NAME</th><th>Department</th><th>ACTION</th></tr>
                        <?php
                        $c = 1;
                            foreach($test_proctors as $proctor)
                            {
                                                            $pno = get_staff_pno($proctor);
                            $biodata = get_staff_biodata($pno);
                            $fullname=ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames'])) ;
                            echo "<tr><td>" . $c++ . "</td><td>" . strtoupper($pno) . "</td><td>" . $fullname. "</td><td>" . ucwords(strtolower($biodata['departmentname'])) . "</td><td>[<a data-tid='$testid' data-uid='$proctor' data-schdid='$schdid' data-fname='$fullname' class='remove_proctor' href='javascript:void(0)'>Remove</a>]</td></tr>";

                            }
                            if($c==1)
                                echo"<tr><td colspan='5'>No Ivnigilator available</td></tr>";
                        ?>
                    </table>
                </fieldset>
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
                    url:'get_proctor_detail.php',
                    data:{pno:$("#pno").val(), tid:$("#tid").val(), schdid:$("#schdid").val()}
                }).done(function(msg){
                    $("#staff-detail").html(msg);
                    $("#contentframe", top.document).height(0).height($(document).height());
                });
                return false;
            });
            
            $(document).on('click','#submit',function(event){ 
            if($.trim($("#end-key").val())=="" || ($("#end-key").val()).length >3)
                {
                    alert("Endorsement key must be at least one character in lenght and not more than three character in lenght.");
                    return false ;
                }
                $("#submit-info").html("processing... ");
                //alert($("#pno").val());
                $.ajax({
                    type:'POST',
                    url:'register_proctor.php',
                    data:$("#test-proctor-frm").serialize()
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    if(msg==0)
                    {
                        alert("Operation was not successful!");
                    }
                    if(msg==1)//success
                    {
                        alert("User was successfully registered!");
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
                return false;
            });

            $(document).on('click','.remove_proctor',function(event){ 
                var dis=$(this);
                var tid=dis.attr("data-tid");
                var uid=dis.attr("data-uid");
                var schdid=dis.attr("data-schdid");
                var fname=dis.attr("data-fname");
                if(window.confirm("Are you sure you want to remove \""+fname+"\" from existing test Invigilator(s)."))
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