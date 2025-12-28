<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
require_once("../../lib/candid_scheduling_func.php");
openConnection();
$tid = $_SESSION['tid'];
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
                        $query = "SELECT centreid FROM tblvenue WHERE venueid IN (SELECT venueid FROM tblscheduling WHERE testid=?)";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute($tid);
                        $numrows = $stmt->rowCount();
                        if ($numrows > 0) {
                            while ($ctid = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $centreid = $ctid['centreid'];
                                $query = "SELECT * FROM tblcentres  WHERE centreid=?";
                                $stmt = $dbh->prepare($query);
                                $stmt->execute($centreid);
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
                    <?php
                    // $s=  get_subject_combination_as_array($tid);
                    //$s= get_subject_selection_as_array($schdid, $cid);
                    $result_subjid= array();
                    $query = "SELECT subjectid FROM tbltestsubject WHERE testid=?";                  
                    $stmt = $dbh->prepare($query);
                    $stmt->execute($tid);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $result_subjid[] = $row['subjectid'];
                        
                    }
                    
                    
                    ?>
                    <label><b>Deselect one subject you do not want to offer. Make sure five(5) subjects are selected. </b></label>
                    <table class="style-tbl" style="width: 1000px" >
                        <tr>
                            <td>
                                <input type="checkbox" name="subj[]" value="<?php echo $result_subjid[0]; ?>" checked="checked" id="subj1"/>
                            </td>
                            <td>
                                <label><?php echo get_subject_name($result_subjid[0]); ?></label>
                            </td>

                            <td>
                                <input type="checkbox" name="subj[]" value="<?php echo $result_subjid[1]; ?>" checked="checked" id="subj2"/>
                            </td>
                            <td>
                                <label><?php echo get_subject_name($result_subjid[1]); ?></label>
                            </td>
                            <td>
                                <input type="checkbox" name="subj[]" value="<?php echo $result_subjid[2]; ?>" id="subj3" checked="checked"/>
                            </td>
                            <td>
                                <label><?php echo get_subject_name($result_subjid[2]); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" name="subj[]" value="<?php echo $result_subjid[3]; ?>" id="subj4" checked="checked"/>
                            </td>
                            <td>
                                <label><?php echo get_subject_name($result_subjid[3]); ?></label>
                            </td>
                            <td>
                                <input type="checkbox" name="subj[]" value="<?php echo $result_subjid[4]; ?>" id="subj5" checked="checked"/>
                            </td>
                            <td>
                                <label><?php echo get_subject_name($result_subjid[4]); ?></label>
                            </td>
                            <td>
                                <input type="checkbox" name="subj[]" value="<?php echo $result_subjid[5]; ?>" id="subj6" checked="checked"/>
                            </td>
                            <td>
                                <label><?php echo get_subject_name($result_subjid[5]); ?></label>
                            </td>
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