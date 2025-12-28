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
    header("Location:" . siteUrl("403.php"));
    exit();
}

if (!isset($_GET['sbjid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

if (!isset($_GET['sectionid'])) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$testid = clean($_GET['tid']);
$sbjid = clean($_GET['sbjid']);
if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, null, $sbjid)) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

//need to remove the exclamation mark
if (date_exceeded($testid, 0, "highest") && isset($_GET['safemode']) && $_GET['safemode'] == "1") {
    echo "Test date exceeded!";
    exit();
}

$sectionid = clean($_GET['sectionid']);

$test_config = get_test_config_param_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);
if (!in_array($sbjid, $test_subjects)) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Selected subject is not registered for this test! <br /> Click <a href='../test_subject/test_subject.php?tid=$testid'>here</a> to register subjects.</div>";
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

            /*styling for question list and navigation buttons*/
            .qvinfo{
                border-color:orange;
                border-style: solid;
                border-width: 1px;
                padding:3px;
                background-color: #ffffcc;
                font-size: 10px;
                width:200px;
                margin-left:auto;
                margin-right: 0px;
                margin-bottom: 1px;
            }
            .ql{
                border-bottom-style: dotted;
                border-bottom-width: 1px;
                border-bottom-color: black;
            }
            .q{
                border-style: solid;
                border-color: #cccccc;
                border-width: 1px;
                -moz-border-radius: 5px ;
                -webkit-border-radius: 5px ;
                padding: 5px;
                background-color: #f2f2f2;
                color:#7f7f7f;

            }
            .q a{
                color:#7f7f7f;
            }

            .use_hist{
                padding: 5px;
                display:none;
            }
            #questionwrapper{
                border-radius:3px;
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
            }


            #pgkeydiv
            {
                vertical-align: middle;

                text-align: center;

            }

            .pgkey
            {
                text-align: center;
                vertical-align: middle;
                min-width:10px;
                border-style: solid;
                border-color: #cccccc;
                border-width: 1px;
                display:inline-block;
                padding:10px;
                margin:5px;
                font-size: 1em;
                cursor:pointer;
            }

            .pgkey:hover
            {
                border-color: #89d387;
                background-color:  #bdefba;
            }

            .curr
            {
                text-align: center;
                vertical-align: middle;
                min-width:10px;
                background-color: #bdefba;
                border-style: solid;
                border-color: #89d387;
                border-width: 1px;
                display:inline-block;
                padding:10px;
                margin:5px;
                font-size: 1em;
                opacity:0.5;
            }

            #pgsel
            {
                width:80px;
            }
            .current{ color:black; font-weight: bold;}

        </style>
