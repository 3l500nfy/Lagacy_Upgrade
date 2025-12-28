<?php
if (!isset($_SESSION))
    session_start();
?>
<!DOCTYPE html>
<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("authoring_functions.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin")) && !has_roles(array("Test Compositor"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}

$msg = "";
if (isset($_POST['submit_edit'])) {
    $subject = $_POST['sbj2'];
    $qid = $_POST['qid2'];
    $topic = $_POST['edit_topic'];
    $topic = trim($topic);
    $savemode = $_POST['savemode'];
    $difflvl = $_POST['dlvl'];
    $qstat = $_POST['edit_status'];
    // Convert 'true'/'false' string to integer 1/0 for MySQL tinyint column
    $qstat = ($qstat === 'true' || $qstat === true) ? 1 : 0;
    $quest = $_POST['edit_q'];
    $quest = addslashes($quest);
    if ($quest != "") {
        if ($savemode == "new")
            $query = "select * from tblquestionbank where subjectid=? && title=?";
        else
            $query = "select * from tblquestionbank where subjectid=? && title=? && questionbankid <> ?";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($subject,$quest,$qid));

        if ($query && $stmt->rwoCount() == 0) {
            $optcount = 5;
            $old_opts = array();
            $new_opts = array();
            $old_opt_id = array();
            $opt_to_del = array();
            $corrs = array();
            $cors = (isset($_POST['corr'])) ? ($_POST['corr']) : (array());
            for ($o = 1; $o <= $optcount; $o++) {
                $ostr = "opt" . $o;
                $nostr = "optt" . $o;
                $oldoptid = "opti" . $o;

                if (isset($_POST[$ostr]) && clean($_POST[$ostr]) != "") {
                    $old_opts[] = $_POST[$ostr];
                    $old_opt_id[] = clean($_POST[$oldoptid]);
                    if (in_array($o, $cors)) {
                        $corrs[] = 1;
                    }
                    else
                        $corrs[] = 0;
                }
                else
                if (isset($_POST[$oldoptid])) {
                    $opt_to_del[] = clean($_POST[$oldoptid]);
                }
                if (isset($_POST[$nostr]) && clean($_POST[$nostr]) != "") {
                    $new_opts[] = $_POST[$nostr];
                    if (in_array($o, $cors)) {
                        $corrs[] = 1;
                    }
                    else
                        $corrs[] = 0;
                }
            }

            if (count($old_opts) > 1 || count($new_opts) > 1) {
                if (in_array(1, $corrs)) {
                    $query = "select topicname from tbltopics where topicid=?";
                    $stmt=$dbh->prepare($query);
                    $stmt->execute(array($topic));
                   $row=$stmt->fetch(PDO::FETCH_ASSOC);
                    $tpcnm = $row['topicname'];
                    if ($savemode == 'new')
                        $query = "insert into tblquestionbank (title, difficultylevel, questiontime, active, author, subjectid, topic, topicid) values (?,?, now(), ?, '" . $_SESSION['MEMBER_USERID'] . "', ?, ?, ?)";
                    else
                        $query = "update tblquestionbank set title=?, difficultylevel=?, questiontime=now(), active=?, author='" . $_SESSION['MEMBER_USERID'] . "', subjectid=?, topic=?, topicid=? where questionbankid='$qid'";
                    $stmt=$dbh->prepare($query);
                    $stmt->execute(array($quest,$difflvl,$qstat,$subject,$tpcnm,$topic));

                    if ($savemode == 'new') {
                        $sql2 = "select questionbankid from tblquestionbank where title=? && difficultylevel=? && active=? && subjectid=? && author='" . $_SESSION['MEMBER_USERID'] . "'";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute(array($quest,$difflvl,$qstat,$subject));
                        $row=$stmt->fetch(PDO::FETCH_ASSOC);

                        $questid = $row[0];
                    }
                    else
                        $questid = $qid;

                    for ($i = 0; $i < count($old_opts); $i++) {

                        $opt = $old_opts[$i];
                        $opt = trim($opt);
                        $opt=addslashes($opt);
                        $corr = $corrs[$i];
                        $optid = $old_opt_id[$i];

                        if ($savemode == "new") {
                            $query = "insert tblansweroptions (test, questionbankid, correctness) values (?, ?, ?)";
                            $stmt = $dbh->prepare($query);
                            $stmt->execute(array($opt, $questid, $corr));
                        }else
                            $query = "update tblansweroptions set test=?, correctness=? where answerid=?";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute(array($opt,$corr,$optid));

                        if ($savemode == "new")
                            $msg = "Question was created successfully.";
                        else
                            $msg = "Question was updated successfully.";
                        unset($_POST);
                    }

                    $r = $i;
                    for ($i = 0; $i < count($new_opts); $i++) {

                        $opt = $new_opts[$i];
                        $opt = addslashes($opt);
                        $corr = $corrs[$r];

                        $query = "insert into tblansweroptions (test, questionbankid, correctness) values (?, ?, ?)";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute(array($opt,$questid,$corr));
                        $r++;

                        if ($savemode == "new")
                            $msg = "Question was created successfully.";
                        else
                            $msg = "Question was updated successfully.";
                        unset($_POST);
                    }

                    for ($i = 0; $i < count($opt_to_del); $i++) {

                        $opt = $opt_to_del[$i];
                        $opt = trim($opt);
                        $opt=clean($opt);

                        $query = "delete from tblansweroptions where answerid='$opt'";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute(array($opt));

                        if ($savemode == "new")
                            $msg = "Question was created successfully.";
                        else
                            $msg = "Question was updated successfully.";
                        unset($_POST);
                    }
                }
                else
                    $msg = "No correct option indicated";
            }
            else
                $msg = "Invalid options supplied";
        }
        else
            $msg = "Question already exists";
    }
    else
        $msg = "No question typed";
}
?>
<html>
    <body style="color:orange; padding:0px; margin:0px; min-height: 0px; font-family: arial; text-align: center; font-size: 15pt;">
        <?php echo $msg; ?>  
        <script type="text/javascript">
<?php
if ($msg != "") {
    
    echo "window.top.load_question_list();";
}
?>
        </script>
    </body>
</html>

