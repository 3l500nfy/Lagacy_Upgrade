<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator")))
    header("Location:" . siteUrl("403.php"));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css"></link>

        <script type="text/javascript">
        </script>
        <style type="text/css">
            .anchor{color:#999999;}
            .anchor:hover{color:black;}
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Restore Logged Out Candidate</h2><br />
            [<a class="anchor" href="manage_restore.php">Manage Restore</a>] | [<a class="anchor" href="manage_time.php">Manage Time Increase</a>] |  [<a class="anchor" href="password_Retrieval.php">Candidate's Password Retrieval</a>]
            <br/><br/>
            <form id="frm1" class="style-frm">
                <div id="first-step">
                    <div class="frm-request-cand-info">
                        <fieldset><legend>Enter Candidate's Details</legend>
                            <table>
                                <tr>
                                    <td><b> Candidate Type</b></td>
                                    <td> 
                                        <select name ="candidatetype" id="candidatetype">
                                            <option value="">--Select Candidate Type --</option>
                                            <?php
                                            $query = "SELECT * FROM tblcandidatetypes";
                                            $stmt=$dbh->prepare($query);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                                $candidatetypeid = $row['candidatetypeid'];
                                                $candidatetype = $row['candidatetype'];
                                                echo "<option value ='$candidatetypeid'>$candidatetype</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Exam Type</b></td>
                                    <td> 
                                        <select name="testid" id="testid"  class="input-block-level"><option value="">Select Category</option>
                                            <?php
                                            //populate all exams to take place today
                                            $query1 = "SELECT tbltestconfig.testid,tbltestconfig.testname,testtypename,session,semester FROM tbltestconfig 
							inner join tbltestcode ON tbltestconfig.testcodeid=tbltestcode.testcodeid
							left join tbltesttype ON tbltestconfig.testtypeid=tbltesttype.testtypeid
							INNER JOIN tblexamsdate on tblexamsdate.testid=	tbltestconfig.testid
							where(tblexamsdate.date=curdate( ))";
                                            $stmt1=$dbh->prepare($query1);
                                            $stmt1->execute();

//                                            for ($i = 0; $i < $stmt1->rowCount(); $i++) {
                                                while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
                                                $testid = $row1['testid'];
                                                $testname = strtoupper($row1['testname']);
                                                $testtypename = $row1['testtypename'];
                                                $session = $row1['session'];
                                                $semester = $row1['semester'];
                                                echo"<option value='$testid'> $testname-$testtypename- $session </option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b> Username</b></td>
                                    <td> <input type="text" name="username" value=""/></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>
                                        <button name="" id="btn-nxt-step1" class="btn btn-primary">Load Candidate's Profile</button> 
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div id="second-step" style='display:none;'>
                </div>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
        <script type ="text/javascript" src ="<?php echo siteUrl("assets/js/select2.min.js"); ?>"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $("#testid").select2();
            });

            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());
            
            $(document).on("click","#btn-nxt-step1", function(event){
                $.ajax({
                    type:'post',
                    url:'candidate_details.php',
                    data:$("#frm1").serialize()  
                                                                    
                }).done(function(msg){
                    $("#second-step").empty().html(msg).slideDown();
                    $("#first-step").slideUp();
                                
                    $("#contentframe", top.document).height(0).height($(document).height());
                });
                return false;
            });
                                                           
            $(document).on("click", "#btn-bk-step2", function(event){
                                                               
                $("#first-step").slideDown();
                $("#second-step").slideUp();
                $("#contentframe", top.document).height(0).height($(document).height());

                return false;
            });
                                                                                            
            $(document).on("click","#btn-nxt-step2", function(event){
                $.ajax({
                    type:'post',                                                                                                    
                    url:'restore_candidate.php',
                    data:$("#frm1").serialize()                                                                    
                }).done(function(){
                    alert('You have succesfully restore the candidate');
                });
                return false;                
            });
        </script>
    </body>
</html>
