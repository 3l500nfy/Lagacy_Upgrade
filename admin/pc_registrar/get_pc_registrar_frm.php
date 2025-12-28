<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_POST['pno'])) {
    echo "Incomplete parameter list.";
    exit();
}

$pno = clean($_POST['pno']);

$pc_registrar = get_staff_userid($pno);
//echo $pc_registrar; exit();
if ($pc_registrar == 0) {
    echo"Not a registered user of CBT";
    exit();
}

$biodata = get_staff_biodata($pno);
if (count($biodata) == 0) {
    echo"Specified user is not a staff of this institution.";
    exit();
}
if (is_pc_registrar($pc_registrar)) {
    echo"Already a pc registrar";
    exit();
}
?>
<table>
    <tr><td>Full Name:</td><td><?php echo ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames'])); ?></td></tr>
    <tr><td>P. No.:</td><td><?php echo strtoupper(str_replace(".", "", $pno)); ?><input type="hidden" name="uid" value="<?php echo $pc_registrar; ?>"</td></tr>
    <tr><td>Department: </td><td><?php echo ucwords(strtolower($biodata['departmentname'])); ?></td></tr>
    <tr><td>Gender: </td><td><?php echo ucfirst($biodata['gender']); ?></td></tr>
    <tr><td style="vertical-align: top;">
            <input type="submit" id="submit" name="submit" value="Assign Test Administrator Role"/><span id="submit-info"></span></td></tr>
</table>