<!DOCTYPE html>
<?php
require_once("../../lib/globals.php");
openConnection();

$msg;

if (isset($_POST['submitted'])) {
    $subject = $_POST['subj'];
    if ($subject != "") {
        $topic = $_POST['topic'];
        $topic = trim($topic);
        if ($topic != "") {
            $quest = $_POST['quest'];
            $quest = trim($quest);
            if ($quest != "") {
                $sql = "select * from tblquestionbank where subjectid=? && title=?";
                $stmt=$dbh->prepare($query);
                $stmt->execute(array($subject,$quest));

                if ($query && $stmt->rowCount() == 0) {
                    $opt1 = $_POST['opt1'];
                    $opt2 = $_POST['opt2'];
                    $opt3 = $_POST['opt3'];
                    $opt4 = $_POST['opt4'];
                    $opt1 = trim($opt1);
                    $opt2 = trim($opt2);
                    $opt3 = trim($opt3);
                    $opt4 = trim($opt4);
                    if ($opt1 != "" && $opt2 != "" && $opt3 != "" && $opt4 != "") {
                        if (isset($_POST["corr"]) && $_POST['corr'] != "") {
                            $corr = $_POST['corr'];
                            $query = "insert into tblquestionbank (title, difficultylevel, questiontime, active, author, subjectid, topic) values (?,'simple',now(), 'true', 'Ibrahim', ?, ?)";
                            $stmt=$dbh->prepare($query);
                            $stmt->execute(array($quest,$subject,$topic));

                            $query1 = "select questionbankid from tblquestionbank where title=? && difficultylevel='simple' && active=1 && subjectid=? && topic=?";
                            $stmt1=$dbh->prepare($query1);
                            $stmt1->execute(array($quest,$subject,$topic));
                            $row=$stmt1->fetch(PDO::FETCH_ASSOC);
                            $row = $questid[0];

                            $query2 = "insert into tblansweroptions (test, questionbankid, correctness) values (?,?," . (($corr == 'opt1') ? ("'1'") : ("'0'")) . " ),(?,?," . (($corr == 'opt2') ? ("'1'") : ("'0'")) . " ),(?,?," . (($corr == 'opt3') ? ("'1'") : ("'0'")) . " ),(?,?," . (($corr == 'opt4') ? ("'1'") : ("'0'")) . " )";
                            $stmt2=$dbh->prepare($query2);
                            $stmt2->execute(array($opt1,$questid,$opt2,$questid,$opt3,$questid,$opt4,$questid));

                            $msg = "Question was created successfully";
                            unset($_POST);
                        }
                        else
                            $msg = "No Correction option indicated";
                    }
                    else
                        $msg = "Invalid options";
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
?>

<html lang="en">
    <head>
        <title>Question Authoring</title>
        <link type="text/css" href="../../assets/css/tconfig.css" rel="stylesheet"></link>
        <?php javascriptTurnedOff(); ?>
        <style>
            .mgt{
                height: 100%;
            }
        </style>
        <?php require_once("../../partials/cssimports.php") ?>
        <script type ="text/javascript" src ="../../assets/js/jquery-1.7.2.min.js"></script>
        <script type ="text/javascript" src ="../../assets/js/jquery-ui-1.7.3.custom.min.js"></script>
        <script type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript" src="../../jscripts/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
    </head>
    <body>
        <div id="container" class="container">
            <div class="page-header">
                <h1>Question Authoring</h1>
            </div>
            <div id=" " class="row">

                <div class="span12"> 
                    <table class="table table-striped">
                        <tr>
                            <td>
                        <center>
                            <h2>Fill the form below to register new question</h2>
                        </center>

                        <form action="index.php" method="post">
                            <span id="msg">
                                <?php
                                if (isset($msg) && $msg != "")
                                    echo $msg;
                                ?>
                            </span>
                            <br />
                            <table class="table table-bordered">
                                <tr>
                                    <td> <b>&nbsp;&nbsp; Subject : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                                        <select name="subj" id="subj">
                                            <option value="">--select subject--</option>
                                            <?php
                                            $query = "select * from tblsubject";
                                            $stmt=$dbh->prepare($query);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                                                echo"<option value='" . $row['subjectid'] . "' " . ((isset($_POST['subj']) && $_POST['subj'] == $row['subjectid']) ? ("selected='selected'") : ("")) . ">" . $row['subjectname'] . "</option>";
                                            ?>
                                        </select>  </td> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                    <td> <b>Topic:&nbsp;&nbsp;&nbsp;</b>
                                        <input type="text" name="topic" <?php if (isset($_POST['topic'])) echo "value='" . $_POST['topic'] . "'"; ?>/>

                                    </td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<td><a href="javascript:void(0);"  id="topic_manager">Manage Topics</a></td>
                                <tr>
                                    <td><b>Authoring Mode:</b>
                                        <select name="quest_mode" id="quest_mode">
                                            <option>--select question mode--</option>
                                            <option value="upload">Upload Questions</option>
                                            <option value="onebyone">Enter Questions one by one</option>
                                        </select></td>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                                    <td colspan="3">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div id="question_authoring">
                                        </div></td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                <center>
                                    <input type="submit" id="submit" name="submitted" class="btn btn-primary" value="Register"/>
                                </center>
                                </td>
                                </tr>
                            </table>
                        </form>
                        </td>
                        </tr>
                    </table>
                </div>
                <script>
                    $(document).ready(function(){
                        $("#quest_mode").live("change", function(){ 
                            if ($(this).val()=="upload")
                                $("#question_authoring").load("upload_questions.php");
                            else
                                $("#question_authoring").load("enterinq_question.php"); 
                        });

                    });
                 
                    $('#btn_topic').live('click', function(event){
                        $.ajax({
                            type:'Post',
                            url:'manage_topic1.php',
                            data:{subj:$("#subj").val(), topic:$("#add_topic").val(), addsubmit:"true"}
                        }).done(function(msg){
                           
                            alert(msg);
                        });
                        return false;
                    });
                    
                    $("#topic_manager").live("click", function (event){
                        $.ajax({
                    
                            type:"post",
                            url:"manage_topic.php"

                        }).done(function(e){
                            
                            $("<div id='mydialog'>"+e+"</div>").dialog({
                    
                                title: "Topic Manager",
                                width: 600,
                                height:300,
                                modal: true,
                                close:function(){$(this).empty().remove();}
                            });
                        });
                
                    });
                    $("#addtopic").live("click", function (event){
                        $.ajax({
                    
                            type:"post",
                            url:"manage_topic1.php"
                        
                        }).done(function(e){
                            //alert("");
                            //$(".mgt").remove();
                            $("#mydialog").html(e);
                        });
                    });
                    
                    $("#edittopic").live("click", function (event){
                        $.ajax({
                    
                            type:"post",
                            url:"manage_topic2.php"
                    
                        
                        }).done(function(e){
                            //alert("");
                            $("#mydialog").html(e);

                        });
                
                    });
                    $("#deletetopic").live("click", function (event){
                        $.ajax({
                    
                            type:"post",
                            url:"manage_topic3.php"
                    
                        
                        }).done(function(e){
                            //alert("");
                            $("#mydialog").html(e);
                        });
                
                    });
            
                </script>
                </body>
                </html>