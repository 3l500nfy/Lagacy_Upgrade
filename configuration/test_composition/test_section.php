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
    header("Location:".siteUrl("403.php"));
    exit();
}
if (!isset($_GET['sbjid'])) {
    header("Location:".siteUrl("403.php"));
    exit();
}

$testid = clean($_GET['tid']);
$sbjid = clean($_GET['sbjid']);

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, null, $sbjid))
    header("Location:test_composition.php?tid=$testid");

$test_config = get_test_config_param_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);

if (!in_array($sbjid, $test_subjects)) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Selected subject is not registered for this test! <br /> Click <a href='../test_subject/test_subject.php?tid=$testid'>here</a> to register subjects.</div>";
    exit();
}
$subject_sections = get_test_sections_as_array($testid, $sbjid);
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
            .current{color:black; font-weight: bold;}
        </style>

    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Composition</h2><br />
            <form class="style-frm" id="test-subj-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <input type="hidden" name="sbjid" id="sbjid" value="<?php echo $sbjid; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>
            <a class="anchor" href="test_composition.php?tid=<?php echo $test_config['testid']; ?>" >&gt;&gt;Test Subjects</a>&gt;&gt;<a class="anchor current" href="test_section.php?tid=<?php echo $test_config['testid']; ?>&sbjid=<?php echo $sbjid; ?>" >Sections</a>
            <br /><br />
                <fieldset id="test-section-list"><legend>Available section(s) in <?php echo strtoupper(get_subject_code($sbjid)); ?></legend>
                    <table class="style-tbl">
                        <tr><th>S/N</th><th>SECTION TITLE</th><th>MARK PER QUEST.</th><th>NO. TO ANSWER</th><th>TOTAL MARK</th><th>ACTION</th></tr>
                        <?php
                        $c = 1;

                        foreach ($subject_sections as $subject_section) {
                            $query = "select * from tbltestsection where testsectionid=?";
                            $stmt=$dbh->prepare($query);
                            $stmt->execute(array($subject_section));
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            $section_title = $row['title'];
                            $section_id = $row['testsectionid'];
                            $markperquestion = $row['markperquestion'];
                            $numtoanswer = $row['num_toanswer'];
                            $totalmark = $markperquestion * $numtoanswer;
                            echo"<tr><td>" . $c++ . "</td><td>$section_title</td><td>$markperquestion</td><td>$numtoanswer</td><td>$totalmark</td><td>[<a href='section_compose.php?tid=" . $testid . "&sbjid=" . $sbjid . "&sectionid=$subject_section"."&safemode=1'>compose</a>][<a href='section_modify.php?tid=" . $testid . "&sbjid=" . $sbjid . "&sectionid=" . $subject_section . "&safemode=1'>modify</a>][<a class='remove-section' data-sectionsbj='".$sbjid."' data-sectionnm='" . $section_title . "' data-sectionid='" . $subject_section . "' href='javascript:void(0);'>remove</a>]</td></tr>";
                        }
                        if ($c == 1)
                            echo"<tr><td colspan='6'>No section available.</td></tr>";
                        ?>
                    </table>
                </fieldset>
                <fieldset id="new-section"><legend>New Section</legend>
                    <table>
                        <tr><td></td><td></td></tr>
                        <tr><td>Section Title:</td><td><input type="text" name="section-title" id="section-title" placeholder="e.g. SECTION A" /></td></tr>
                        <tr><td>Instructions (if any):</td><td><input type="text" name="section-instr" id="section-instr" /></td></tr>
                        <tr><td>Mark Per Question:</td><td><input type="text" class="numeric-input" data-type="float" name="section-mpq" id="section-mpq" placeholder="numeric" /></td></tr>
                        <tr><td>No. to Answer:</td><td><input type="text" class="numeric-input" name="section-nta" id="section-nta" placeholder="numeric" /></td></tr>
                        <tr><td>Number of Easy:</td><td><input type="text" class="numeric-input" name="section-noe" id="section-noe" placeholder="numeric" /></td></tr>
                        <tr><td>Number of Moderate:</td><td><input type="text" class="numeric-input" name="section-nom" id="section-nom" placeholder="numeric" /></td></tr>
                        <tr><td>Number of Difficult:</td><td><input type="text" class="numeric-input" name="section-nod" id="section-nod" placeholder="numeric" /></td></tr>
                        <tr><td></td><td><input type="submit" name="add-section" id="add-section" value="Add Section" /></td></tr>
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

            $(document).on('click',"#add-section",function(event){ //alert("changed");
                if($.trim($("#section-title").val())=="" || $.trim($("#section-mpq").val())==""|| $.trim($("#section-nta").val())==""|| ($.trim($("#section-noe").val())==""&& $.trim($("#section-nom").val())==""&& $.trim($("#section-nod").val())==""))
                {
                    alert("Invalid Input!");
                    return false;
                }
                    
                if((($.trim($("#section-noe").val())-0) + ($.trim($("#section-nom").val())-0) + ($.trim($("#section-nod").val())-0)) != $.trim($("#section-nta").val()))
                {
                    alert("\"Number of Easy\" + \"Number of Moderate\" + \"Number of Difficult\" must be equal to \"Number to Answer\"");
                    return false;
                }
                        
                $.ajax({
                    type:"POST",
                    url:"add_section.php",
                    data:$("#test-subj-frm").serialize()
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    if(msg==0)//Server error
                    {
                        alert("Server Error!");
                    }
                    if(msg==1)//Success
                    {
                        //window.location.reload(true);
                        refresh_section_list();
                    }
                    if(msg==2)//Insufficient privilege
                    {
                        alert("Access Denied!");
                    }
                    if(msg==3)//Date Passed
                    {
                        alert("Test Date Exceeded!");
                    }
                    if(msg==4)//Invalid input
                    {
                        alert("Invalid Input");
                    }
                    if(msg==5)//Section already exist
                    {
                        alert("Section "+$("#section-title").val()+" already exist!");
                    }
                });
                return false;
            });
        
            $(document).on('click','.remove-section',function(event){
                var sectionid=$(this).attr('data-sectionid');
                var sectionnm=$(this).attr('data-sectionnm');
                var sectionsbj=$(this).attr('data-sectionsbj');
                if(window.confirm("Are you sure you want to remove the section \""+sectionnm+"\" completely?"))
                {
                    $.ajax({
                        type:'POST',
                        url:'delete_section.php',
                        data:{sectionid:sectionid, tid:$("#tid").val(), sbjid: sectionsbj, safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                    }).done(function(msg){
                        msg=($.trim(msg)-0);
                        if(msg==0)//Server error
                        {
                            alert("Server Error!");
                        }
                        if(msg==1)//Success
                        {
                            //window.location.reload(true);
                            refresh_section_list();
                        }
                        if(msg==2)//Insufficient privilege
                        {
                            alert("Access Denied!");
                        }
                        if(msg==3)//Date Passed
                        {
                            alert("Test Date Exceeded!");
                        }
                        if(msg==4)//Invalid input
                        {
                            alert("Invalid Selection");
                        }
                    });
                }
            });

            $(document).on('click','#safemode',function(event){
                refresh_section_list();
            });
            function refresh_section_list()
            {
                $.ajax({
                    type:'GET',
                    url:'../getters/refresh_section_list.php',
                    data:{testid:$("#tid").val(), sbjid:$("#sbjid").val(), safemode:(($("#safemode").prop("checked")==true)?(1):(""))}
                }).done(function(msg){
                    $("#test-section-list").html(msg);
                    $("#contentframe", top.document).height($(document).height());
                });
            }

        </script>
    </body>
</html>