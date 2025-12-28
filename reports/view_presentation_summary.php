<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
require_once("test_report_function.php");
openConnection();
global $dbh;
authorize();

if (!isset($_GET['tid'])) {
   header("Location:".siteUrl("403.php"));
    exit();
}
$testid = clean($_GET['tid']);

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin"))) {
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
            #selectable{margin-left: auto; margin-right: auto;}
            #selectable .ui-selecting { background: #FECA40; }
            #selectable .ui-selected { background: #F39814; color: white; }
            #selectable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
            #selectable li { cursor: pointer; margin: 3px; padding: 0.4em; font-size: 1.4em; height: 18px; }
            #selectable li:hover {background-color:  #FECA40;}
            .current {color:black; font-weight: bold;}
        </style>

    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Presentation Report</h2><br />
            [<a class="anchor"href="view_report_summary.php?tid=<?php echo $testid; ?>">Test Result Summary</a>] | [<a class="anchor" href="view_question_summary.php?tid=<?php echo $testid; ?>">Question Response Statistics</a>] | [<a class="anchor current" href="view_presentation_summary.php?tid=<?php echo $testid; ?>">Presentation Report</a>]
            <form class="style-frm" id="test-report-frm" method="post" target="_blank" action="show_presentation_report.php"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <fieldset><legend>Subject</legend>
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

                </fieldset>
                <fieldset><legend>Select Candidate(s)</legend>
                    <span style="font-weight:bold; color:red; font-style: oblique;">Drag over with your cursor to select items or hold down the Ctrl key to make multiple non-adjacent selections. </span><br />
                        <br />
                        <span id="selectioninfo">No selection made yet.</span>
                    <div style="max-height:300px; overflow: auto;">
                        <?php
                        $candidates = get_all_candidate_as_array($testid, array('category' => 'all'));
                        if (count($candidates) == 0)
                            echo"No candidate scheduled for this test!";
                        else {
                            echo"<ol id='selectable'>";
                            foreach ($candidates as $candidate) {
                                $studtype = (($test_config['testname'] == 'Post-UTME') ? ("Post-UTME") : (($test_config['testname'] == 'SBRS') ? ("SBRS") : (($test_config['testname'] == 'SBRS-NEW') ? ("SBRS-NEW") : ("REGULAR"))));
                                $biodata = get_candidate_biodata($candidate, $studtype);
                                $matric = strtoupper($biodata['matricnumber']);
                                $surname = strtoupper($biodata['surname']);
                                $fname = ucfirst(strtolower($biodata['firstname']));
                                $oname = ucfirst(strtolower($biodata['othernames']));
                                echo"<li class='ui-widget-content' data-cid='$candidate'>$matric - $surname, $fname $oname </li>";
                            }
                            echo"</ol>";
                        }
                        ?>
                    </div>
                </fieldset>

                <hr style="border-color:green; border-style: solid; border-width:1px;"/>
                <div style="margin-left:auto; margin-right:0px; width:100px;"><input type="submit" id="load-report" value=" Generate "/></div>
            </form>

            <form id="form2" target="_blank" method="post" action="show_presentation_report.php"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid'];     ?>" />
>
		<input type="hidden" name="tsbjs" id="tsbjs2" />
		<?php
			foreach($candidates as $candidate){
				echo "<input type='hidden' name='candid[]' value = '$candidate' />";
			}
                ?>
		<input type="submit" name="allpres" id="allpres" value="Generate for All" />
           </form>
            <div id="summary-result">
            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
            $( "#selectable" ).selectable({
                stop:function(){
                    $("#selectioninfo").html($("#selectable .ui-selected").size()+" selection(s) made.");
                    $("#selectable li").each(function(){
                        $("input", $(this)).remove();
                        if($(this).hasClass("ui-selected"))
                            {
                                var cid=$(this).attr("data-cid");
                                $(this).append("<input type='hidden' name='candid[]' value='"+cid+"'/>");
                            }
                    });
                }
            });
		$(document).on('change','#tsbjs',function(event){

			$("#tsbjs2").val($("#tsbjs").val());
		});

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

        </script>
    </body>
</html>