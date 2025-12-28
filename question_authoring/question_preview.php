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
if (!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin"))) {
    header("Location:" . siteUrl("403.php"));
    exit();
}
$pgtitle = "::Question Authoring";
$navindex = 4;
require_once '../partials/cbt_header.php';
?>
<link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>
<link type="text/css" href="../assets/css/jquery-ui.css" rel="stylesheet"></link>
<link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css"></link>

<?php javascriptTurnedOff(); ?>
<style>
    .qdiv:hover
    {
        background-color: #9fe69f;
    }
    .links
    {
        display:inline-block;
        padding:5px;
    }
    .qdiv
    {
        border-radius:3px;
        -o-border-radius:3px;
        -ms-border-radius:3px;
        -webkit-border-radius:3px;
        -moz-border-radius:3px;
        border-width:1px;
        border-style:solid;
        margin:5px;
        padding:5px;
        background-color: #ccffcc;
        border-color: #9fe69f;
        width:800px;
    }
    #pgkeydiv
    {
        vertical-align: middle;

        text-align: center;

    }

    .pgkey
    {
        text-align: center;
        vertical-align: middle;
        min-width:10px;
        border-style: solid;
        border-color: #cccccc;
        border-width: 1px;
        display:inline-block;
        padding:10px;
        margin:5px;
        font-size: 1em;
        cursor:pointer;
    }

    .pgkey:hover
    {
        border-color: #89d387;
        background-color:  #bdefba;
    }

    .curr
    {
        text-align: center;
        vertical-align: middle;
        min-width:10px;
        background-color: #bdefba;
        border-style: solid;
        border-color: #89d387;
        border-width: 1px;
        display:inline-block;
        padding:10px;
        margin:5px;
        font-size: 1em;
        opacity:0.5;
    }

    #pgsel
    {
        width:80px;
    }
    .current{ color:black; font-weight: bold;}

</style>

<script type ="text/javascript" src ="../assets/js/jquery-1.7.2.min.js"></script>
<script type ="text/javascript" src ="../assets/js/jquery-ui-1.7.3.custom.min.js"></script>
<script type ="text/javascript" src ="../assets/js/select2.min.js"></script>

<div id="container" class="container" style="padding-left: 20px">
    <div class="page-header">
        <h1>Question Authoring</h1>
    </div>
    <div id=" " class="row">
        <?php
        include ("toplinks.php");
        ?>
    </div>
