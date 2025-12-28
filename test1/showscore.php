<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("testfunctions.php");
require_once("../reports/test_report_function.php");
//require_once("../lib/test_config_func.php");
openConnection();
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("online/index.php"));
}

//check if the candidate has taken and cmpleted the test
//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];


$testinfo = array();
if (!isset($_SESSION['testinfo'])) {
    $testinfo = gettestinfo($testid);
} else {
}
//$pgtitle = "::SCORE";

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
<?php echo pageTitle("Online Tutorial") ?>
        </title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>

        <style type="text/css">

            .tbnnavigation
            {
                -moz-border-radius:5px;
                -webkit-border-radius:5px;
                border-radius:5px;
                margin:3px;
                font-weight:bold;
                font-size:2em;
                padding:10px;

            }

            .auto-style1 {
                text-align: center;
            }
            .auto-style2 {
                text-align: left;
            }
        </style>
    </head>

    <body>

        
        
<div><img src="<?php echo siteUrl("assets/img/dariya_logo1.png");?>" /></div>
<link href="<?php echo siteUrl("assets/css/startteststyle.css"); ?>" type="text/css" rel="stylesheet"></link>
<div id="resultdiv">
    <table style="margin-left:auto; margin-right:auto;"><tr><th colspan="2"><h1><?php echo trim($_SESSION['biodata']['candidatename'],',');?>, Your Score Is:</h1></th></tr>
        <?php
        $aggregate2=0;
        $aggregate=0;
        $subjectscore=0;
        $overallscore=0;
        $rw="";
        $tsubj = get_subject_combination_as_array($testid);
        $rsubj = get_subject_registered_as_array($testid, $candidateid);
        foreach ($tsubj as $sbj) {
            if (in_array($sbj, $rsubj)) {
                //echo "en ".$sbj;
                $sbj_name=get_subject_code_name($sbj);
                $aggregate = get_candidate_subject_score($testid, $candidateid, $sbj);
                $aggregate2 += $aggregate;
               $subjectscore= get_subject_total_mark($sbj, $testid);
               $overallscore +=$subjectscore;
                //$total = $total + $aggregate;
                $rw .="<tr><td>$sbj_name</td><td>$aggregate /$subjectscore</td></tr>";
            }
        }
        $rw.="<tr><td><b>Total</b></td><td><b>$aggregate2/$overallscore</b></td>";
        echo $rw;
        ?>
   <tr><td><b>Note: <i>This is not an  ABU Test Result and <br> cannot in any circumstances be tendered as such
.</i></b></td></tr>
    </table>
    
    <div  style="text-align:center; font-size: 2em; color: green">
        <?php
        /*
                               <button type="button" id="viewsolution"class="btn btn-info btn-block" style="font-weight: bold;">View Solution</button>
             */
        ?>               
    </div>
    <?php
  //  <div style="margin-left: auto; margin-left: auto;"><a href="<?php siteUrl("online/index.php")>">Exit</a></div>
    ?>
</div>



<div id="maindiv" class="span5 style-div" style="margin-left: auto; width:700px; margin-top: 50px; padding-left: 30px; margin-right: auto;">
                
                
                  

                
                <form class="style-frm" method="POST" action="../online/feedback.php">
                       		
                      <?php echo" <input type='hidden' id='candidateid' value='$candidateid' name='candidateid'>";?>
                      <?php echo" <input type='hidden' id='testid'value='$testid' name='testid'>";?>
                    
                         <div class="control-group">
                            <label for="comments" style="font-weight: bold;">Please kindly fill the form below to give us your feedback on the use of this system. Thank You <p></label> 
                            <div class="controls">
                                <textarea name="comments" id="comments" placeholder="Enter your comments here" rows="4" cols="60" required></textarea>
                            </div>                

                        </div><!-- /.control-group -->
			
                        <p>
                            
                        
                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-info btn-block" style="font-weight: bold;">Submit Feedback</button>
                            </div>
                        </div> <!-- /.control-group -->
                     
                        
                    </form>
                    
               
                
                
                <div class="contentbox" style="padding-top: 10px;  ">
                    <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
                        <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); text-rendering: optimizelegibility; font-size: 18px; font-weight: 700; line-height:40px; "> 
                           </h2>
                    </div>

                    
                    
                </div
                
                
                
            </div>
        </div>




<?php
  session_destroy();
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    $("#viewsolution").bind('click', function(evt) {
       // alert("kk");
       
      ////////////////////////
      $.ajax({
                        type: 'POST',
                        url: '../online/showsolution.php',
                        data:{candidateid:$("#candidateid").val(),testid:$("#testid").val()}
                    }).done(function(msg) {
                        ////////////////////////////////
                        //alert(msg);
                         $('<div></div>').appendTo('body')
                .html('<div style="text-align: center; font-size: 3em; "> SOLUTION<p></div>\n\
                <b>Key:</b> <div class="selected-opt" style="width:30px; height:30px;"> </div> Candidate Selection <br /> <div style="width:30px; height:30px; background-color:#8dc96e;"> </div> Correct Option<div>' + msg + '</div>')
                .dialog({
            modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: true,
            buttons: {
                
                Done: function() {
                        $(this).remove();
                }
            },
            close: function(event, ui) {
                $(this).remove();
            }
                        
                        
                         
        });
         });
                     
                        //////////////////////
                        
                        
                      

      
      ////////////////////////
    });

</script>
</body>
</html>