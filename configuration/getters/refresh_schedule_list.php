<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
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
if (count($schedules) > 0) {
    ?>

    <br />
    <fieldset id="schd-list"><legend>Existing Schedules</legend>

        <div id="existing-schedule">
            <table class="style-tbl" style="margin-left: auto; margin-right: auto;"><tr><th>S/N</th><th>Date</th><th>Venue</th><th>Total Possible Batch</th><th>No. Per Batch</th><th>Start Time</th><th>End Time</th><th>Action</th></tr>
                <?php
                $c = 1;
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
                    $schid_date = new DateTime($row['date'] . " " . $row['dailystarttime']);
                    $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());
                    if ($intaval > 0 && isset($_GET['safemode']) && $_GET['safemode'] !="") {
                        echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>[date elapsed]</td></tr>";
                    }else
                        echo"<tr><td>" . $c++ . "</td><td>" . $tdate->format("D, M d, Y") . "</td><td>" . intelligentStr(get_venue_name($row['venueid'])) . "</td><td>" . (($row['maximumBatch'] == -1) ? ("Unlimited") : ($row['maximumBatch'])) . "</td><td>" . $row['noPerschedule'] . "</td><td>" . $tstime->format("h:i a") . "</td><td>" . $tetime->format("h:i a") . "</td><td>[<a href='test_schedule_edit.php?schid=" . $row['schedulingid'] . "".((isset($_GET['safemode']) && $_GET['safemode'] !="")?("&safemode=1"):(""))."'>edit</a>] [<a class='del-schd' data-schd='" . $row['schedulingid'] . "' href='javascript:void(0);'>remove</a>]</td></tr>";
                }
                ?>
            </table>
        </div><br /></fieldset>
    <?php
}
?>
