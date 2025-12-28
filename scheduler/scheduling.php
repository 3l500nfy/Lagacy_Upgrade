<?php
if (!isset($_SESSION))
    session_start();
$tid = $_SESSION['tid'];
require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();

?>

<div class="span12">

    <form action="schedule_summary.php" method="POST" class="style-frm">
        <table class="style-tbl" style="width: 1000px">
            <tr>
                <td><b>Preferred Centre</b></td>
                <td>
                    <select name="centre" id="centre">
                        <option value ="">--Select Center--</option>
                        <?php
                        $query = "SELECT centreid FROM tblvenue WHERE venueid IN (SELECT venueid FROM tblscheduling WHERE testid= $tid)";
                        $stmt = $dbh->prepare($query);
                        $result = $stmt->execute();
                        $numrows = $stmt->rowCount();

                        if ($numrows > 0) {
                            while ($ctid = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $cid = $ctid['centreid'];
                                $query = "SELECT * FROM tblcentres  where centreid='$cid'";
                                $stmt = $dbh->prepare($query);
                                $result = $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $centreid = $row['centreid'];
                                    $name = $row['name'];
                                    echo "<option value ='$centreid'>$name</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </td>
                <td><b>Preferred Venue</b></td>
                <td>
                    <select name="venue" id="venue">
                        <option value ="">--Select Venue--</option>

                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                  
                    <label><b>PUTME Subjects:</b></label>
                    <table class="style-tbl" style="width: 1000px">
                        <tr>
                            <td>1. English Language</td>
                            <td>2. <select name="subj2" id="subj2" >

                                    <?php
                                    $sub2=$_SESSION['Subj2'];
                                    $query="SELECT subjectid,subjectname FROM tblsubject where subjectcode='$sub2'";
                                    $stmt = $dbh->prepare($query);
                                    $result = $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $sub_id= $row['subjectid'];
                                    $sub_nm= $row['subjectname'];
                                    echo "<option value='$sub_id' readonly>$sub_nm</option>";
                                    ?>
                                </select>
                            </td>
                            <td>3  <select name="subj3" id="subj3">
                                    <?php
                                   $sub3=$_SESSION['Subj3'];
                                    $query="SELECT subjectid,subjectname FROM tblsubject where subjectcode='$sub3'";
                                    $stmt = $dbh->prepare($query);
                                    $result = $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $sub_id= $row['subjectid'];
                                    $sub_nm= $row['subjectname'];
                                    echo "<option value='$sub_id' readonly>$sub_nm</option>";
                                    ?>

                                </select></td>
                            <td>4. <select name="subj4" id="subj4">

                                    <?php
                                    $sub4=$_SESSION['Subj4'];
                                    $query="SELECT subjectid,subjectname FROM tblsubject where subjectcode='$sub4'";
                                    $stmt = $dbh->prepare($query);
                                    $result = $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $sub_id= $row['subjectid'];
                                    $sub_nm= $row['subjectname'];
                                    echo "<option value='$sub_id' readonly>$sub_nm</option>";
                                    ?>

                                </select></td>
                        </tr>
                    </table>
                    
                    
                </td>

            </tr>
            <tr>

                <td colspan="4">
            <center>

                <input type ="submit" name ="continue_btn" value="Submit" class ="btn btn-primary" id ="continue_btn2" />
            </center>
            </td>
            </tr>   

        </table>
    </form>

</div>