<?php
if (!isset($_SESSION))
    session_start();
?>
<?php
require_once("../../lib/globals.php");
require_once("../../lib/security.php");


openConnection(true);
authorize();

if (isset($_POST['registersubject'])) {
    $subselcount = $_POST['subcount'];
    $tid = $_POST['testid'];
    for ($i = 0; $i < $subselcount; $i++) {
        if (isset($_POST['subj' . $i])) {
            $sid = $_POST['subj' . $i];

            $query = "insert into tbltestsubject (testid, subjectid) values (?, ?)";
            $stmt=$dbh->prepare($query);
            $stmt->execute(array($tid,$sid));

        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT</title>
        <?php require_once("../../partials/cssimports.php") ?>
        <link type="text/css" href="../../assets/css/qconfig.css" rel="stylesheet"></link>

    </head>

    <body>
        <?php include_once "../../partials/navbar.php" ?>;

        <div id="container" class="container"><h1>Subject Selection</h1> <hr />
            <form id="subselfrm" name="subselfrm" action="#" method="post">
                <div id="sel">
                    <table>
                        <tr>
                            <td>Exam:</td><td><select style="margin: 10px;" name="exam" id="exam"><option value="">select exam</option><?php
                                    $query1 = "select * from tbltestcode";
                                    $stmt=$dbh->prepare($query1);
                                    $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            echo"<option value='" . $row['testcodeid'] . "'>" . $row['testname'] . "</option>";
        ?></select></td>
                            <td>Session:</td><td><select style="margin: 10px;" name="sess" id="sess"><option value="">select session</option><?php
                                    $dt = date('Y');
                                    for ($i = $dt - 5; $i < $dt + 5; $i++)
                                        echo "<option value='$i'" . (($i == $dt) ? ("selected='selected'") : ("")) . ">$i</option>";
        ?></select></td>
                            <td>Semester</td><td><select style="margin: 10px;" name="sem" id="sem"><option value="">select semester</option><option value="1">First</option><option value="2">Second</option><option value="3">Third</option></select></td>
                        </tr>
                        <tr>
                            <td></td><td><button id="genform">Generate</button></td><td></td><td></td><td></td><td></td>
                        </tr>
                    </table>
                </div>
                <br />
            </form>
        </div><!-- /container -->

        <?php include_once "../../partials/footer.php" ?>;
        <?php include_once "../../partials/jsimports.php" ?>;
        <script type="text/javascript" >
                            $("#contentframe", top.document).height(0).height($(document).height());

            $("#exam").bind('change', function(evt){
                if($(this).val()=='1')
                {
                    $('#sem').val("").attr("disabled","disabled");
                }
                else $('#sem').removeAttr("disabled");
            });
            
            $(document).on('click', '.subjs', function(){ 
                if(!$(this).hasClass('subsel'))
                {
                    $(this).addClass('subsel');
                    $("#registersubject").show();
                }
                else
                {
                    $(this).removeClass('subsel');
                    if($('.subsel').size()==0)
                        $("#registersubject").hide();
                }
            });
            
            $(document).on('change', '.subjs1', function(){ 
                if($(this).val()!='')
                {
                    $("#registersubject").show();
                }
                else
                {
                    $("#registersubject").hide();
                }
            });
            
            $("#genform").bind('click', function(evt){
                if($('#exam').val()=="" || $('#sess').val()=="")
                {
                    alert("Invalid Selection");
                    $("#subseldiv").remove();
                    return false;
                }
                if($('#sem').val()=="" && $('#exam').val()!="1")
                {
                    alert("Invalid Selection");
                    $("#subseldiv").remove();
                    return false;
                }
                $.ajax({
                    type:'POST',
                    url:'getsubselform.php',
                    data:{exam:$('#exam').val(), sess:$('#sess').val(), sem:$('#sem').val()}
                }).done(function(msg){
                    $("#subseldiv").remove();
                    $("#subselfrm").append(msg);
                });
                return false;
            });
            $(document).on('click', "#addsection", function(evt){
                $.ajax({
                    type:'POST',
                    url:'getnewsectionfrm.php'
                }).done(function(msg){
                    $(msg).dialog({ title:'New Section', width:'500px', 'buttons':{'SAVE':function(){$(this).dialog('close').remove();}, 'CANCEL':function(){$(this).dialog('close').remove();}}}); 
                });
            
            });
            $("button").button();
        </script>
    </body>
</html>
