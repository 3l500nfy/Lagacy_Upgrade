<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
//require_once('../../lib/candid_scheduling_func.php');
require_once("test_report_function.php");
openConnection();
global $dbh;
authorize();
//get testid
if (!isset($_POST['tid']))
{
    header("Location:".siteUrl("403.php"));
    exit();
}

$tid = $_POST['tid'];
if (!is_test_administrator_of($tid) && !is_test_compositor_of($tid) && !has_roles(array("Super Admin")) && !has_roles(array("Admin")))
{
    header("Location:".siteUrl("403.php"));
    exit();
}

$test_config=get_test_config_param_as_array($tid);
$fsubj = ((isset($_POST['tsbjs']) && trim($_POST['tsbjs']!="")) ? (array($_POST['tsbjs'])) : (get_subject_combination_as_array($tid)));
if (count($fsubj) == 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No subject registered on this test</div>";
    exit();
}

//echo $fields['1'];
?>


<div>
    <?php
    
    foreach ($fsubj as $sbj)
    {
        $sbjcode=strtoupper(get_subject_code($sbj));
        $sbjname=get_subject_name($sbj);
        $sbjtitle=$sbjcode."-".$sbjname;
        echo"<h2>$sbjtitle</h2>";
        $questions=get_all_questionid_as_array($tid, $sbj);
        foreach($questions as $question)
        {
            $questiontext=get_full_question($question);
            $questionoptions=get_question_options_as_array($question);
            $presentcount=get_candidate_presented_count($question, $tid);
            $passcount=get_question_pass_count($question, $tid, $questionoptions);
            $failcount=get_question_fail_count($question, $tid, $questionoptions);
            $nooptioncount=($presentcount-($passcount+$failcount));
            echo"<div class='questiontxt'>";
            echo html_entity_decode($questiontext,ENT_QUOTES);
            echo"<div class='qstat'><ul><li class='presentstat'><b>No of candidate(s) presented to: </b> $presentcount</li>
            <li class='correctstat'><b>Correct Response(s): </b> $passcount</li>
            <li class='wrongstat'><b>Incorrect Response(s): </b> $failcount</li>
            <li class='nooptionstat'><b>No response: </b> $nooptioncount</li>
            </ul></div><a class='showopts' data-toshow='qopt".$question."' href='javascript:void(0);'>Show options</a><hr /><div id='qopt".$question."' class='qopts'>";
            foreach($questionoptions as $questionoption)
            {
                $choicecount=get_choice_count($tid, $questionoption['answerid']);
                echo "<div><div style='display:inline-table;'>".html_entity_decode($questionoption['test'],ENT_QUOTES)."</div> <div class='".(($questionoption['correctness']==1)?("correct"):("wrong"))."' style='display:inline-table;'><span> (". (($choicecount==0)?("none"):($choicecount))." chose this)</span></div></div>";
            }
            echo"</div>";
            echo"</div>";
        }
    }
    ?>
</div>
