<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();

$vid = $_POST['vid'];

$query = "select * from tblvenuecomputers where venueid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($vid));

$i = 1;
?>
<form id="macs" name="macs">
    <input type="hidden" name="vid2" id="vid2" value="<?php echo $vid;?>" />
    <table>
        <tr><th>S/N</th><th>Mac Addr.</th><th>Remove</th></tr>
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mac = $row['computermac'];

            echo"<tr><td>$i</td><td>$mac</td><td><input type='checkbox' name='mcaddr[]' value='$mac' /></td></tr>";
            $i++;
        }
        if ($i == 1)
            echo"<table>No computer registered in this venue.";
        else {
            ?>
            <tr><td colspan="3"><input type="submit" name="removemac" id="removemac" value="Remove Selected"/></td></tr>
                    <?php
                }
                ?>
    </table>
</form>