</head>
<body style="background-image: url('../img/bglogo2.jpg');">

    <div id="framecontent">
        <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Composition</h2><br />
        <form class="style-frm" id="test-quest-frm" >
            <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" <?php if(isset($_GET['safemode']) && $_GET['safemode']==1) echo "checked"; ?>/> safe mode</label></div>
            &gt;&gt;<a class="anchor" href="test_composition.php?tid=<?php echo $test_config['testid']; ?>" >Test Subjects</a>&gt;&gt;<a class="anchor" href="test_section.php?tid=<?php echo $test_config['testid']; ?>&sbjid=<?php echo $sbjid; ?>" >Sections</a>&gt;&gt;<a class="anchor current" href="section_compose.php?tid=<?php echo $test_config['testid']; ?>&sbjid=<?php echo $sbjid; ?>&sectionid=<?php echo $sectionid; ?>" >Compose Questions</a>
            <br /><br />
            <b>I want to compose questions for version:</b> <select name="version" id="version"><?php for ($i = 1; $i <= $test_config['versions']; $i++)
        echo "<option value='$i'>$i</option>";
    ?></select> <b> of this test.</b> <br /><hr />
            <input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
            <input type="hidden" name="sbjid" id="sbjid" value="<?php echo $sbjid; ?>" />
            <input type="hidden" name="sectionid" id="sectionid" value="<?php echo $sectionid; ?>" />
            <b>Difficulty Level:</b> <select name="diff-lvl" id="diff-lvl"><option value="">All</option><option value="simple">Easy</option><option value="difficult">Moderate</option><option value="moredifficult">Difficult</option></select>
            <b>Topic:</b> <select name="topic" id="topic" style="width:150px;"><option value="">All</option><?php echo get_topics_as_options($sbjid); ?></select>
            <b>Author:</b> <select name="author" id="author" ><option value="">All</option><option value="me">Me</option><option value="someoneelse">Someone else</option></select><br />
            <b>Search for questions containing: </b> <input type="text" name="phrase" id="phrase" placeholder="Type a phrase" style="width:250px;"/>
            <input type="submit" name="load" id="load" value="Load Questions" /><br />
            <div id="issues">

            </div>
            <hr style="border-width: 2px; border-style:  groove;" />
            <div id="question-list">
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

    <script type="text/javascript">

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());
            var pgclicked = 1;
            //alert($("#contentframe", top.document).height()); 
            $(document).on('change', '#version', function(event) {
                $("#load").trigger("click");
            });

            $(document).on('click', "#load", function(event) { //alert("changed");
                $("#question-list").html("<div style='margin-left:auto; margin-right:auto; text-align:center; padding-top:20px;'><img src='<?php echo siteUrl("assets/img/preloader.gif"); ?>' /> <br /><i>Loading...</i></div>");
                $.ajax({
                    type: 'POST',
                    url: 'get_questions.php?page=1',
                    data: $("#test-quest-frm").serialize()
                }).done(function(msg) {
                    $("#question-list").html(msg);
                    $("#contentframe", top.document).height(0);
                    $("#contentframe", top.document).height($(document).height());
                    pgclicked = 1;
                });
                check_issues();
                return false;
            });

            $(document).on('click', '.moreinf', function(event) {
                var id = $(this).attr('id');
                // alert($("#uh"+id).attr('display'));

                if ($("#uh" + id).css('display') != 'none')
                {

                    $("#uh" + id).hide();
                    $(this).text("Show usage history...");
                }
                else
                {
                    $("#uh" + id).show();
                    $(this).text("Hide usage history...");
                }
                // $("#contentframe", top.document).height(0);
                $("#contentframe", top.document).height($(document).height());

            });

            $(document).on('click', '.fullquest', function(event) {
                var id = $(this).attr('id');
                id = id.substring(1);
                //alert(id);
                $.ajax({
                    type: 'POST',
                    url: 'get_full_question.php',
                    data: {qid: id}
                }).done(function(msg) {
                    $("<div title='Full Question'>" + msg + "</div>").dialog({modal: true, width: "600px"});
                    //$("#contentframe", top.document).height(0);
                    $("#contentframe", top.document).height($(document).height());

                });
                return false;
            });

            $(document).on('click', ".q_select_multi", function(event) {
                var id = $(this).attr('id');
                var qid = $(this).val();
                var clk = $(this);

                if (id == "q_select_all")
                {
                    //$(this).prop('checked', true);
                    $(".q_select").each(function() {
                        if ($(this).prop('checked') == false) {
                            $(this).click()
                        }
                    });
                }
                else {
                    $(".q_select").each(function() {
                        if ($(this).prop('checked') == true) {
                            $(this).click()
                        }
                    });
                }
                return false;
            });

            $(document).on('click', ".q_select", function(event) {
                //             alert("en");
                var id = $(this).attr('id');
                var qid = $(this).val();
                var clk = $(this);

                if ($(this).prop('checked') == true)
                {
                    //$(this).prop('checked', true);
                    var op = 'add';
                    $('label[for=' + id + ']').text('Deselect').hide();
                    $(this).prev().prev().show();
                    $(this).hide();
                }
                else
                {
                    // $(this).removeAttr('checked');

                    var op = 'remove';
                    $('label[for=' + id + ']').text('Select').hide();
                    $(this).prev().prev().show();
                    $(this).hide();


                }

                var ver = $("#version").val();
                var sectid = $('#sectionid').val();

                $.ajax({
                    type: 'POST',
                    url: 'register_question.php',
                    data: {qid: qid, ver: ver, sectid: sectid, op: op}
                   // alert("#data").val();
                }).done(function(msg) { //alert(msg);
                    if (($.trim(msg) - 0) == 0)
                    {
                        if (op == 'add')
                        {
                            clk.prop('checked', false);
                            $('label[for=' + id + ']').text('Select');
                        }
                        else
                        {
                            clk.prop('checked', true);
                            $('label[for=' + id + ']').text('Deselect');
                        }
                    }
                    else
                    {
                        if (op == 'add')
                            $('#qcount').html((($('#qcount').text()) - 0) + 1);
                        else
                            $('#qcount').html((($('#qcount').text()) - 0) - 1);

                    }
                    $('label[for=' + id + ']').show();
                    clk.prev().prev().hide();
                    clk.show();
                    check_issues();
                });

            });
            //:::::::::::::::::Events particularly for pagination

            $(document).on('click', ".pgkey", function(event) {
                var vl = $(this).text();
                //alert(vl);
                if (vl == 'Next')
                {
                    vl = pgclicked + 1;
                }
                else
                if (vl == 'Previous')
                {
                    vl = pgclicked - 1;
                }
                else
                    vl = vl - 0;
                //alert(vl);
                $.ajax({
                    type: 'POST',
                    url: 'get_questions.php?page=' + vl,
                    data: $("#test-quest-frm").serialize()
                }).done(function(msg) {
                    $('#question-list').html(msg);
                    pgclicked = vl;
                    //load_qcount();
                    $("#contentframe", top.document).height(0);
                    $("#contentframe", top.document).height($(document).height());
                    $(window.top.document).scrollTop(0);

                });

            });

            $(document).on('change', "#pgsel", function(event) {
                // alert("ene");
                var vl = $(this).val();
                //alert(vl);
                vl = vl - 0;
                //alert(vl);
                $.ajax({
                    type: 'POST',
                    url: 'get_questions.php?page=' + vl,
                    data: $("#test-quest-frm").serialize()
                }).done(function(msg) {
                    $('#question-list').html(msg);
                    pgclicked = vl;
                    //load_qcount();
                    $("#contentframe", top.document).height(0);
                    $("#contentframe", top.document).height($(document).height());
                    $(window.top.document).scrollTop(0);

                });

            });

            function check_issues()
            {
                $("#issues").html("<div><img src='<?php echo siteUrl("assets/img/preloader.gif"); ?>' /> <br /><i>Loading...</i></div>");
                var sectionid = $("#sectionid").val();
                var version = $("#version").val();
                $.ajax({
                    type: 'POST',
                    url: 'check_section_issues.php',
                    data: {sectionid: sectionid, version: version}
                }).done(function(msg) {
                    $("#issues").html(msg);

                    $("#contentframe", top.document).height($(document).height());
                });
            }


    </script>
</body>
</html>