<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor"))&& !has_roles(array("Test Previewer")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Test Config";
$navindex = 2;
if (!isset($_GET['tid']))
    header("Location:home.php");
$testid = $_GET['tid'];

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid)&& !is_question_viewer_of($testid))
    header("Location:home.php");

$test_config = get_test_config_param_as_array($testid);
$unique = $test_config['session'] . " /" . $test_config['testname'] . " /" . $test_config['testtypename'] . " /" . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));

require_once '../partials/cbt_header.php';
?>
<link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
<br />
<div class="cooltitle">
    <?php echo $unique; ?>
</div>
<div id="container2">
    <table style="height: 100%;">
        <tr>
            <td id="left-nav">
                <div style="font-size: 14px;"><a class="anchor" href="home.php">&lt;&lt;Test Configurations</a></div>
                <?php if (is_test_administrator_of($testid)) { ?>
                    <div class="active"><a class="anchor" id="lk-basic" href="basic_config/basic_config.php?tid=<?php echo $testid; ?>" target="contentframe">Basic Configurations</a></div>
                    <div><a class="anchor" id="lk-version" href="test_version/test_version.php?tid=<?php echo $testid; ?>" target="contentframe">Test Versions</a></div>
                    <div><a class="anchor" id="lk-date" href="test_date/test_date.php?tid=<?php echo $testid; ?>" target="contentframe">Test Dates</a></div>
                    <div><a class="anchor" id="lk-schedule" href="test_schedule/test_schedule.php?tid=<?php echo $testid; ?>" target="contentframe">Test Schedules</a></div>
                    <?php if($test_config['testcodeid']==1){ ?> <div><a class="anchor" id="lk-mapping" href="test_mapping/test_mapping.php?tid=<?php echo $testid; ?>" target="contentframe">Test Mapping</a></div><?php } ?>
                    <?php if($test_config['testcodeid']==1){ ?><div><a class="anchor" id="lk-upload" href="test_candidate_upload/single_candidate_upload.php?tid=<?php echo $testid; ?>" target="contentframe">Manual Candidate Scheduling</a></div><?php } else { ?>
                    <div><a class="anchor" id="lk-upload" href="test_candidate_upload/upload_process.php?tid=<?php echo $testid; ?>" target="contentframe">Upload Student list</a></div><?php } ?>
                    <div><a class="anchor" id="lk-subject" href="test_subject/test_subject.php?tid=<?php echo $testid; ?>" target="contentframe">Test Subjects</a></div>
                    <?php
                }
                if (is_test_administrator_of($testid) || is_test_compositor_of($testid)) {
                    ?>
                    <div <?php if (!is_test_administrator_of($testid))
                    echo "class='active'"; ?>><a class="anchor" id="lk-compose" href="test_composition/test_composition.php?tid=<?php echo $testid; ?>" target="contentframe">Test Composition</a></div>
                <?php } 
                if (is_test_administrator_of($testid) || is_test_compositor_of($testid) || is_question_viewer_of($testid)) { ?>
                    <div><a class="anchor" id="lk-preview" href="quest_viewer/quest_viewer.php?tid=<?php echo $testid; ?>" target="contentframe">Preview Test Questions</a></div>
                <?php } 
                if (is_test_administrator_of($testid)) { ?>
                    <div><a class="anchor" id="lk-user" href="test_user/test_user.php?tid=<?php echo $testid; ?>" target="contentframe">Manage Users</a></div>
                <?php } ?>
            </td>
            <td id="frametd">
                <?php
                                if (is_test_administrator_of($testid))
                    $link = "basic_config/basic_config.php?tid=$testid";
                else
                if (is_test_compositor_of($testid))
                    $link = "test_composition/test_composition.php?tid=$testid";
                else
                if (is_question_viewer_of($testid))
                    $link = "quest_viewer/quest_viewer.php?tid=$testid";

                if(isset($_GET['framepg']) && clean($_GET['framepg'])!="")
                {
                    $lk=clean($_GET['framepg']);
                    if($lk=="basic")
                        $link="basic_config/basic_config.php?tid=$testid";
                    if($lk=="upload"){
                        if($test_config['testcodeid']==1){
                            $link="test_candidate_upload/single_candidate_upload.php?tid=$testid";
                        }else{
                        $link="test_candidate_upload/upload_process.php?tid=$testid";
                        }
                    }
                    if($lk=="venue")
                        $link="test_venue/test_venue.php?tid=$testid";
                    if($lk=="compose")
                        $link="test_composition/test_composition.php?tid=$testid";
                    if($lk=="user")
                        $link="test_user/test_user.php?tid=$testid";
                }
                ?>
                <iframe src="<?php echo $link; ?>" id="contentframe" name="contentframe" style="width:100%; display:block; min-height:500px; border-style: none; border-width: 0px;"></iframe>
            </td>
        </tr>
    </table>
</div>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    <?php
        if($lk !=""){
             echo"$('.active').removeClass('active');";
             echo"$('#lk-$lk').parent().addClass('active');";
        }
    ?>
    $(document).on('click','#left-nav .anchor',function(event){
        $(".active").removeClass("active");
        $(this).parent().addClass("active");
    });

</script>

</body>
</html>