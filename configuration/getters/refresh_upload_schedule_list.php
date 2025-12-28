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
if (!has_roles(array("Test Administrator"))) {
    echo"<i>Assess Denied!</i>";
    exit();
}

if (!isset($_GET['testid'])) {
    echo"Test not set!";
    exit();
}
$testid = clean($_GET['testid']);

$schedules = get_schedule_ids_as_array($testid);
?>

<fieldset id="sel_schd"><legend>Select Schedule:</legend>

    <table class="style-tbl" style="margin-left: auto; margin-right: auto;"><tr><th>S/N</th><th>Date</th><th>Venue</th><th>Total Possible Batch</th><th>No. Per Batch</th><th>Space</th><th>Start Time</th><th>End Time</th><th>Action</th></tr>
        <?php
        $c = 1;
        $c2 = 1;
        foreach ($schedules as $schedule) {
            $query = "select * from tblscheduling where schedulingid=?";
            $stmt=$dbh->prepare($query);
            $stmt->execute(array($schedule));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $tdate = new DateTime($row['date'] . " 00:00:00");
            $tstime = new DateTime("000-00-00 " . $row['dailystarttime']);
            $tetime = new DateTime("000-00-00 " . $row['dailyendtime']);

            //check to see if date is passed
            $now = new DateTime();
            $schid_date = new DateTime($row['date'] . " " . $row['dailyendtime']);
            $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
            $freespace = get_schedule_freeslot($schedule);
            if ($intaval > 0 && isset($_GET['safemode']) && $_GET['safemode'] != "") {
                echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . (($freespace == -1) ? ("Unlimited") : ($freespace)) . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>[date elapsed]</td></tr>";
            } else {
                echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . (($freespace == -1) ? ("Unlimited") : ($freespace)) . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td><input type='radio' name='schd[]' class='schd' value='" . $schedule . "' " . (($c2 == 1) ? ("checked") : ("")) . "/></td></tr>";
                $c2++;
            }
        }
        if ($c == 1)
            echo "<tr><td colspan='9'>No schedule available yet.</td></tr>";
        ?>
    </table>
    <input type="submit" name="save-btn" id="save-btn" value="Register candidate" /><br />
</fieldset>

