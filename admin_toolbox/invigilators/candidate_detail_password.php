<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
openConnection();
global $dbh;
$candidatetype = $_POST['candidatetype'];
$username = $_POST['username'];


if ($candidatetype == 1) {
    $query = "SELECT * from tbljamb WHERE tbljamb.RegNo=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($username));

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $RegNo = $row['RegNo'];
            $CandName = $row['CandName'];
            $loginpassword = $row['StateOfOrigin'];
        }
    } else {
        echo '<b style=' . 'color:red;' . '>Username does not exist as a Post UTME candidate!!!</b>';
        exit();
    }
} elseif ($candidatetype == 2) {
    $query = "SELECT * FROM tblsbrsstudents WHERE tblsbrsstudents.sbrsno=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($username));

    if ($stmt->rowCount() > 0) {
        while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $RegNo = $row1['sbrsno'];
            $surname = $row1['surname'];
            $firstname = $row1['firstname'];
            $othernames = $row1['othernames'];
            $loginpassword = $row1['loginpassword'];
        }
        $CandName = $surname . " " . $firstname . " " . $othernames;
    } else {
        echo '<b style=' . 'color:red;' . '>Username does not exist as a SBRS candidate!!!</b>';
        exit();
    }
} elseif ($candidatetype == 3) {
    $query1 = "SELECT * FROM tblstudents WHERE tblstudents.matricnumber=?";
    $stmt=$dbh->prepare($query1);
    $stmt->execute(array($username));

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $RegNo = $row['matricnumber'];
            $surname = $row['surname'];
            $firstname = $row['firstname'];
            $othernames = $row['othernames'];
            $loginpassword = $row['loginpassword'];
        }
        $CandName = $surname . " " . $firstname . " " . $othernames;
    } else {
        echo '<b style=' . 'color:red;' . '>Username does not exist as a Regular candidate!!!</b>';
        exit();
    }
}
?>

<div class="frm-cand-profile">
    <form class="style-frm">
        <fieldset><legend>Candidate's Profile</legend>
            <div>
                <table style="width:100%" >

                    <tr>
                        <td><b>Full Name:</b></td>
                        <td><?php echo $CandName; ?></td>
                        <td rowspan="3" colspan="2">
                            <div>
                                <img src="
                                <?php
                                if (file_exists('../../picts/$RegNo.jpg')) {

                                    echo siteUrl('picts/$RegNo.jpg');
                                } else {
                                    echo siteUrl('assets/img/photo.png');
                                }
                                ?>
                                     " alt="image" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Reg. No:</b></td>
                        <td><?php echo $RegNo; ?></td>
                    </tr>
                    <tr>
                        <td><b>Password:</b></td>
                        <td><?php echo $loginpassword; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:center"><button id="btn-bk-step2" class="btn btn-primary">Back</button></td>
                    </tr>
                </table>
            </div>
        </fieldset>
    </form>
</div>