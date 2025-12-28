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

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, null, $sbjid))
    header("Location:" . siteUrl("403.php"));

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
            .current {color:black; font-weight: bold;}
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Composition</h2><br />
            <form class="style-frm" id="test-subj-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <input type="hidden" name="sbjid" id="sbjid" value="<?php echo $sbjid; ?>" />
                <input type="hidden" name="sectionid" id="sectionid" value="<?php echo $sectionid; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" <?php if (isset($_GET['safemode']) && $_GET['safemode'] == 1) echo "checked"; ?>/> safe mode</label></div>
                &gt;&gt;<a class="anchor" href="test_composition.php?tid=<?php echo $test_config['testid']; ?>" >Test Subjects</a>&gt;&gt;<a class="anchor" href="test_section.php?tid=<?php echo $test_config['testid']; ?>&sbjid=<?php echo $sbjid; ?>" >Sections</a>&gt;&gt;<a class="anchor current" href="section_modify.php?tid=<?php echo $test_config['testid']; ?>&sbjid=<?php echo $sbjid; ?>&sectionid=<?php echo $sectionid; ?>" >Modify Section</a>
                <br /><br />

                <fieldset id="new-section"><legend>Modify Section</legend>
                    <?php
                    $query = "select * from tbltestsection where testsectionid=?";
                    $stmt=$dbh->prepare($query);
                    $stmt->execute(array($sectionid));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    ?>
                    <table>
                        <tr><td></td><td><?php if (false) {
                        echo"<b style='color:red;'>Test Date Exceeded!</b>";
                    } ?></td></tr>
                        <tr><td>Section Title:</td><td><input type="text" name="section-title" id="section-title" placeholder="e.g. SECTION A" value="<?php echo $row['title']; ?>" /></td></tr>
                        <tr><td>Instructions (if any):</td><td><input type="text" name="section-instr" id="section-instr" value="<?php echo $row['instruction']; ?>" /></td></tr>
                        <tr><td>Mark Per Question:</td><td><input type="text" class="numeric-input" data-type="float" name="section-mpq" id="section-mpq" placeholder="numeric" value="<?php echo $row['markperquestion']; ?>" /></td></tr>
                        <tr><td>No. to Answer:</td><td><input type="text" class="numeric-input" name="section-nta" id="section-nta" placeholder="numeric" value="<?php echo $row['num_toanswer']; ?>" /></td></tr>
                        <tr><td>Number of Easy:</td><td><input type="text" class="numeric-input" name="section-noe" id="section-noe" placeholder="numeric" value="<?php echo $row['numofeasy']; ?>" /></td></tr>
                        <tr><td>Number of Moderate:</td><td><input type="text" class="numeric-input" name="section-nom" id="section-nom" placeholder="numeric"value="<?php echo $row['numofmoderate']; ?>" /></td></tr>
                        <tr><td>Number of Difficult:</td><td><input type="text" class="numeric-input" name="section-nod" id="section-nod" placeholder="numeric" value="<?php echo $row['numofdifficult']; ?>" /></td></tr>
                        <tr><td></td><td><input type="submit" name="modify-section" id="modify-section" value="Modify Section"  /></td></tr>

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

<?php
if (false) {
    echo"\$('#test-subj-frm input, #test-subj-frm select, #test-subj-frm textarea').prop('disabled',true);";
}
?>
            $(document).on('click', "#modify-section", function(event) { //alert("changed");
                if ($.trim($("#section-title").val()) == "" || $.trim($("#section-mpq").val()) == "" || $.trim($("#section-nta").val()) == "" || ($.trim($("#section-noe").val()) == "" && $.trim($("#section-nom").val()) == "" && $.trim($("#section-nod").val()) == ""))
                {
                    alert("Invalid Input!");
                    return false;
                }

                if ((($.trim($("#section-noe").val()) - 0) + ($.trim($("#section-nom").val()) - 0) + ($.trim($("#section-nod").val()) - 0)) != $.trim($("#section-nta").val()))
                {
                    alert("\"Number of Easy\" + \"Number of Moderate\" + \"Number of Difficult\" must be equal to \"Number to Answer\"");
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "modify_section.php",
                    data: $("#test-subj-frm").serialize()
                }).done(function(msg) { //alert(msg);
                    msg = ($.trim(msg) - 0);
                    if (msg == 0)//Server error
                    {
                        alert("Server Error!");
                    }
                    if (msg == 1)//Success
                    {
                        alert("Section Modified Successfully.");
                        //window.location.reload(true);
                    }
                    if (msg == 2)//Insufficient privilege
                    {
                        alert("Access Denied!");
                    }
                    if (msg == 3)//Date Passed
                    {
                        alert("Test Date Exceeded!");
                    }
                    if (msg == 4)//Invalid input
                    {
                        alert("Invalid Input");
                    }
                    if (msg == 5)//Section already exist
                    {
                        alert("Section " + $("#section-title").val() + " already exist!");
                    }
                });
                return false;
            });

        </script>
    </body>
</html>