<?php if(!isset($_SESSION)) session_start ();
require_once("../lib/globals.php");
require_once("../lib/security.php");
openConnection();
global $dbh;
authorize();

$qs = array();
$msg;
if (isset($_POST['submitted'])) {
    $dlvl=$_POST['dlvl'];
    $subjectid = ((isset($_POST['subj'])) ? ($_POST['subj']) : (0));
    $topicid = ((isset($_POST['topic'])) ? ($_POST['topic']) : (0));
    if ($subjectid != "") {

        $quest = trim($_POST['quest']);

        if ($quest != "") {

            $quest = trim($quest);
            $quest = trim($quest, '^');
            $questions = explode("^^", $quest);

// 4 each question
            for ($i = 0; $i < count($questions); $i++) {
                if(trim($questions[$i])=="")
                    continue;
                $rec = $questions[$i];
                $rec = trim($rec);
//echo $rec;
                $rec = explode("@@", $rec);
               // echo count($rec); exit;
                if($i==1){
                    //echo count($rec); exit;
                    //var_dump($rec);exit;
                }
                $que = addslashes($rec[0]);
                //$que=bin2hex($rec[0]);
                $query = "insert into tblquestionbank_temp (title, difficultylevel, questiontime, active, author, subjectid , topicid) values ('$que','$dlvl',now(), 1, '".$_SESSION['MEMBER_USERID']."',$subjectid, '$topicid')";
                //echo $query; exit;
                $stmt=$dbh->prepare($query);
                $stmt->execute();

                $query1 = "select questionbankid from tblquestionbank_temp where title='$que' && difficultylevel='$dlvl' && subjectid= '$subjectid' && active=1 && author='".$_SESSION['MEMBER_USERID']."'  order by questionbankid desc limit 1";
                $stmt1=$dbh->prepare($query1);
                $stmt1->execute();
                $questid = $stmt1->fetch(PDO::FETCH_ASSOC);
                $questid = $questid['questionbankid'];
                $qs[] = $questid;

                //4 each option
                for ($k = 1; $k < count($rec); $k++) {
                    $op1 = trim($rec[$k]);
                    //echo $op1."befroe"; 
                    if ($op1 != "") {
                        $op1=trim($op1);
                        //$opt_end = strlen($op1) - 1;
                        //$trim_opt_end = trim($opt_end);
                      
                        if ( strripos($op1, "&amp;#126;")>0) {

                            $crr = 1;
                            $op1 = substr_replace($op1, "", strripos($op1, "&amp;#126;"), 10);
                        } else {
                            $crr = 0;
                        }
                        // echo $op1; 
                        $op1=addslashes($op1);
                        $query2="insert into tblansweroptions_temp (test, questionbankid, correctness) values (?, ?, ? )";
                        $stmt2=$dbh->prepare($query2);
                        $stmt2->execute(array($op1,$questid,$crr));
                    }
                }
            }
        }
        else
            $msg = "Questions not uploaded";
    }
    else
        $msg = "No subject selected";
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Question Preview</title>
        <link type="text/css" href="../../assets/css/tconfig.css" rel="stylesheet"></link>
        <?php javascriptTurnedOff(); ?>
        <style>
            .editable{
                margin:10px;
            }
            .mgt{
                height: 100%;
            }
            #msg{
                text-decoration:blink;
                color:red;
                font-size: 20px;
                text-align: left;
            }
            .qpanel
            {
                font-family: arial;
                background-color: lightblue;
                margin-left: auto;
                margin-right: auto;
                margin-top:20px;
                padding:20px;
                border-style: solid;
                border-width: 1px;
                border-color:#cccccc;
                border-radius:3px;
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
                -o-border-radius:3px;
                -ms-border-radius:3px;
                width:700px;
            }

            .optpanel
            {
                background-color:#e7e7fd;
                margin-left: auto;
                margin-right: auto;
                border-style: solid;
                border-width: 1px;
                border-color:#cccccc;
                border-radius:3px;
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
                -o-border-radius:3px;
                -ms-border-radius:3px;
            }
            .optpanel ul
            {
                list-style-type:  upper-alpha;
            }
            .ok, .ok *
            {
                text-decoration: underline;
            }
        </style>
        
        <script type ="text/javascript" src ="../../assets/js/jquery-1.7.2.min.js"></script>
    </head>
    <body style="background-image:none; background-color:transparent;">
        <h1>Question Preview</h1>
        <?php
        if (isset($msg)) {
            echo $msg;
            exit;
        } else {
            echo "<form id='editq' action='commit_question.php' method ='POST' enctype='multipart/form-data'>";
            echo"<input type='hidden' name='subjectid' id='subjectid' value='$subjectid'/>";
            echo"<input type='hidden' name='topicid' id='topicid' value='$topicid'/>";
            echo"<input type='hidden' name='dlvl' id='dlvl' value='$dlvl'/>";
            $qs_str = trim(implode(",", $qs), ",");
//var_dump($qs_str); exit;
            $query3 = "select * from tblquestionbank_temp where questionbankid in ($qs_str)";
            $stmt3=$dbh->prepare($query3);
            $stmt3->execute();

            $c = 1;
            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $output = "<div class='qpanel'>";
                $qid = $row3['questionbankid'];
                $ques = $row3['title'];
                $output .="<div>$c. <div id='$qid'>".html_entity_decode(stripslashes($ques),ENT_QUOTES)." </div><input type='hidden' name='q" . $c . "' id='q$qid' value='$qid' /></div>";

                $opt_sql = "select * from tblansweroptions_temp where questionbankid = ?"; //echo $opt_sql;
                $stmt4=$dbh->prepare($opt_sql);
                $stmt4->execute(array($qid));

                if ($stmt4->rowCount() == 0)
                    continue;
                $output.="<div class='optpanel'><ul>";
                while ($opt_row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                    $optid = $opt_row['answerid'];
                    $opttxt = $opt_row['test'];
                    $optcurr = $opt_row['correctness'];
                    if ($optcurr == 1)
                        $output.="<li class='ok'>";
                    else
                        $output .= "<li>";
                    $output.="<span> ".html_entity_decode(stripslashes($opttxt),ENT_QUOTES)." </span><input type='hidden' name='op" . $c . "[]' class='q$qid' value='".stripslashes($opttxt)."' id='$optid' /><input type='hidden' name='curr".$c."[]' value='".$optcurr."' /></li>";
                }
                $output .="</ul></div>";
                $output .="</div>";
                $c++;
                echo $output;
            }
            echo"<input type='hidden' name='qcount' value='$c' id='qcount'/>";
            echo"<div style='width:100px; margin-left:auto; margin-right:auto;' ><input type='submit' name='submit' id='submit' value='Submit'/> </div>
                </form>";
        }
        ?>

        <script type="text/javascript">
          $('#submit2').live('click', function(event){
              $.ajax({
                  type:'POST',
                  url:'commit_question.php',
                  data:$("#editq").serialize()
              }).done(function(msg){
                  if(msg==-2)
                      {
                         alert("Server Error!");
                      }
                      else if(msg==0)
                          {
                              alert("Operation was not successfull.");
                          }
                          else
                              {
                                  alert("Operation was successfull");
                                  window.close();
                              }
              });
              return false;
          });
          
            $(".qedit").live('click',function(event){
                var inp="";
                var qid=$(this).prev().attr('id');
                var qtxt=$("#"+qid).html();
                inp += "<h2>Question:</h2><div class='editable'><textarea id='tm-q'  class='qt' data-toedit='"+qid+"'>"+qtxt+"</textarea></div><h2>Options:</h2>";
                $(".q"+qid).each(function(){
                    var optvl=$(this).val();
                    var optid=$(this).attr("id");
                    inp += "<div class='editable'><textarea class='opt' data-toedit='"+optid+"'>"+optvl+"</textarea></div>";
                });
            
                $("<div>"+inp+"</div>").dialog({title:'Modify Question', modal:true, width:700, height:450, buttons:{
                        'Apply':function(){
                            var tchg= $("#tm-q").attr('data-toedit');
                            //alert(tchg);
                            //alert($("#tm-q").text());
                            $("#q"+tchg).val($("#tm-q").text());
                            $("#"+tchg).html($("#tm-q").text());
                        }
                    }
                });
            
            });
        </script>
    </body>
</html>