</div>
<div>
    <div class="span12"> 
        <table class="table table-striped">
            <tr>
                <td>
                    <form id="authfrm" target='_blank' method ='POST' action='preview_questions.php' class="style-frm">
                        <span id="msg">
                            <iframe src="register_question.php" name="report"  style="width:100%; padding:0px; margin:0px; height: 50px; border-style:none; border-width: 0px;">

                            </iframe>
                        </span>
                        <br />
                        <table class="table table-bordered">
                            <tr>
                                <td>
                                    <b>Subject Category:</b>
                                    <select name="subjcat" id="subjcat" style="width:100px;" >
                                        <option value="">--select--</option>
                                        <option value="3">O'Level</option>
                                        <option value="1">Regular</option>
                                        <option value="2">SBRS</option>
                                    </select>
                                </td>
                                <td>
                                    <b>Subject:</b>
                                    <select name="subj" id="subj" style="width:100px;" >
                                        <option value="">--Select subject--</option>
                                    </select>  
                                </td>
                                <td>
                                    <b>Topic:</b> <a  style="display:none;" href="javascript:void(0);"  id="ttopic_manager">Manage Topics <br /></a>
                                    <select id="topic" name="topic" style="width:100px;" >
                                        <option value="">All topics</option> 
                                        <?php
                                        if (isset($_POST['subj']))
                                            get_topics_as_options($_POST['subj'], ((isset($_POST['topic'])) ? ($_POST['topic']) : ("")), false);
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <b>Difficulty Level:</b>
                                    <select name="dlevel" id="dlevel" style="width:100px;" >
                                        <option value="all">All</option>
                                        <option value="simple">Simple</option>
                                        <option value="difficult">Difficult</option>
                                        <option value="moredifficult">More Difficult</option>
                                    </select>
                                </td>

                                <td>
                                    <b>Date From: </b>
                                    <input type="text" id="fro" style="width:100px;" placeholder="yyyy-mm-dd" name="fro" />
                                </td>
                                <td>
                                    <b>To: </b>
                                    <input type="text"  id="to" style="width:100px;" placeholder="yyyy-mm-dd" name="to" />
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Records per page:</b> <input type="text" class="numeric-input" name="recperpage" id="recperpage" placeholder="Numeric" value="50" style="width:50px;"/>
                                </td>
                                <td>
                                    <input type="submit" id="load" value=" Load " name="load" />
                                </td>
                                <td colspan="2">

                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" id="question_list">
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
</div>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
<script type="text/javascript">
    var page = 0;
    //$("#to, #fro").datepicker();
    function generate_editor()
    {
        tinymce.init({
            //General
            selector: 'textarea', /**/
            plugins: 'jbimages,table,paste,style, nonbreaking, advhr,autoresize,insertdatetime, inlinepopups, advimage,advlist,fullscreen, autolink,contextmenu,emotions, tiny_mce_wiris',
            theme: 'advanced',
            encoding: "xml",
            entity_encoding: "numeric",
            verify_html: true,
            dialog_type: "modal",
            theme_advanced_buttons1_add: "advhr, nonbreaking,pastetext,pasteword,selectall",
            theme_advanced_buttons3_add: "insertdate,inserttime,motions,fullscreen, tablecontrols",
            table_styles: "Header 1=header1;Header 2=header2;Header 3=header3",
            table_cell_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
            table_row_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
            table_cell_limit: 100,
            table_row_limit: 5,
            table_col_limit: 5,
            extended_valid_elements: "hr[class|width|size|noshade], img[!src|border:0|alt|title|width|height|style]a[name|href|target|title]",
            theme_advanced_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
            theme_advanced_buttons1: "insertdate,inserttime,preview,tiny_mce_wiris_formulaEditor, separator, zoom,separator,forecolor,backcolor",
            theme_advanced_buttons2: "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator",
            theme_advanced_buttons3: "hr,removeformat,visualaid,separator,sub,sup,separator,charmap,jbimages",
            theme_advanced_buttons4: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            relative_urls: false
        });
    }


    $('#subjcat').live('change', function(event) {
        $('#subj').html("<option value=''>loading...</option>");
        $('#topic').html("<option value=''>All topics</option>");
        $("#question_list").html("");
        page = 0;
        $("#topic_manager").hide();
        var sbjcat = $(this).val();
        if (sbjcat == "")
        {
            $('#subj').html("<option value=''>--Select subject--</option>");
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'get_subject.php',
            data: {subjcat: sbjcat}
        }).done(function(msg) {
            //alert(msg);
            $('#subj').html(msg);
            $("#subj").select2();

        });
    });

    $('#subj').live('change', function(event) {
        $('#topic').html("<option value=''>loading...</option>");
        $("#question_list").html("");
        page = 0;

        var sbj = $(this).val();
        if (sbj == "")
        {
            $('#topic').html("<option value=''>All topics</option>");
            $('#topic_manager').hide();
            return false;
        }
        $('#topic_manager').show();
        $.ajax({
            type: 'POST',
            url: 'get_topic.php',
            data: {subj: sbj, addgen: true}
        }).done(function(msg) {
            $('#topic').html("<option value=''>All topics</option>" + msg);
        });
    });

    $('#btn_add_topic').live('click', function(event) {
        $.ajax({
            type: 'Post',
            url: 'manage_topic1.php',
            data: {subj: $("#sbj").val(), topic: $("#add_topic").val(), addsubmit: "true"}
        }).done(function(msg) {
            $("#subj").trigger("change");
            alert(msg);
            $("#add_topic").val("");
        });
        return false;
    });

    $("#load").live('click', function(event) {
        page=0;
        $("#reccount").remove();
        load_question_list();
        return false;
    })
    function load_question_list()
    {
        //load question list
        $("#question_list").html("Loading...");
        $.ajax({
            type: 'POST',
            url: 'get_question_list4.php',
            data: {page: page, sbj: $("#subj").val(), topicid: $("#topic").val(), dlevel: $("#dlevel").val(), fro: $("#fro").val(), to: $("#to").val(), recperpage: $("#recperpage").val()}
        }).done(function(msg) {
            $("#question_list").html(msg);
        });
    }

    $(".editq").live('click', function(event) {
        $("<div id='edit_dialog'>Loading...</div>").dialog({title: 'Edit Question', modal: true, width: 850, height: 500, close: function() {
                $(this).empty().remove();
            }});
        $.ajax({
            type: 'POST',
            url: 'get_edit_question_frm.php',
            data: {qid: $(this).attr("data-qid")}
        }).done(function(msg) {
            $("#edit_dialog").html(msg);
            generate_editor();
        });
        return false;
    });

    $("#topic").live('change', function(event) {
        $("#question_list").html("");
        page = 0;
    });

    $('#btn_del_topic').live('click', function(event) {
        $.ajax({
            type: 'POST',
            url: 'manage_topic3.php',
            data: {subj: $("#sbj").val(), topicid: $("#del_topic").val(), delsubmit: "true"}
        }).done(function(msg) {
            $("#subj").trigger("change");
            $("#deletetopic").trigger("click");
            alert(msg);
        });
        return false;
    });

    $('#btn_edit_topic').live('click', function(event) {
        $.ajax({
            type: 'POST',
            url: 'manage_topic2.php',
            data: {subj: $("#sbj").val(), topicid: $("#edit_topic").val(), edition: $("#edition").val(), editsubmit: "true"}
        }).done(function(msg) {
            $("#subj").trigger("change");
            $("#edittopic").trigger("click");
            alert(msg);
        });
        return false;
    });

    $("#topic_manager").live("click", function(event) {
        $.ajax({
            type: "post",
            url: "manage_topic.php",
            data: {sbj: $("#subj").val()}
        }).done(function(msg) {
            $("<div id='mydialog'>" + msg + "</div>").dialog({
                title: "Topic Manager",
                width: 600,
                height: 300,
                modal: true,
                close: function() {
                    $(this).empty().remove();
                }
            });
        });

    });
    $("#addtopic").live("click", function(event) {
        $("#ctr-cont").html("Loading...");
        $.ajax({
            type: "post",
            url: "manage_topic1.php",
            data: {sbj: $("#sbj").val()}
        }).done(function(msg) {
            //alert("");
            $("#ctr-cont").html(msg);
        });
    });

    $(".tab").live("click", function(event) {
        $(".active").removeClass("active");
        $(this).addClass("active");
    });

    $("#edittopic").live("click", function(event) {
        $("#ctr-cont").html("Loading...");
        $.ajax({
            type: "post",
            url: "manage_topic2.php",
            data: {sbj: $("#sbj").val()}
        }).done(function(msg) {
            //alert("");
            $("#ctr-cont").html(msg);
        });
    });


    $("#deletetopic").live("click", function(event) {
        $("#ctr-cont").html("Loading...");
        $.ajax({
            type: "post",
            url: "manage_topic3.php",
            data: {sbj: $("#sbj").val()}
        }).done(function(msg) {
            //alert("");
            $("#ctr-cont").html(msg);
        });
    });


    //:::::::::::::::::Events particularly for pagination

    $(document).on('click', ".pgkey", function(event) {
        var vl = $(this).text();
        //alert(vl);
        if (vl == 'Next')
        {
            vl = page + 1;
        }
        else
        if (vl == 'Previous')
        {
            vl = page - 1;
        }
        else
            vl = vl - 1;
        //alert(vl);
        page = vl;
        load_question_list();
    });

    $(document).on('change', "#pgsel", function(event) {
        // alert("ene");
        var vl = $(this).val();
        //alert(vl);
        vl = vl - 1;
        //alert(vl);
        page = vl;
        load_question_list();
    });

//:::::::::::::::for selections::::::::::::
$(document).on('click','#selall',function(event){
    if($(this).prop('checked')== true){
        $(".sel").prop("checked",true);
    }else{
        $(".sel").prop("checked",false);
    }
});

$(document).on('click','.sel',function(event){
    if($(this).prop('checked')== false){
        $("#selall").prop("checked",false);
    }
});
</script>
</body>
</html>