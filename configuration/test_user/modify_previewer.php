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
    echo "Incomplete parameter list.";
    exit();
}
if (!isset($_GET['pno'])) {
    echo "Incomplete parameter list.";
    exit();
}

$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid))
{
    echo"Access denied.";
    exit();
}

$pno = clean($_GET['pno']);
$test_config = get_test_config_param_as_array($testid);

$test_subjects = get_test_subjects_as_array($testid);
if (count($test_subjects) == 0) {
    echo "No subject registered for this test yet";
    exit();
}

$question_previewer= get_staff_userid($pno);
//echo $question_previewer; exit();
if($question_previewer==0)
{
    echo"Not a registered user of CBT";
    exit();
}

if($question_previewer== $_SESSION['MEMBER_USERID'])
{
   echo"All test administrators are automatically question previewers of their test subjects.";
   exit();
}
$biodata = get_staff_biodata($pno);
if(count($biodata)==0)
    {
   echo"Specified user is not a staff of this institution.";
   exit();
}

$subject_previewed = get_previewer_subject_as_array($testid, $question_previewer);
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Previewer(s)</h2><br />
            [<a class="anchor"href="manage_compositor.php?tid=<?php echo $testid; ?>">Manage Compositors</a>] | [<a class="anchor" href="manage_proctor.php?tid=<?php echo $testid; ?>">Manage Invigilators</a>] | [<a class="anchor" href="manage_previewer.php?tid=<?php echo $testid; ?>">Manage Previewers</a>]
            <form class="style-frm" id="previewer-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <fieldset><legend>Modify Previewer</legend>
                    <input type="hidden" name="pno" id="pno" value="<?php echo $pno; ?>" /> 
                    <div id="staff-detail">
                        <table>
                            <tr><td>Full Name:</td><td><?php echo ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames'])); ?></td></tr>
                            <tr><td>P. No.:</td><td><?php echo strtoupper(str_replace(".", "", $pno)); ?><input type="hidden" name="uid" value="<?php echo $question_previewer; ?>"</td></tr>
                            <tr><td>Department: </td><td><?php echo ucwords(strtolower($biodata['departmentname'])); ?></td></tr>
                            <tr><td>Gender: </td><td><?php echo ucfirst($biodata['gender']); ?></td></tr>
                            <tr><td style="vertical-align: top;">Test subject(s): </td><td><table class="style-tbl"><?php
foreach ($test_subjects as $testsubject) {
    echo "<tr ><td><label><input type='checkbox' name='tsbj[]' class='tsbj' value='$testsubject' " . ((in_array($testsubject, $subject_previewed)) ? ("checked") : ("")) . " /> " . strtoupper(get_subject_code($testsubject)) . "-" . ucwords(strtolower(get_subject_name($testsubject))) . "</label></td></tr>";
}
?></table>
                                    <input type="submit" id="submit" name="submit" value="Submit"/><span id="submit-info"></span></td></tr>
                        </table>                    </div>
                </fieldset>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());
            
            $(document).on('click','#submit',function(event){ 
                $("#submit-info").html("processing... ");
                //alert($("#pno").val());
                $.ajax({
                    type:'POST',
                    url:'register_previewer.php',
                    data:$("#previewer-frm").serialize()
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    if(msg==0)
                    {
                        alert("Operation was not successful!");
                    }
                    if(msg==1)//success
                    {
                        alert("Previewer role was modified successfully!");
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

        </script>
    </body>
</html>