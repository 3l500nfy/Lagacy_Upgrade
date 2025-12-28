<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("cvs_functions.php");
openConnection();

$vid = $_POST['vid'];

$query = "select * from tblvenue where venueid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($vid));

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$centreid = $row['centreid'];
$venuename = $row['venuename'];
$location = $row['location'];
$capacity = $row['capacity'];
?>
<form id="venuefrm" name="venuefrm">
    <input type="hidden" name="vid2" id="vid2" value="<?php echo $vid; ?>"/>
    <table>
        <tr><td>Center: </td><td><select name="edt-center" id="edt-center"><?php echo get_center_as_options($vid); ?></select></td></tr>
        <tr><td>Venue: </td><td><input type="text" name='edt-vn' id='edt-vn' value='<?php echo $venuename; ?>' /></td></tr>
        <tr><td>Location: </td><td><input type='text' name='edt-loc' id='edt-loc' value='<?php echo $location; ?>' /></td></tr>
        <tr><td>Capacity: </td><td><input type='text' name='edt-cap' id='edt-cap' value='<?php echo $capacity; ?>' /></td></tr>
        <tr><td colspan="3"><input type="submit" name="editvn2" id="editvn2" value="Update"/></td></tr>
    </table>
</form>