<form id="frm3" class="style-frm">
    <?php
    if (!isset($_SESSION))
        session_start();
    require_once("../../lib/globals.php");
    require_once("invigilator_functions.php");
    require_once("../../lib/cbt_func.php");
    global $dbh;
    openConnection();

    $candidatetype = $_POST['candidatetype_inc'];
    $examtype = $_POST['testid_inc'];
    $username = $_POST['username_inc'];

    if ($candidatetype == 1) {
        $query = "SELECT * from tbljamb WHERE tbljamb.RegNo=?";
        $stmt=$dbh->prepare($query);
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
    
    } elseif ($candidatetype == 2) {

        $query = "SELECT * FROM tblsbrsstudents WHERE tblsbrsstudents.sbrsno=?";
        $stmt=$dbh->prepare($query);
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
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($username));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $RegNo = $row['matricnumber'];
            $surname = $row['surname'];
            $firstname = $row['firstname'];
            $othernames = $row['othernames'];
        }
        $CandName = $surname . " " . $firstname . " " . $othernames;
    }

    $query1 = "SELECT elapsed, candidateid FROM tbltimecontrol where candidateid=(SELECT candidateid from tblscheduledcandidate where RegNo=? and candidatetype=? limit 1)";
    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($RegNo,$candidatetype));
    $row1=$stmt1->fetch(PDO::FETCH_ASSOC);
    $elapsedtime_secs = $row1['elapsed'];
    $candidateid = $row1['candidateid'];
    $elapsedtime=floor($elapsedtime_secs/60);

    $query2 = "SELECT scheduleid FROM tblcandidatestudent WHERE candidateid=$candidateid && scheduleid in (select schedulingid from tblscheduling where testid = $examtype)";
    $stmt2=$dbh->prepare($query2);
    $stmt2->execute();
    $row2=$stmt2->fetch(PDO::FETCH_ASSOC);

    $scheduleid =$row2['scheduleid'];

    $test_param = get_test_config_param_as_array($examtype);
    $schedule_param = get_schedule_config_param_as_array($scheduleid);

    $venuename = $schedule_param['venuename'];
    $venueid = $schedule_param['venueid'];
     $centrename = $schedule_param['centername'];
       $duration = $test_param['duration'];
       ?>
    <fieldset><legend>Candidate's Time Usage</legend>
    <div class="frm-cand-profile">
        <div>
            <table style="width:100%" class="table table-bordered">
                <tr>
                    <td><b>Full Name:</b></td>
                    <td>
                        <?php echo $CandName; ?>
                    </td>
                    <td rowspan="3" colspan="2">
                        <div>
                            <?php
                            echo "<img src='";
                            echo siteUrl('picts/');
                            echo $RegNo . ".jpg" . "'" . " alt='image' />";
                            ?>
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
                    <td colspan="4"><b>Reduce Elapsed Time:</b> <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input  type="hidden" name="duration" id="duration" value=<?php echo $duration; ?> />
                        <input type="hidden" name="candidtype" id="candidtype" value=<?php echo $candidatetype; ?> />
                        <input type="hidden" name="candid" id="candid" value=<?php echo $candidateid; ?> />
                        <input type="hidden" name="usern" id="usern" value=<?php echo $username; ?> />
                        <input type="hidden" name="examtyp" id="examtyp" value=<?php echo $examtype; ?> />
                        <input type="hidden" name="timespent" id="timespent" value=<?php echo $elapsedtime; ?> />
                        <input type="hidden" name="initialtimespent" id="initialtimespent" value=<?php echo $elapsedtime; ?> />
                        <input type="hidden" name="initialtimespentsec" id="initialtimespentsec" value=<?php echo $elapsedtime_secs; ?> />

                        <span id="el">Elapse: <?php echo $elapsedtime . " " . "mins"; ?></span> 
                        <div id="slider" style="display:inline-block; margin:5px;width:200px;"></div>
                        <span id="rem">Remaining:<?php echo ($duration - $elapsedtime ) . " " . "mins"; ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center"><button id="btn-bk-step3" class="btn btn-primary">Back</button>&nbsp;&nbsp;<button id="btn-nxt-step3" class="btn btn-primary">Save Changes</button></td>
                </tr>
            </table>
        </div>
    </div>
    </fieldset>
</form>