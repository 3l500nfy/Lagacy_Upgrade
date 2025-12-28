<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("question_preview_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_GET['tid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
if (!isset($_GET['sbjid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
if (!isset($_GET['version'])) {
    $version=1;
}
else
    $version = clean($_GET['version']);

$testid = clean($_GET['tid']);
$sbjid = clean($_GET['sbjid']);

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, $_SESSION['MEMBER_USERID'], $sbjid) && !is_question_viewer_of($testid, $_SESSION['MEMBER_USERID'], $sbjid)) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);
//if (date_exceeded($testid)) {
//    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test date exceeded!</div>";
//    exit();
//}
if (!in_array($sbjid, $test_subjects)) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Selected subject is not registered for this test! <br /> Click <a href='../test_subject/test_subject.php?tid=$testid'>here</a> to register subjects.</div>";
    exit();
}

//$subject_questions = sections_as_array($testid, $sbjid);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl("assets/css/presentationstyle.css"); ?>" rel="stylesheet" type="text/css" ></link>

        <script type="text/javascript">
        </script>
        <style type="text/css">
            .anchor{color:#999999;}
            .anchor:hover{color:black;}
            .current{color:black; font-weight: bold;}
            @media print{
                #print-ctr{
                    display: none;
                }
            }
        </style>

    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div>
            <?php
            echo "<div class='presentation'>";
            get_presentation($testid, $sbjid, $version);
            echo"</div></div>";
            ?>
        </div>
        <div id="print-ctr"><a href="javascript:window.print();">Print</a></div>
    </body>
</html>