<?php
if (!isset($_SESSION))
    session_start();
?>
<!DOCTYPE html>
<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("authoring_functions.php");
$pgtitle="::Question Authoring";
$navindex=4;
require_once '../partials/cbt_header.php';
openConnection();
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
?>
<html lang="en">
    <head>
        <title>Question Authoring</title>
        <link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>
        <?php javascriptTurnedOff(); ?>
        <style>

            .links
            {
                display:inline-block;
                padding:5px;
            }

            .row{

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

        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>

        <script type ="text/javascript" src ="../assets/js/jquery-1.7.2.min.js"></script>
        <script type ="text/javascript" src ="../assets/js/jquery-ui-1.7.3.custom.min.js"></script>
        <script type="text/javascript" src="jscripts/tinymce/tiny_mce.js"></script>

    </head>
    <body>

        <div id="container" class="container" style="padding-left: 20px">
            <div class="page-header"><br />
            <h1 style="padding-left: 20px">Add Questions to the Question Bank..</h1>
            </div>
            <div  class="row">
<?php
include ("toplinks.php");
?>
            </div>
        </div>
        <div class="span12" style="padding-left: 20px"> 
            <table class="table table-striped">
                <tr>
                    <td>
                        <form id="authfrm" action="index.php" method="post" enctype='multipart/form-data' class="style-frm">
                            <span id="msg">
                                <iframe src="register_question.php" name="report"  style="width:100%; padding:0px; margin:0px; height: 50px; border-style:none; border-width: 0px;">

                                </iframe>
                            </span>
                            <br />
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        <b>Subject Category:</b>
                                        <select name="subjcat" id="subjcat">
                                            <option value="">--select--</option>
                                            <option value="3">O'level</option>
                                            <option value="1">Regular</option>
                                            <option value="2">SBRS</option>
                                        </select>
                                    </td>
                                    <td>
                                        <b>Subject:</b>
                                        <select name="subj" id="subj">
                                            <option value="">--Select subject--</option>
                                        </select>  
                                    </td>
                                    <td>
                                        <b>Topic:</b> <a  style="display:none;" href="javascript:void(0);"  id="topic_manager">Manage Topics</a>
                                        <select id="topic" name="topic">
                                            <option value="">--Select topic--</option> 
                                            <?php
                                            if (isset($_POST['subj']))
                                                get_topics_as_options($_POST['subj'], ((isset($_POST['topic'])) ? ($_POST['topic']) : ("")), true);
                                            ?>
                                        </select>
                                    </td>
                                    <td colspan="4">
                                        <b>Authoring Mode:</b>                                                
                                        <select name="quest_mode" id="quest_mode" disabled >
                                            <option value="">--Select question mode--</option>
                                            <option value="upload">Upload Questions</option>
                                            <option value="onebyone">Enter Questions one by one</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div id="question_authoring">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                <center>
                                    <input type="submit" id="submit" name="submitted" style="display:none;" class="btn btn-primary" value="Register" />
                                </center>
                                </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>

        </div>

        <script type="text/javascript">
            function generate_editor()
            {
                tinymce.init({
                    //General
                    selector:'textarea',/**/
                    plugins:'jbimages,table,paste,style, nonbreaking, advhr,autoresize,insertdatetime, inlinepopups, advimage,advlist,fullscreen, autolink,contextmenu,emotions, tiny_mce_wiris,gsynuhimgupload',
                    theme:'advanced',
                    encoding : "xml",
                    entity_encoding : "numeric",
                    verify_html : true,
                    dialog_type : "modal",
                    theme_advanced_buttons1_add : "advhr, nonbreaking,pastetext,pasteword,selectall",
                    theme_advanced_buttons3_add : "insertdate,inserttime,motions,fullscreen, tablecontrols",
                    table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
                    table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
                    table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
                    table_cell_limit : 100,
                    table_row_limit : 5,
                    table_col_limit : 5,
                    extended_valid_elements : "hr[class|width|size|noshade], img[!src|border:0|alt|title|width|height|style]a[name|href|target|title]",
                    theme_advanced_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
                    theme_advanced_buttons1 : "insertdate,inserttime,preview,tiny_mce_wiris_formulaEditor, separator, zoom,separator,forecolor,backcolor",
                    theme_advanced_buttons2 : "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,gsynuhimgupload",
                    theme_advanced_buttons3 : "hr,removeformat,visualaid,separator,sub,sup,separator,charmap,jbimages",
                    theme_advanced_buttons4:"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
                        
                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align : "left",
                    theme_advanced_statusbar_location : "bottom",
                    theme_advanced_resizing : true,
                    relative_urls:false
                });

            }
                   
            $("#quest_mode").live("change", function(){ 
                if ($(this).val()=="upload"){
                    $('#question_authoring').html("Loading...");
                    $.ajax({
                        type:'POST',
                        url:'<?php echo siteUrl('question_authoring/test.php'); ?>'
                    }).done(function(msg){
                        $("#question_authoring").html(msg);
                                
                        $('#authfrm').attr('action','test_exec.php');
                        $('#authfrm').attr('target', '_blank');
                        $(".mceEditor").remove();
                        generate_editor();
                        $('#submit').show();
                    });
                }
                else if($(this).val()=="onebyone"){
                    $('#question_authoring').html("Loading..");
                            
                    $.ajax({
                        type:"POST",
                        url:"enterinq_question.php"
                    }).done(function(msg){
                        $("#question_authoring").html(msg); 
                                
                        $('#authfrm').attr('action','register_question.php');
                        $('#authfrm').removeAttr('target');
                        $('#authfrm').attr('target', 'report');
                        $(".mceEditor").remove();
                        generate_editor();
                        $('#submit').show();
                    });
                }
                else
                {
                    $('#question_authoring').html("");
                    $('#submit').hide();
                }
            });
                    
                    
            $('#subjcat').live('change',function(event){
                $('#subj').html("<option value=''>loading...</option>");
                $('#topic').html("<option value=''>--Select topic--</option>");
                $("#quest_mode").val('').attr('disabled','disabled');                                
                $("#question_authoring").html("");
                $('#submit').hide();
                $("#topic_manager").hide();
                var sbjcat=$(this).val();
                if(sbjcat=="")
                {
                    $('#subj').html("<option value=''>--Select subject--</option>");
                    return false;
                }
                $.ajax({
                    type:'POST',
                    url:'<?php echo siteUrl('question_authoring/get_subject.php'); ?>',
                    data:{subjcat:sbjcat}
                }).done(function(msg){
                    //alert(msg);
                    $('#subj').html(msg);
                });
            });
                 
            $('#subj').live('change',function(event){
                $('#topic').html("<option value=''>loading...</option>");
                $("#quest_mode").val('').attr('disabled','disabled');    
                $('#submit').hide();
                $("#question_authoring").html("");
                       
                var sbj=$(this).val();
                if(sbj=="")
                {
                    $('#topic').html("<option value=''>--Select topic--</option>");
                    $('#topic_manager').hide();
                    return false;
                }
                $('#topic_manager').show();
                $.ajax({
                    type:'POST',
                    url:'get_topic.php',
                    data:{subj:sbj, addgen:true}
                }).done(function(msg){
                    //alert(msg);
                    $('#topic').html("<option value=''>--Select topic--</option>"+msg);
                });
            });
                 
            $('#btn_add_topic').live('click', function(event){
                $.ajax({
                    type:'Post',
                    url:'manage_topic1.php',
                    data:{subj:$("#sbj").val(), topic:$("#add_topic").val(), addsubmit:"true"}
                }).done(function(msg){    
                    $("#subj").trigger("change");
                    alert(msg);
                    $("#add_topic").val("");
                });
                return false;
            });
                    
            $("#topic").live('change', function(event){
                if($(this).val()=="")
                {
                    $("#quest_mode").val('').attr('disabled','disabled');                                
                    $("#question_authoring").html("");
                    $('#submit').hide();
                }
                else
                {
                    $("#quest_mode").removeAttr('disabled');
                                    
                }
            });
                    
            $('#btn_del_topic').live('click', function(event){
                $.ajax({
                    type:'POST',
                    url:'manage_topic3.php',
                    data:{subj:$("#sbj").val(), topicid:$("#del_topic").val(), delsubmit:"true"}
                }).done(function(msg){    
                    $("#subj").trigger("change");
                    $("#deletetopic").trigger("click");
                    alert(msg);
                });
                return false;
            });
                    
            $('#btn_edit_topic').live('click', function(event){
                $.ajax({
                    type:'POST',
                    url:'manage_topic2.php',
                    data:{subj:$("#sbj").val(), topicid:$("#edit_topic").val(), edition:$("#edition").val(), editsubmit:"true"}
                }).done(function(msg){    
                    $("#subj").trigger("change");
                    $("#edittopic").trigger("click");
                    alert(msg);
                });
                return false;
            });
                    
            $("#topic_manager").live("click", function (event){
                $.ajax({                    
                    type:"post",
                    url:"manage_topic.php",
                    data:{sbj:$("#subj").val()}                        
                }).done(function(msg){                            
                    $("<div id='mydialog'>"+msg+"</div>").dialog({                    
                        title: "Topic Manager",
                        width: 600,
                        height:300,
                        modal: true,
                        close:function(){$(this).empty().remove();}
                    });
                });
                
            });
            $("#addtopic").live("click", function (event){
                $("#ctr-cont").html("Loading...");
                $.ajax({                    
                    type:"post",
                    url:"manage_topic1.php",
                    data:{sbj:$("#sbj").val()}
                }).done(function(msg){
                    //alert("");
                    $("#ctr-cont").html(msg);
                });
            });
                
            $(".tab").live("click", function (event){
                $(".active").removeClass("active");
                $(this).addClass("active");
            });
                    
            $("#edittopic").live("click", function (event){
                $("#ctr-cont").html("Loading...");
                $.ajax({                    
                    type:"post",
                    url:"manage_topic2.php",                    
                    data:{sbj:$("#sbj").val()}
                }).done(function(msg){
                    //alert("");
                    $("#ctr-cont").html(msg);                               
                });
            });
                     
                     
            $("#deletetopic").live("click", function (event){
                $("#ctr-cont").html("Loading...");
                $.ajax({                    
                    type:"post",
                    url:"manage_topic3.php",
                    data:{sbj:$("#sbj").val()}
                }).done(function(msg){
                    //alert("");
                    $("#ctr-cont").html(msg);
                });               
            });
                    
            $("#addopt").live('click',function(event){
                $.ajax({
                    type:"POST",
                    url:'add_option.php',
                    data:{optcount:$("#optcount").val()}
                }).done(function(msg){
                    $("#opttb").append(msg);
                    $("#optcount").val(($("#optcount").val()+1));
                    $(".mceEditor").filter(function(){ if($(this).css('display')!="none") return false; else return true;}).remove();
                    generate_editor();
                            
                });
            });
        </script>

    </body>
</html>