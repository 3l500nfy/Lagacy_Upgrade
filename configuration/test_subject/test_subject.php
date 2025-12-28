<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();

if (!isset($_GET['tid']))
    header("Location:".siteUrl("403.php"));
$testid = $_GET['tid'];

if (!is_test_administrator_of($testid))
    header("Location:".siteUrl("403.php"));

$test_config = get_test_config_param_as_array($testid);
$unique = $test_config['session'] . " /" . $test_config['testname'] . " /" . $test_config['testtypename'] . " /" . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/jquery-ui-timepicker-addon.css'); ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
        </script>
        <style type="text/css">
            #letter{
                padding:5px;
                padding-top:10px;
                padding-bottom:3px;
                border-width:0px;
                border-style: none;
                border-bottom-style: solid;
                border-bottom-width: 2px;
                border-bottom-color: green;                
            }

            .sbj:hover{
                background-color:  #ddf4d5;
                border-width: 1px;
                border-color: green;
                border-style: solid;

            }

            #subject-list
            {
                max-height: 250px;
                overflow: auto;
            }

            .sbj{
                margin-top: 3px;
                cursor: pointer;
                display:inline-table;
                padding:5px;
                padding-top:4px;
                padding-bottom:1px;
                border-width:1px;
                border-color: transparent;
                border-style: solid;
                border-bottom-style: solid;
                border-bottom-width: 2px;
                border-bottom-color: green;    
                margin-left: 20px;
                border-radius:3px;
                -o-border-radius:3px;
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
                -ms-border-radius:3px;            
            }

            #letter .alpha{
                padding:3px;
                margin:0px;
                margin-left: 5px;
                display: inline-block;
                max-width: 10px;
                cursor:pointer;
            }

            #letter .choosen{
                font-weight: bold;
                text-decoration: underline;
                font-size:  larger;
            }

            #letter .alpha:hover{
                font-weight: bold;
                text-decoration: underline;
            }
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Test Subject(s):</h2><br />
            <form class="style-frm" id="add-subject-frm"><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />
                <div style="text-align: right;"><label> <input type="checkbox" id="safemode" name="safemode" value="1" checked/> safe mode</label></div>
                <fieldset id="add-subject"><legend>Add Subject</legend>
                    <div style="padding:0px; margin:0px; border-width: 0px;">
                        <b>Hint: </b> <input type="text" id="hint" name="hint" placeholder="Type some words to filter" /> 

                        <div id="letter" class="alpha-div">
                            <?php
                            $letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
                            foreach ($letters as $letter) {
                                echo"<div class='alpha'>$letter</div>";
                            }
                            ?>
                        </div>
                    </div>
                    <br />
                    <div id="subject-list">
                        <?php
                        if (strtoupper(trim($test_config['testcodeid'])) == '1' || strtoupper(trim($test_config['testcodeid'])) == "12") {
                            $query = "select * from tblsubject where subjectcategory =3";
                        } else
                        if (strtoupper(trim($test_config['testcodeid'])) == '2') {
                            $query = "select * from tblsubject where subjectcategory =2";
                        } else {
                            $query = "select * from tblsubject where subjectcategory =1";
                        }
                        $stmt=$dbh->prepare($query);
                        $stmt->execute();

                        if ($stmt->rowCount() == 0)
                            echo"No subject to select from.";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $sbj_code = $row['subjectcode'];
                            $sbj_name = $row['subjectname'];
                            $sbj_id = $row['subjectid'];

                            if (is_registered_subject($testid, $sbj_id))
                                continue;

                            echo"<div class='sbj' data-sbjid='$sbj_id'>" . strtoupper(trim($sbj_code)) . " - " . intelligentStr(ucwords(strtolower($sbj_name)), 30) . "</div>";
                        }
                        ?>
                    </div>
                </fieldset>
                <br />
                <?php
                $test_subjects = get_test_subjects_as_array($test_config['testid']);

                    ?>
                    <fieldset id="sbj-list" <?php if (count($test_subjects) == 0) echo"style='display:none;'"; ?>><legend>Registered Test Subject(s)</legend>
                        <div id="existing-subject">
                            <table class="style-tbl" style=""><tr><th>S/N</th><th>Subject Code</th><th>Subject name</th><th>Remove</th></tr>
                                <?php
                                $c = 1;
                                foreach ($test_subjects as $test_subject) {
                                    $query1 = "select * from tblsubject where subjectid=?";
                                    $stmt1=$dbh->prepare($query1);
                                    $stmt1->execute(array($test_subject));
                                    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

                                    $sbj_code = $row1['subjectcode'];
                                    $sbj_name = $row1['subjectname'];
                                    $sbj_id = $row1['subjectid'];

                                    echo"<tr><td>" . $c++ . "</td><td>" . strtoupper(trim($sbj_code)) . "</td><td>" . intelligentStr(ucwords($sbj_name), 30) . "</td><td>[<a class='del-sbj' data-sbjid='" . $sbj_id . "' href='javascript:void(0);'>remove</a>]</td></tr>";
                                }
                                ?>
                            </table>
                        </div><br /></fieldset>
                        
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script src="<?php echo siteUrl('assets/js/jquery-ui-timepicker-addon.js'); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
                $("#contentframe", top.document).height(0).height($(document).height());

            $(document).on('click','.sbj',function(event){
                var dis=$(this);//alert($("#sbjid").val());
                $.ajax({
                    type:'POST',
                    url:'test_subject_add.php',

                    data:{tid:$("#tid").val(), sbjid:dis.attr("data-sbjid"), safemode:(($("#safemode").prop("checked")==true)?("1"):(""))}
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    if(msg==0)//server error
                    {
                        alert("Server Error! Please try again.");
                    }
                    if(msg==1)//success
                    {
                        alert("Success!");
                        dis.remove();
                        refresh_subject_list(true);
                    }
                    if(msg==2)//permission denied
                    {
                        alert("Permission denied.");
                    }
                    if(msg==3)//test already taken
                    {
                        alert("Test already taken.");
                    }
                    if(msg==4)//possible displaceent
                    {
                        alert("Some scheduled candidates may be affected.");
                    }
                    if(msg==5)//test id not taken
                    {
                        alert("No subject selection.");
                    }
                    if(msg==6)//subject already registered
                    {
                        alert("Subject has already been registered.");
                    }
                });
                return;                
                
            });
            
            $(document).on('click','.alpha',function(event){
                $("#subject-list").html("Searching...");
            
                $("#hint").val("");
                $(".choosen").removeClass("choosen");
                $(this).addClass("choosen");
                $.ajax({
                    type:'POST',
                    url:'../getters/search_subject.php',
                    data:{tid:$("#tid").val(), mode:'first', ch:$.trim($(this).text())}
                }).done(function(msg){
                    $("#subject-list").html(msg);
                });
          
            });  
          
            $(document).on('keyup change','#hint',function(event){
                $("#subject-list").html("Searching...");
                $(".choosen").removeClass("choosen");
                
                $.ajax({
                    type:'POST',
                    url:'../getters/search_subject.php',
                    data:{tid:$("#tid").val(), mode:'any', ch:$.trim($(this).val())}
                }).done(function(msg){
                    $("#subject-list").html(msg);
                });
          
            });  
            
            $(document).on('click','.del-sbj', function(event){
                if(!window.confirm("Are you sure you want to remove this subject?"))
                {
                    return false;
                }
                else
                {
                    var sbjid=$(this).attr("data-sbjid");
                   
                    $.ajax({
                        type:'POST',
                        url:'test_subject_delete.php',
                        data:{sbjid:sbjid, tid:$("#tid").val(), safemode:(($("#safemode").prop("checked")==true)?("1"):(""))}
                    }).done(function(msg){ //alert(msg);
                        msg=($.trim(msg)-0);
                             
                        if(msg==0)//server error
                        {
                            alert("Server Error! Please try again.");
                        }else
                            if(msg==1)//success
                        {
                            alert("Subject was successfully removed.");
                            refresh_subject_list();
                        }else
                            if(msg==2)//invalid permission
                        {
                            alert("Permission denied");
                        }else
                            if(msg==3)//possible displacement
                        {
                            //alert("resolve_subject_displacement.php?tid="+$("#tid").val()+"&subjid="+sbjid);
                            window.location="resolve_subject_displacement.php?tid="+$("#tid").val()+"&subjid="+sbjid+(($("#safemode").prop("checked")==true)?("&safemode=1"):(""));
                        }else
                            if(msg==4)// schedule date passed
                        {
                            alert("Test has already been taken.");
                        }else
                            if(msg==5)// schedule not selected
                        {
                            alert("No subject selection.");
                        }
                        return false;
                                 
                    });
                }
                return false;
            });
          
            function refresh_subject_list(cond)
            {
                if(cond==null || cond== undefined)
                    cond=false;
                $.ajax({
                    type:'GET',
                    url:'../getters/refresh_subject_list.php',
                    data:{testid:$("#tid").val()}
                }).done(function(msg){
                    if(cond)
                    $("#sbj-list").show();
                    $("#existing-subject").html(msg);
                   
                    $("#contentframe", top.document).height($(document).height());
                });
            }
        </script>
    </body>
</html>