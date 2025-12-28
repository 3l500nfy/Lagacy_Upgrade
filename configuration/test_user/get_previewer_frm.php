<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_POST['tid'])) {
    echo "Incomplete parameter list.";
    exit();
}
if (!isset($_POST['pno'])) {
    echo "Incomplete parameter list.";
    exit();
}

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid))
{
    echo"Access denied.";
    exit();
}

$pno = clean($_POST['pno']);
$test_config = get_test_config_param_as_array($testid);

$test_subjects = get_test_subjects_as_array($testid);
if (count($test_subjects) == 0) {
    echo "No subject registered for this test yet";
    exit();
}

$question_previewer= get_staff_userid($pno);

if($question_previewer==0)
{
    echo"Not a registered user of CBT";
    exit();
}

if($question_previewer== $_SESSION['MEMBER_USERID'])
{
   echo"All test administrators are automatically question previewers of their test subjects.";
   exit();
}
$biodata = get_staff_biodata($pno);
if(count($biodata)==0)
    {
   echo"Specified user is not a staff of this institution.";
   exit();
}

$subject_previewed = get_previewer_subject_as_array($testid, $question_previewer);
?>
<table>
    <tr><td>Full Name:</td><td><?php echo ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames'])); ?></td></tr>
    <tr><td>P. No.:</td><td><?php echo strtoupper(str_replace(".","",$pno)); ?><input type="hidden" name="uid" value="<?php echo $question_previewer; ?>"</td></tr>
    <tr><td>Department: </td><td><?php echo ucwords(strtolower($biodata['departmentname'])); ?></td></tr>
    <tr><td>Gender: </td><td><?php echo ucfirst($biodata['gender']); ?></td></tr>
    <tr><td style="vertical-align: top;">Test subject(s): </td><td><table class="style-tbl"><?php
foreach ($test_subjects as $testsubject) {
    echo "<tr ><td><label><input type='checkbox' name='tsbj[]' class='tsbj' value='$testsubject' " . ((in_array($testsubject, $subject_previewed)) ? ("checked") : ("")) . " /> " . strtoupper(get_subject_code($testsubject)) . "-" . ucwords(strtolower(get_subject_name($testsubject))) . "</label></td></tr>";
}
?></table>
        <input type="submit" id="submit" name="submit" value="Submit"/><span id="submit-info"></span></td></tr>
</table>