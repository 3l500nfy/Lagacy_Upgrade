<?php if(!isset($_SESSION)) session_start ();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/html_sanitizer.php");
openConnection();
global $dbh;
if (isset($_POST['submit'])) {

    $subjectid = ((isset($_POST['subjectid'])) ? ($_POST['subjectid']) : (0));
    $topicid = ((isset($_POST['topicid'])) ? ($_POST['topicid']) : (0));
    $difflvl=((isset($_POST['dlvl'])) ? ($_POST['dlvl']) : ("simple"));
     $qcount = $_POST['qcount'];
   /* try {
        $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, array(
                    PDO::ATTR_PERSISTENT => false
                ));
    } catch (PDOException $e) {

        echo "Error in database connection.";
        exit();
    }*/
    try {
        $dbh->beginTransaction();
        for ($i = 1; $i <= $qcount; $i++) {
            if (isset($_POST['q' . $i])) {
                $question = $_POST['q' . $i];
                $question=clean($question);
                // Sanitize HTML content before storage to prevent XSS
                $question = sanitizeHtmlContent($question);
                $sql = "insert into tblquestionbank (title, difficultylevel, questiontime, active, author, subjectid , topicid) values ((select title from tblquestionbank_temp where questionbankid=?),?,now(), 1, '".$_SESSION['MEMBER_USERID']."',?, ?)";
                $query=$dbh->prepare($sql);
                $query=$query->execute(array($question,$difflvl,$subjectid,$topicid));
                //$query = $dbh->exec($sql);
                if (!$query) {
                    $dbh->rollBack();
                    echo "Server error [001]";
                    exit();
                }

                $sql2 = "select questionbankid from tblquestionbank where title=(select title from tblquestionbank_temp where questionbankid='$question') && difficultylevel='$difflvl' && subjectid= '$subjectid' && topicid='$topicid' && author='".$_SESSION['MEMBER_USERID']."' && active=1 order by questionbankid desc limit 1";
                $query2=$dbh->prepare($sql2);
                $exec=$query2->execute();
               // $query2 = $dbh->query($sql2);
                if (!$exec) {
                    $dbh->rollBack();
                    echo "Server error [002]";
                    exit();
                }
                $row = $query2->fetch(PDO::FETCH_ASSOC);
                $qid = $row['questionbankid'];
                $query2->closeCursor();

                if (isset($_POST['op' . $i])) {
                    $options = $_POST['op' . $i];
                    $correctness = $_POST['curr' . $i];
                    $j = 0;
                    foreach ($options as $option) {
                        $correct = $correctness[$j];
                        $j++;
                        $option=clean($option);
                        // Sanitize HTML content before storage to prevent XSS
                        $option = sanitizeHtmlContent($option);
                        $sql3 = "insert into tblansweroptions (test, questionbankid, correctness) values ('$option', $qid, '$correct' )";
                        $query3=$dbh->prepare($sql3);
                        $query3=$query3->execute();
                        //$query3 = $dbh->exec($sql3);
                        if (!$query3) {
                            $dbh->rollBack();
                            echo " $sql3 Server error [003]";
                            exit();
                        }
                    }
                } else {
                    $dbh->rollBack();
                    echo "Server error [004]";
                    exit();
                }
            }
        }
        echo"Completed successfully. Click <a href='javascript:window.close()'>here</a> to close window";
        $dbh->commit();
    } catch (PDOException $e) {
        $dbh->rollBack();
        echo "Server error [005]";
        exit();
    }
}
?>