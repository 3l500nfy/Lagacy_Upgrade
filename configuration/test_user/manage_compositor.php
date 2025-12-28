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
if (!isset($_GET['tid'])) {
    echo "Incomplete parameter";
    exit();
}
$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid)) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
$test_config = get_test_config_param_as_array($testid);
$test_compositors = get_test_compositors_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Compositors</h2><br />
            [<a class="anchor"href="manage_compositor.php?tid=<?php echo $testid; ?>">Manage Compositors</a>] | [<a class="anchor" href="manage_proctor.php?tid=<?php echo $testid; ?>">Manage Invigilators</a>] | [<a class="anchor" href="manage_previewer.php?tid=<?php echo $testid; ?>">Manage Previewers</a>]
<?php
if (false && date_exceeded($testid)) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test date exceeded.</div>";
    exit();
}
if (count($test_subjects) == 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No subject registered for this test! <br /> Click <a href='../test_subject/test_subject.php?tid=$testid'>here</a> to register subjects.</div>";
    exit();
}
?>
            <form class="style-frm" id="test-compositor-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <br />
                <fieldset><legend>Add/Modify Compositor</legend>
                    <b>Staff P.No.: </b> <input type="text" name="pno" id="pno" /> <input type="submit" name="load" id="load" value="Generate" />
                    <div id="staff-detail">
                    </div>
                </fieldset><br />
                <fieldset id="test-compositor-list"><legend>Available test compositor(s)</legend>
                    <table class="style-tbl">
                        <tr><th>S/N</th><th>P.No.</th><th>FULL NAME</th><th>Department</th><th>Subject(s)</th><th>ACTION</th></tr>
<?php
$c = 1;
foreach ($test_compositors as $test_compositor) {
    $pno = get_staff_pno($test_compositor);
    $biodata = get_staff_biodata($pno);
    $fullname = ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames']));
    $subject_compose = get_compositor_subject_as_array($testid, $test_compositor);
    $subject_compose_str = "";
    foreach ($subject_compose as $sbjcomp) {
        $sbjnm = get_subject_code($sbjcomp);
        $subject_compose_str.=", " . strtoupper($sbjnm);
    }
    $subject_compose_str = trim($subject_compose_str, " ,");
    echo "<tr><td>" . $c++ . "</td><td>" . strtoupper($pno) . "</td><td>" . $fullname . "</td><td>" . ucwords(strtolower($biodata['departmentname'])) . "</td><td>$subject_compose_str</td><td>[<a href='modify_compositor.php?tid=" . $testid . "&pno=$pno'>Modify</a>][<a data-tid='$testid' data-uid='$test_compositor' data-fname='$fullname' class='remove_compositor' href='javascript:void(0)'>Remove</a>]</td></tr>";
}
if ($c == 1)
    echo"<tr><td colspan='6'>No test compositor available.</td></tr>";
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
            
            $(document).on('click','#submit',function(event){ 
                $("#submit-info").html("processing... ");
                $.ajax({
                    type:'POST',
                    url:'register_compositor.php',
                    data:$("#test-compositor-frm").serialize()
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
                        alert("No subject selected!");
                    }
                    $("#submit-info").html("");
                });
                return false;
            });

            $(document).on('click','.remove_compositor',function(event){ 

                var dis=$(this);
                var tid=dis.attr("data-tid");
                var uid=dis.attr("data-uid");
                var fname=dis.attr("data-fname");
                if(window.confirm("Are you sure you want to remove \""+fname+"\" from existing test compositors list."))
                {
                    $.ajax({
                        type:'POST',
                        url:'remove_compositor.php',
                        data:{tid:tid, uid:uid}
                    }).done(function(msg){ //alert(msg);
                        msg=($.trim(msg)-0);
                        if(msg==0)
                        {
                            alert("Operation was not successful!");
                        }
                        if(msg==1)//success
                        {
                            alert("User was successfully remove from list of compositors!");
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
                            alert("No subject selected!");
                        }
                        $("#submit-info").html("");
                    });
                }
                return false;
            });

        </script>
    </body>
</html>