<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("subj_reg_func.php");
openConnection(true);
$exam = $_POST['exam'];
$sess = $_POST['sess'];
$sem = $_POST['sem'];
if ($exam == '1') {
    $sem = 0;
}
$query = "select * from tbltestconfig where session=? && semester=? && testcodeid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($sess,$sem,$exam));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($stmt->rowCount() == 0) {
    echo"<div id='subseldiv'><span> No test configuration available for the above criteria.</span></div>";
    exit;
}

$testid = $row['testid'];
?>
<div id="subseldiv"><form action="index.php" method="POST">
        <?php
        echo"<input type='hidden' name='testid' value='$testid' />";
        $query2 = "select * from tblsubject";
        $stmt1=$dbh->prepare($query2);
        $stmt1->execute();

        $category = $test['testcategory'];
        ?>
        <div id="subjdiv" >
            <?php if ($category == 'Single Subject') { if(doneRegistered($testid)) echo"<b>A subject has already been registered!</b>"; else{ ?>
                <table style="margin-right: auto; margin-left: auto;"><tr><td>Subject:</td><td><select style="margin-left: 10px;" name="subj0" id="subj" class="subjs1"><option value=''>Select a subject</option><?php
                while ($subjects = $stmt1->fetch(PDO::FETCH_ASSOC))
                    if (!isRegistered($testid, $subjects['subjectid']))
                        echo"<option value='" . $subjects['subjectid'] . "'>" . $subjects['subjectname'] . "</option>";
                ?></select></td></tr></table>
                                <?php echo"<input type='hidden' name='subcount' id='subcount' value='1'/>"; }
                            } else { ?>
                <div id="subjlist" style="min-height: 50px; max-height: 150px; overflow: auto; padding:15px;">
                    <table class="striped">
                        <?php
                        $i = 0;
                        while ($subjects = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                            if (!isRegistered($testid, $subjects['subjectid'])) {
                                echo"<tr><td style='padding:5px; vertical-align:center'><input type='checkbox' name='subj$i' id='subj$i' class='subjs' value='" . $subjects['subjectid'] . "' /></td><td style='vertical-align:center'>" . $subjects['subjectname'] . "</td></tr>";
                                $i++;
                            }
                        }
                        echo"<input type='hidden' name='subcount' id='subcount' value='$i'/>";
                        ?>
                    </table>
                </div>
                <?php
            }
            echo"<div style='margin-left:auto; margin-right:auto; width:300px;'><br /><input type='submit' name='registersubject' id='registersubject' style='display:none;' value='Register Subject(s)'/></div>";
            ?>
        </div><hr />
    </form>
    <script type="text/javascript">
$('.striped tr:even').css('backgroundColor','#dcdcdc'); 
$('.striped td').css('verticalAlign','bottom'); 
$('.striped').css('width','295px');
$('input[type=submit]').button();

</script>
</div>