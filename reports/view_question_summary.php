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

if (!isset($_GET['tid']))
{
    header("Location:".siteUrl("403.php"));
    exit();
}
$testid = clean($_GET['tid']);

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin")))
{
    header("Location:".siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);
if (count($test_subjects) == 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No subject registered on this test</div>";
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
        <link href="<?php echo siteUrl('assets/css/reportstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
        </script>
        <style>
            #slider-info{
                font-style:italic;
            }
            .current{font-weight: bold; color:black;}
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Question Response Summary</h2><br />
            [<a class="anchor"href="view_report_summary.php?tid=<?php echo $testid; ?>">Test Result Summary</a>] | [<a class="anchor current" href="view_question_summary.php?tid=<?php echo $testid; ?>">Question Response Statistics</a>] | [<a class="anchor" href="view_presentation_summary.php?tid=<?php echo $testid; ?>">Presentation Report</a>]
            <form class="style-frm" id="test-report-frm" method="post" target="_blank" action="show_question_summary_print.php"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div id="filter-panel">
                    <b style="line-height:36px; vertical-align: middle;">Test Subject(s): </b> <select name="tsbjs" id="tsbjs" style="width:300px;">
                        <?php
                        if (count($test_subjects) > 1)
                            echo'<option value="">All</option>';

                        foreach ($test_subjects as $test_subject) {
                            $sbj_code = get_subject_code($test_subject);
                            $sbj_name = get_subject_name($test_subject);
                            echo "<option value='$test_subject'>" . trim(strtoupper($sbj_code)) . "- " . $sbj_name . "</option>";
                        }
                        ?>
                    </select>
                    <br />
                    <b style="line-height:36px; vertical-align: middle;">Key Word: </b> <input type="text" name="keyword" id="keyword" placeholder="Type a phrase" size="50" />
                    <div style="margin-left:auto; margin-right:0px; width:100px;"><input type="submit" id="load-report" value=" Load "/></div>
                </div>
                <hr style="border-color:green; border-style: solid; border-width:1px;"/>
            </form>
            <div id="summary-result">
            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());
            $(document).on("click","#print-ctr",function(event){
                $("#test-report-frm").submit();
            });
            
            $(document).on('click','#load-report',function(event){
                $("#summary-result").html("<div style='margin-left:auto; margin-right:auto; text-align:center; padding-top:20px;'><img src='<?php echo siteUrl("assets/img/preloader.gif"); ?>' /> <br /><i>Loading...</i></div>");
                $.ajax({
                    type:'POST',
                    url:'show_question_summary.php',
                    data:$("#test-report-frm").serialize()
                }).done(function(msg){
                    $("#contentframe", top.document).height(0);
                    $("#summary-result").html(msg);
                    $(".wrong, .correct").each(function(){
                        var siblingheight=$(this).prev().height();
                        //alert(siblingheight);
                        $(this).height(siblingheight);
                    });
                    $("#contentframe", top.document).height(0).height($(document).height());
                    $("#contentframe", top.document).width($(document).width());

                });
                return false;
            });
            
            $(document).on('click','.showopts',function(event){
                if($(this).hasClass("showing"))
                {
                    $(this).removeClass("showing");
                    $(this).text("Show options");
                        
                }
                else
                {
                    $(this).addClass("showing");
                    $(this).text("Hide options");
                }
                $("#"+$(this).attr("data-toshow")).toggle();
                //$("#contentframe", top.document).height(0));
                $("#contentframe", top.document).height($(document).height());
                $("#contentframe", top.document).width($(document).width());

                return false;
            });
        </script>
    </body>
</html>