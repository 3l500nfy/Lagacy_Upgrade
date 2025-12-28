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
if(!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin")))
    
{
    header("Location:".siteUrl("403.php"));
    exit();
}

$msg;

if (isset($_POST['submitted'])) {
    $dlvl=$_POST['dlvl'];
    $subject = $_POST['subj'];
    if ($subject != "") {
        $topic = $_POST['topic'];
        $topic = trim($topic);
        if ($topic != "") {
            $quest = $_POST['quest'];
            $quest = clean($quest);
            if ($quest != "") {
                $query = "select * from tblquestionbank where subjectid=? && title=?";
                $stmt=$dbh->prepare($query);
                $stmt->execute(array($subject,$quest));

                if ($stmt->rowCount() == 0) {
                    $optcount = 5;
                    $opts = array();
                    $corrs = array();
                    $cors=(isset($_POST['corr']))?($_POST['corr']):(array());
                    for ($o = 1; $o <= $optcount; $o++) {
                        $ostr = "opt" . $o;
                        
                        if (isset($_POST[$ostr]) && clean($_POST[$ostr]) != "") {
                            $opts[] = clean($_POST[$ostr]);
                            if(in_array($o, $cors))
                            {
                                $corrs[]=1;
                            }
                            else
                                $corrs[]=0;
                        }
                    }

                    if (count($opts) > 1) {
                        if (in_array(1, $corrs)) {

                            $topicsql = "select topicname from tbltopics where topicid=?";
                            $stmt=$dbh->prepare($query);
                            $stmt->execute(array($topic));

                            $tpcrow = $stmt->fetch(PDO::FETCH_ASSOC);
                            $tpcnm = $tpcrow['topicname'];

                            $query1 = "insert into tblquestionbank (title, difficultylevel, questiontime, active, author, subjectid, topic, topicid) values (?,?, now(), 1, '" . $_SESSION['MEMBER_USERID'] . "', ?, ?, ?)";
                            $stmt1=$dbh->prepare($query1);
                            $stmt1->execute(array($quest,$dlvl,$subject,$tpcnm,$topic));

                            $query2 = "select questionbankid from tblquestionbank where title=? && difficultylevel='simple' && active=1 && subjectid=? && author='" . $_SESSION['MEMBER_USERID'] . "'";
                            $stmt2=$dbh->prepare($query2);
                            $stmt2->execute(array($quest,$subject));

                            $questid = $stmt2->fetch(PDO::FETCH_ASSOC);;
                            $questid = $questid[0];
                            for ($i = 0; $i < count($opts); $i++) {

                                $opt = $opts[$i];
                                $opt = trim($opt);
                                $corr = $corrs[$i];

                                $query3 = "insert into tblansweroptions (test, questionbankid, correctness) values (?, ?, ?)";
                                $stmt3=$dbh->prepare($query3);
                                $stmt3->execute(array($opt,$questid,$corr));

                                $msg = "<span id='msg-success'>Question was created successfully</span>";
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
        else
            $msg = "No topic entered";
    }
    else
        $msg = "No subject selected";
}
else 
    $msg="";
?>
<html>
<body style="color:orange; padding:0px; margin:0px; min-height: 0px; font-family: arial; text-align: center; font-size: 15pt;">
  <?php echo $msg; ?>  
            <script type ="text/javascript" src ="../assets/js/jquery-1.7.2.min.js"></script>

    <script type="text/javascript">
        window.top.scrollTo(window.top.scrollY, 0);
        //$(document).scrollTop(0);
        if($("#msg-success").size()==1)
            {
                $("#quest_mode", $(top.document)).trigger("change");
            }
    </script>
</body>
</html>