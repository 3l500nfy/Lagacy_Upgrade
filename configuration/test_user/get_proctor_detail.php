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
if (!isset($_POST['tid'])) {
    echo "Incomplete parameter.";
    exit();
}
if (!isset($_POST['pno'])) {
    echo "Incomplete parameter.";
    exit();
}

if (!isset($_POST['schdid'])) {
    echo "Incomplete parameter";
    exit();
}

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid))
{
    header("Location:" . siteUrl("403.php"));
    exit();
}

$pno = clean($_POST['pno']);
$schdid = clean($_POST['schdid']);
$test_config = get_test_config_param_as_array($testid);

$test_proctor= get_staff_userid($pno);

if($test_proctor==0)
{
    echo"Not a registered user of CBT";
    exit();
}

if($test_proctor== $_SESSION['MEMBER_USERID'])
{
   echo"All test administrators are automatically test invigilator of their test schedules.";
   exit();
}
$biodata = get_staff_biodata($pno);
if(count($biodata)==0)
    {
   echo"User is not a staff of this institution.";
   exit();
}
if(is_test_invigilator_of($schdid, $test_proctor))
{
    echo "Specified user is already an invigilator for this schedule.";
    exit();
}
?>
<table>
    <tr><td>Full Name:</td><td><?php echo ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames'])); ?></td></tr>
    <tr><td>P. No.:</td><td><?php echo strtoupper(str_replace(".","",$pno)); ?><input type="hidden" name="uid" value="<?php echo $test_proctor; ?>"</td></tr>
    <tr><td>Department: </td><td><?php echo ucwords(strtolower($biodata['departmentname'])); ?></td></tr>
    <tr><td>Gender: </td><td><?php echo ucfirst($biodata['gender']); ?></td></tr>
    <tr><td>Endorsement key: </td><td><input type="text" value="abc" name="end-key" id="end-key" /></td></tr>
    <tr><td></td><td> <input type="submit" id="submit" name="submit" value="Register as invigilator for the above schedule"/><span id="submit-info"></span></td></tr>
</table>