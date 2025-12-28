<?php
if (!isset($_SESSION))
    session_start();
?>
<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("testfunctions.php");
$candidateid=1;
$testid=23;

openConnection(true);
authorize();

if (isset($_POST['registersubject'])) {
    $subselcount = $_POST['subcount'];
    $tid = $_POST['testid'];
    for ($i = 0; $i < $subselcount; $i++) {
        if (isset($_POST['subj' . $i])) {
            $sid = $_POST['subj' . $i];

            $sql = "insert into tbltestsubject (testid, subjectid) values ($tid, $sid)";
            $query = mysql_query($sql);

            //echo $sql;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT</title>
        <?php require_once("./../partials/cssimports.php") ?>
        <link type="text/css" href="./../assets/css/qconfig.css" rel="stylesheet"></link>

    </head>

    <body>
        <?php include_once "../partials/navbar.php" ?>;
		<?php
		$testinfo=array();
		$testinfo=gettestinfo($testid);
		//get biodata
		$biodata=array();
		if(!isset($_SESSION['biodata'])){
		$biodata=getbiodata($candidateid);
			}
			else{
				$biodata=$_SESSION['biodata'];
			}
			$candidatename=$biodata['candidatename'];
			$matric=$biodata['matric'];
		
		//get the passport
		$pict="";
		if(file_exists("../picts/$matric.jpg")){$pict="$matric";}else{$pict="nopicture.jpg";}
		
		?>
        <div id="container" class="container">
		<table width=100% border=1>
                        <tr>
                            <td><font size=4><strong>EXAM:<strong></font><?php echo $testinfo['name'];?> <br><font size=4> Duration:</font> <?php echo $testinfo['duration'];?> mns</font> </td>
							<td><font size=4><strong>Candidate NAME:  <?php echo $candidatename;?><br> Candidate Number:<?php echo $matric;?> </font> </td>
							<td>
							
							<div id="defaultCountdown">k</div>
							<font size=4>Time control</font><br> <font size=4>Start Time:</font><?php echo $testinfo['starttime'];?>
							<br> <font size=4>End Time:</font><?php echo $testinfo['endtime'];?> 
							<br><font size=4>Remaining time:</font> </td>
							<td><img alt="Picture"  border="1" align="right"height="70"  width="70"src="<?php echo "../picts/$pict.jpg";?>" /></td>
						</tr>	
		</table>					
							
		<hr />
		<h2 class="step_heading"> <?php getsubject($candidateid,$testid);?> </h2>
            <form id="subselfrm" name="subselfrm" action="#" method="post">
                <div id="sel">
                    <table>
                        <tr>
                        <button id="genform">Generate</button>
						</tr>
                    </table>
                </div>
                <br />
            </form>
        </div><!-- /container -->

        <?php include_once "../partials/footer.php" ?>;
<?php include_once "../partials/jsimports.php" ?>;
        <script type="text/javascript" >
           
			$('.click').click(function() {
				var linkText = $(this).id();
				alert(linkText);
				
				return false;
			});

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
            
    $(document).on('click', '.subjs1', function(){ 
        if(!$(this).val()!='')
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
        </script>
    </body>
</html>
