<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_GET['tid']))
    header("Location:".siteUrl("403.php"));
$testid = clean($_GET['tid']);

if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid) && !is_question_viewer_of($testid))
    header("Location:".siteUrl("403.php"));

$test_config = get_test_config_param_as_array($testid);
$test_subjects = get_test_subjects_as_array($testid);
if (count($test_subjects) == 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No subject registered for this test! <br /> Click <a href='../test_subject/test_subject.php?tid=$testid'>here</a> to register subjects.</div>";
    exit();
}
if (count($test_subjects) == -1) {
    $test_subject = $test_subjects[0];
    header("Location:test_section.php?tid=" . $testid . "&sbjid=$test_subject");
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
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Composition</h2><br />
            <form class="style-frm" id="test-subj-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />

                <fieldset id="test-subj-list"><legend>Available test subjects</legend>
                    <table class="style-tbl">
                        <tr><th>S/N</th><th>CODE</th><th>SUBJECT NAME</th><th>ACTION</th></tr>
                        <?php
                        $c = 1;
                        $versions= get_test_versions_as_array($testid);
                        foreach ($test_subjects as $test_subject) {
                            if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, $_SESSION['MEMBER_USERID'], $test_subject) && !is_question_viewer_of($testid, $_SESSION['MEMBER_USERID'], $test_subject))
                                  continue;
                            $query = "select * from tblsubject where subjectid=?";
                            $stmt=$dbh->prepare($query);
                            $stmt->execute(array($test_subject));
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            $subject_code = $row['subjectcode'];
                            $subject_name = $row['subjectname'];
                            $subject_id = $row['subjectid'];
                            echo"<tr><td>" . $c++ . "</td><td>$subject_code</td><td>$subject_name</td><td>";
                            for($j=1; $j<=$stmt->rowCount(); $j++)
                            {
                                if(get_test_question_count($testid, $test_subject, $j)>0)
                                echo"[<a target='_blank' href='question_preview_frm.php?tid=" . $testid . "&version=".$j."&sbjid=$subject_id'>preview version $j</a>]";
                                else
                                    echo"<i>No question available</i>";
                            }
                            echo "</td></tr>";
                                
                        }
                        ?>
                    </table>
                </fieldset>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());
            
            //alert($("#contentframe", top.document).height());            
            $(document).on('change',"#checkall",function(event){ //alert("changed");
                if($(this).prop("checked"))
                {
                    $(".schd").prop('checked',true);
                }
                else
                {
                    $(".schd").prop('checked',false);
                }
            });
            
            $(document).on('change','.schd',function(event){
                $("#checkall").prop('checked',false);
            });            

            $(document).on('click','#save-btn',function(event){
                if(trim($("#sheet").val())=="")
                    $("#sheet").val(1);
            });            

        </script>
    </body>
</html>