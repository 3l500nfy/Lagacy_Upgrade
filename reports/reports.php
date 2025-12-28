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
if (!has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")) && !has_roles(array("Admin"))&& !has_roles(array("Super Admin")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Test Reports";
$navindex = 5;
if (!isset($_GET['tid']))
    header("Location:home.php");
$testid = $_GET['tid'];

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid) && !has_roles(array("Admin"))&& !has_roles(array("Super Admin")))
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
                <div style="font-size: 14px;"><a class="anchor" href="home.php">&lt;&lt;Test Reports</a></div>
                    <div><a class="anchor" href="view_report_summary.php?tid=<?php echo $testid; ?>" target="contentframe">Report Summary</a></div>
                    <div><a class="anchor" href="view_question_summary.php?tid=<?php echo $testid; ?>" target="contentframe">Question Summary</a></div>
                    <div><a class="anchor" href="view_presentation_summary.php?tid=<?php echo $testid; ?>" target="contentframe">Presentation Summary</a></div>
            </td>
            <td id="frametd">
                <iframe src="<?php echo siteUrl("blank.php"); ?>" id="contentframe" name="contentframe" style="width:100%; display:block; min-height:500px; border-style: none; border-width: 0px;"></iframe>


            </td>
        </tr>
    </table>
</div>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    $(document).on('click','#left-nav .anchor',function(event){
        $(".active").removeClass("active");
        $(this).parent().addClass("active");
    });
</script>

</body>
</html>
