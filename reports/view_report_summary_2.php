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

if (!isset($_GET['tid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$test_config = get_test_config_param_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);
$test_schedules = get_test_schedule_as_array($testid);
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
        <style type="text/css" rel="stylesheet">
            #slider-info{
                font-style:italic;
            }
            .tohide{
                display:none;
            }
            .pgnav:hover{
                background-color:#bdefba;
            }
            .pgnav{
                border-style:solid; 
                border-width:1px;
                border-color:#333333;
                padding:2px;
                padding-left: 5px;
                padding-right: 5px;
                display: inline-block;
                text-align: center;
                vertical-align: middle;
                margin:2px;
                margin-left: 3px;
                margin-right: 3px;
                cursor: pointer;
            }
            .active-pgnav
            {
                color:#cccccc;
                cursor: default;
                border-style:solid; 
                border-width:1px;
                border-color:#cccccc;
                padding:2px;
                padding-left: 5px;
                padding-right: 5px;
                display: inline-block;
                text-align: center;
                vertical-align: middle;
                margin:2px;
                margin-left: 3px;
                margin-right: 3px;

            }

            #pagination{
                vertical-align: middle;
                text-align: center;
                padding-top: 10px;
            }
            .current{
                font-weight: bold; color:black;
            }
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Result Summary</h2><br />
            [<a class="anchor" href="view_report_summary.php?tid=<?php echo $testid; ?>">Test Result Summary</a>] | [<a class="anchor" href="view_question_summary.php?tid=<?php echo $testid; ?>">Question Response Statistics</a>] | [<a class="anchor" href="view_presentation_summary.php?tid=<?php echo $testid; ?>">Presentation Report</a>] | [<a class="anchor current"href="view_report_summary_2.php?tid=<?php echo $testid; ?>">Test Result Summary Beta</a>]
            <form class="style-frm" id="test-report-frm" method="post" target="_blank" action="show_summary_print_2.php"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div id="filter-panel">
                    <b>Test Subject(s): </b> <select name="tsbjs" id="tsbjs" style="width:300px;">
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
                    <b>Gender: </b> <select name="gender" id="gender"><option value="all">All</option><option value="m">Male</option><option value="f">Female</option><option value="unspecified">Unspecified</option></select>
                    <br />
                    <b>Score Range (Aggregate): </b><div id="slider-range" style="margin-top:10px;margin-left: 20px; margin-right: 20px; width:300px; display:inline-block;"></div>
                    <span id="slider-info">0%(F5) - 100%(A) inclusive</span>
                    <input type="hidden" name="min-range" id="min-range" value="0"/>
                    <input type="hidden" name="max-range" id="max-range" value="100"/><br />
                    <div style="padding:3px; padding-top:10px;"><b>Fields: </b> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-sname" value="sname" checked/> Surname</label> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-fname" value="fname" checked/> Firstname</label> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-oname" value="oname" checked/> Othername</label> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-gender" value="gender"/> Gender</label> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-prog" value="prog" checked/> Programme</label> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-subjscore" value="subjscore" checked/> Subject Score</label> 
                        <?php
                        if (count($test_subjects) > 1)
                            echo'<label><input type="checkbox" name="disp-field[]" id="disp-aggre" value="aggre" checked/> Aggregate</label> ';
                        ?>
                        <label><input type="checkbox" name="disp-field[]" id="disp-percent" value="percent" /> Percentage</label> 
                        <label><input type="checkbox" name="disp-field[]" id="disp-lapse" value="lapse" /> Lapse</label> <br />
                        <label>Record Per Page: <input class="numeric-input" type="text" data-min="1" data-type="numeric" name="perpage" value="2000"/></label>
                    </div> <a href="javascript:void(0);" id="adv-ctr">Advance filter...</a>

                    <div id="advance-filter" style="display:none;">
                        <fieldset>
                            <label><input type="radio" name="category" class="category" id="individual" value="individual" /> Individual</label>
                            <label><input type="radio" name="category" class="category" id="schedule" value="schedule" /> By Schedule</label>
                            <label><input type="radio" name="category" class="category" id="ps" value="ps" checked/> Programme/State</label>
                        </fieldset>
                        <fieldset class="category1" id="individual1" style="display:none;">
                            <legend>Individual:</legend>
                            <b>Candidate Reg No.: </b> <input type="text" type id="regno" name="regno" />
                        </fieldset>
                        <fieldset class="category1" id="schedule1" style="display:none;">
                            <legend>Schedule:</legend>
                            <b>Select Schedule: </b> <select id="schedule" name="schedule">
                                <?php
                                foreach ($test_schedules as $schid) {
                                    $schedule_config = get_schedule_config_param_as_array($schid);
                                    $schedule_date = $schedule_config['date'];
                                    $schedule_starttime = $schedule_config['dailystarttime'];
                                    $schd_date = new DateTime($schedule_date . " " . $schedule_starttime);
                                    $schd_date_formated = $schd_date->format("D, M d, Y h:i a");
                                    $schedule_venue = $schedule_config['venuename'];
                                    $schedule_center = $schedule_config['centername'];
                                    $scheduleid = $schedule_config['schedulingid'];
                                    echo "<option value='$scheduleid'>$schd_date_formated/$schedule_venue/$schedule_center</option>";
                                }
                                ?>
                            </select>
                        </fieldset>
                        <fieldset class="category1" id="ps1" >
                            <legend>Programme:</legend>
                            <b>Faculty: </b> <select id="fac" name="fac"><option value=""> All </option><?php echo get_faculty_as_option(); ?></select>
                            <b>Department: </b> <select style="width:200px;" id="dept" name="dept"><option value=""> All </option></select>
                            <b>Programme: </b> <select style="width:200px;" id="prog" name="prog"><option value=""> All </option></select>
                        </fieldset>
                        <fieldset class="category1" id="ps2">
                            <legend>Origin:</legend>
                            <b>State: </b> <select id="state" name="state"><option value=""> All </option><?php echo get_state_as_option(); ?></select>
                            <b>LGA: </b> <select style="width:200px;" id="lga" name="lga"><option value=""> All </option></select>
                        </fieldset>
                    </div>

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
            $(document).on("click", "#print-ctr", function(event) {
                $("#test-report-frm").attr('action', 'show_summary_print.php').submit();
            });

            $(document).on("click", "#excel-ctr", function(event) {
                $("#test-report-frm").attr('action', 'show_summary_excel.php').submit();
            });
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 100,
                values: [0, 100],
                slide: function(event, ui) {
                    minv = ui.values[ 0 ];
                    maxv = ui.values[ 1 ];
                    $("#min-range").val(minv);
                    $("#max-range").val(maxv);
                    if (minv == maxv)
                    {
                        $("#slider-info").html(minv + "% (" + compute_standard_grade(minv) + ")");
                    }
                    else
                    {
                        $("#slider-info").html(minv + "% (" + compute_standard_grade(minv) + ") - " + maxv + "% (" + compute_standard_grade(maxv) + ") inclusive");
                    }
                }
            });

            function compute_standard_grade(v)
            {
                if (v >= 70)
                    return "A";
                if (v >= 60)
                    return "B";
                if (v >= 50)
                    return "C";
                if (v >= 45)
                    return "D";
                if (v >= 40)
                    return "E";
                if (v >= 39)
                    return "F1";
                if (v >= 38)
                    return "F2";
                if (v >= 35)
                    return "F3";
                if (v >= 30)
                    return "F4";
                return "F5";
            }

            $(document).on('click', '#load-report', function(event) {
                $("#summary-result").html("<div style='margin-left:auto; margin-right:auto; text-align:center; padding-top:20px;'><img src='<?php echo siteUrl("assets/img/preloader.gif"); ?>' /> <br /><i>Loading...</i></div>");
                $.ajax({
                    type: 'POST',
                    url: 'show_summary_2.php',
                    data: $("#test-report-frm").serialize()
                }).done(function(msg) {
                    $("#contentframe", top.document).height(0);
                    $("#summary-result").html(msg);
                    $("#contentframe", top.document).height($(document).height());
                    $("#contentframe", top.document).width($(document).width());
                    //alert($("#report-summary-table").offset().top);
                    $(top.document).scrollTop($("#report-summary-table").offset().top);

                });
                return false;
            });
            $(document).on('change', '#tsbjs', function(event) {
                if ($(this).val() != "")
                {
                    $("#disp-aggre").prop("checked", false).prop("disabled", true);
                }
                else
                {
                    $("#disp-aggre").prop("disabled", false);
                }


            });
            $(document).on('click', '.pgnav', function(event) {
                $(".active-pgnav").removeClass("active-pgnav").addClass("pgnav");
                $(".rec").addClass("tohide");
                var strec = $(this).attr("data-start");
                var stoprec = $(this).attr("data-stop");
                $(".rec").each(function() {
                    if ($(this).index() >= strec && $(this).index() <= stoprec)
                        $(this).removeClass("tohide");
                });
                $(window.top.document).scrollTop(150);//.scrollTop();
                $("#contentframe", top.document).height(0).height($(document).height());

                $(this).addClass("active-pgnav").removeClass(".pgnav");

            });

            $(document).on('change', '#fac', function(event) {
                var dis = $(this);
                $("#prog").html("<option value=''> All </option>");
                $("#dept").html("<option value=''> Loading... </option>");
                if (dis.val() == "")
                {
                    $("#dept").html("<option value=''> All </option>");
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: 'getters/get_dept.php',
                    data: {facid: dis.val()}
                }).done(function(msg) {
                    $("#dept").html("<option value=''> All </option>" + msg);
                });
                return false;
            });

            $(document).on('change', '#dept', function(event) {
                var dis = $(this);
                $("#prog").html("<option value=''> Loading... </option>");
                if (dis.val() == "")
                {
                    $("#prog").html("<option value=''> All </option>");
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: 'getters/get_prog.php',
                    data: {deptid: dis.val()}
                }).done(function(msg) {
                    $("#prog").html("<option value=''> All </option>" + msg);
                });
                return false;
            });

            $(document).on('change', '#state', function(event) {
                var dis = $(this);
                $("#lga").html("<option value=''> Loading... </option>");
                if (dis.val() == "")
                {
                    $("#lga").html("<option value=''> All </option>");
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: 'getters/get_lga.php',
                    data: {stateid: dis.val()}
                }).done(function(msg) {
                    $("#lga").html("<option value=''> All </option>" + msg);
                });
                return false;
            });

            $(document).on('click', '#adv-ctr', function(event) {
                $("#advance-filter").toggle();
                $("#contentframe", top.document).height($(document).height());
                $("#contentframe", top.document).width($(document).width());

            });

            $(document).on('click', '.category', function(event) {
                var v = $(this).val();
                $(".category1").hide();

                if (v == "individual") {
                    $("#individual1").show();
                } else if (v == "schedule") {
                    $("#schedule1").show();
                } else {
                    $("#ps1").show();
                    $("#ps2").show();
                }

                $("#contentframe", top.document).height($(document).height());
                $("#contentframe", top.document).width($(document).width());

            });
        </script>
    </body>
</html>
