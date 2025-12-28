<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
openConnection();
global $dbh;
$candidatetype = $_POST['candidatetype'];
$examtype = $_POST['testid'];
$username = $_POST['username'];

// Initialize variables
$RegNo = '';
$surname = '';
$firstname = '';
$othernames = '';
$CandName = '';

if ($candidatetype == 2) {
    $query = "SELECT * from tbljamb WHERE tbljamb.RegNo=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($username));

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = $row['id'];
        $DeptSn = $row['DeptSn'];
        $RegNo = $row['RegNo'];
        $CandName = $row['CandName'];
        $StateOfOrigin = $row['StateOfOrigin'];
        $Sex = $row['Sex'];
        $Age = $row['Age'];
        $EngScore = $row['EngScore'];
        $Subj2 = $row['Subj2'];
        $Subj2Score = $row['Subj2Score'];
        $Subj3 = $row['Subj3'];
        $Subj3Score = $row['Subj3Score'];
        $Subj4 = $row['Subj4'];
        $Subj4Score = $row['Subj4Score'];
        $TotalScore = $row['TotalScore'];
        $Faculty = $row['Faculty'];
        $Course = $row['Course'];
    }
    $_SESSION['RegNo'] = $RegNo;
    $_SESSION['CandName'] = $CandName;
    $_SESSION['StateOfOrigin'] = $StateOfOrigin;
    $_SESSION['Sex'] = $Sex;
    $_SESSION['Age'] = $Age;
    $_SESSION['EngScore'] = $EngScore;
    $_SESSION['Subj2'] = $Subj2;
    $_SESSION['Subj2Score'] = $Subj2Score;
    $_SESSION['Subj3'] = $Subj3;
    $_SESSION['Subj3Score'] = $Subj3Score;
    $_SESSION['Subj4'] = $Subj4;
    $_SESSION['Subj4Score'] = $Subj4Score;
    $_SESSION['TotalScore'] = $TotalScore;
    $_SESSION['Faculty'] = $Faculty;
    $_SESSION['Course'] = $Course;

} elseif ($candidatetype == 1) {
    $query = "SELECT * FROM tblsbrsstudents WHERE tblsbrsstudents.sbrsno=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($username));

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $RegNo = $row['sbrsno'];
        $surname = $row['surname'];
        $firstname = $row['firstname'];
        $othernames = $row['othernames'];
    }
    $CandName = $surname . " " . $firstname . " " . $othernames;
} elseif ($candidatetype == 3) {
    $query = "SELECT * FROM tblstudents WHERE tblstudents.matricnumber=?";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($username));

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $RegNo = $row['matricnumber'];
        $surname = $row['surname'];
        $firstname = $row['firstname'];
        $othernames = $row['othernames'];
    }
    $CandName = $surname . " " . $firstname . " " . $othernames;
}

$query1 = "SELECT candidateid FROM tblscheduledcandidate WHERE candidatetype=? and RegNo=?";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($candidatetype, $username));
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
$candidateid = $row1['candidateid'];

$query2 = "SELECT scheduleid FROM tblcandidatestudent WHERE candidateid=?";
$stmt2 = $dbh->prepare($query2);
$stmt2->execute(array($candidateid));
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

$scheduleid = $row2['scheduleid'];

$query3 = "SELECT venuename FROM tblvenue where venueid=(SELECT venueid FROM tblscheduling WHERE schedulingid=?)";
$stmt3 = $dbh->prepare($query3);
$stmt3->execute(array($scheduleid));
$row3 = $stmt3->fetch(PDO::FETCH_ASSOC);

$venuename = $row3['venuename'];


$query4 = "SELECT venueid FROM tblscheduling WHERE schedulingid=?";
$stmt4 = $dbh->prepare($query4);
$stmt4->execute(array($scheduleid));
$row4 = $stmt4->fetch(PDO::FETCH_ASSOC);

$venueid = $row4['venueid'];

$query5 = "SELECT centrename FROM tblcentres WHERE centreid=(SELECT centreid FROM tblvenue where venueid=?)";
$stmt5 = $dbh->prepare($query5);
$stmt5->execute(array($venueid));
$row5 = $stmt5->fetch(PDO::FETCH_ASSOC);

$centrename = $row5['centrename'];
?>

<div class="frm-cand-profile">
    <form class="style-frm">
        <fieldset>
            <legend>Candidate's Profile</legend>
            <div>
                <table style="width:100%">

                    <tr>
                        <td><b>Full Name:</b></td>
                        <td>
                            <?php echo $CandName; ?>
                        </td>
                        <td rowspan="3" colspan="2">
                            <div>
                                <img src="
                                <?php
                                if (file_exists("../../picts/$RegNo.jpg")) {
                                    echo siteUrl("picts/$RegNo.jpg");
                                } else {
                                    echo siteUrl("assets/img/photo.png");
                                }
                                ?>
                                     " style="width:150px; height:150px;" alt="image"/>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td><b>Reg No:</b></td>
                        <td>
                            <?php
                            echo $RegNo;
                            ?>
                        </td>

                    </tr>
                    <tr>
                        <td><b>Center:</b></td>
                        <td>
                            <?php
                            echo $centrename;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Venue:</b></td>
                        <td>
                            <?php
                            echo $venuename;
                            ?>
                        </td>
                        <td colspan="2"></td>

                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:center">
                            <button id="btn-bk-step2" class="btn btn-primary">Back</button>&nbsp;&nbsp;<button
                                    id="btn-nxt-step2" class="btn btn-primary">Restore Candidate
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
    </form>

</div>