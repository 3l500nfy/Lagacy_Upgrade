<!DOCTYPE html>



<?php
require_once("../../../lib/globals.php");
openConnection();
?>

<?php
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
                $query = "SELECT * FROM tblquestionbank WHERE subjectid=? && title='?'";
                $stmt = $dbh->prepare($query);
                $stmt->execute(array($subject, $quest));
                $numrows = $stmt->rowCount();
            
                if (($stmt && ($numrows)) == 0) {
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
                            $query1 = "INSERT INTO tblquestionbank (title, difficultylevel, questiontime, active, author, subjectid, topic) VALUES ('$quest','simple',now(), 'true', 'Ibrahim', $subject, '$topic')";
                            $stmt = $dbh->prepare($query1);
                            $stmt->execute();
                            
                            $query2 = "SELECT questionbankid FROM tblquestionbank WHERE title='?' && difficultylevel='simple' && active=1 && subjectid=? && topic='?'";
                            $stmt = $dbh->prepare($query2);
                            $stmt->execute(array($quest,$subject,$topic));
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            $questid = $row[0];
                            $query3 = "INSERT INTO tblansweroptions (test, questionbankid, correctness) VALUES ('$opt1',$questid," . (($corr == 'opt1') ? ("'1'") : ("'0'")) . " ),('$opt2',$questid," . (($corr == 'opt2') ? ("'1'") : ("'0'")) . " ),('$opt3',$questid," . (($corr == 'opt3') ? ("'1'") : ("'0'")) . " ),('$opt4',$questid," . (($corr == 'opt4') ? ("'1'") : ("'0'")) . " )";
                            $stmt = $dbh->prepare($query3);
                            $stmt->execute();
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
        <link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>
        <?php javascriptTurnedOff(); ?>
        <style>


            .mgt{
                height: 100%;
            }
        </style>
        <?php require_once("../../../partials/cssimports.php") ?>
        <script type ="text/javascript" src ="../../../assets/js/jquery-1.7.2.min.js"></script>
        <script type ="text/javascript" src ="../../../assets/js/jquery-ui-1.7.3.custom.min.js"></script>
        <script type="text/javascript" src="../../../jscripts/tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript" src="../../../jscripts/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>

        <script type="text/javascript">
            tinyMCE.init({
                // General options
                mode : "textareas",
                theme : "advanced",
                skin : "o2k7",
                elements : "B",
                plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
                file_browser_callback : "tinyBrowser",
                // Theme options
                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,insertdate,inserttime,preview,|,image, advimage,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,print,|,ltr,rtl,|,fullscreen",

                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                content_css : "css/content.css",
                template_external_list_url : "lists/template_list.js",
                external_link_list_url : "lists/link_list.js",
                external_image_list_url : "lists/image_list.js",
                media_external_list_url : "lists/media_list.js"
            });
        </script>

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
                                            $query = "SELECT * from tblsubject";
                                            $stmt = $dbh->prepare($query);
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
                                            <table class="table table-bordered">
                                                <th>Type a Question here ...</th>
                                                <tr>
                                                    <td colspan="2">

                                                        <textarea name="quest" placeholder="Type your Question here..." rows="10" cols="30"><?php
                                            if (isset($_POST['quest']))
                                                echo $_POST['quest'];
                                            ?></textarea><br /><br />
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td>
                                                        <b> Enter Options here..</b>
                                                        <textarea  placeholder="Option 1" name="opt1"> <?php
                                                            if (isset($_POST['opt1']))
                                                                echo "value='" . $_POST['opt1'] . "'";
                                            ?></textarea>&nbsp;
                                                    </td>
                                                    <td><b>Ans.</b><input type="radio" name="corr" value="opt1" <?php
                                                            if (isset($_POST['corr']) && $_POST['corr'] == "opt1")
                                                                echo "checked='checked'"
                                                ?> />
                                                    </td><br /><br />
                                            </tr><tr>

                                            <td>
                                                <textarea  placeholder="Option 2" name="opt2"> <?php
                                                                      if (isset($_POST['opt2']))
                                                                          echo "value='" . $_POST['opt2'] . "'";
                                            ?></textarea>&nbsp;
                                            </td>
                                            <td><input type="radio" name="corr" value="opt2" <?php
                                                    if (isset($_POST['corr']) && $_POST['corr'] == "opt2")
                                                        echo "checked='checked'"
                                                ?> />
                                            </td><br /><br />
                                    </tr><tr>

                                    <td>
                                        <textarea placeholder="Option 3" name="opt3" ><?php
                                                   if (isset($_POST['opt3']))
                                                       echo "value='" . $_POST['opt3'] . "'";
                                            ?></textarea>&nbsp;</td>

                                    <td><input type="radio" name="corr" value="opt3" <?php
                                            if (isset($_POST['corr']) && $_POST['corr'] == "opt3")
                                                echo "checked='checked'"
                                                ?> /></td><br /><br />
                            </tr><tr>
                            <td>
                                <textarea placeholder="Option 4" name="opt4"> <?php
                                           if (isset($_POST['opt4']))
                                               echo "value='" . $_POST['opt4'] . "'";
                                            ?></textarea>&nbsp;</td>
                            <td><input type="radio" name="corr" value="opt4" <?php
                                    if (isset($_POST['corr']) && $_POST['corr'] == "opt4")
                                        echo "checked='checked'"
                                                ?> /></td><br /><br />


                        </tr>
                    </table>
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
                  
    //  $(document).ready(function(){
    //        $("#quest_mode").live("change", function(){ 
    //            if ($(this).val()=="upload")
    //               $("#question_authoring").load("upload_questions.php");
    //           else
    //              $("#question_authoring").load("enterinq_question.php"); 
    //     });
                
               
    // });
                 
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



