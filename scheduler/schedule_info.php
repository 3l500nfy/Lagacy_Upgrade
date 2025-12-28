<?php
if (!isset($_SESSION))
    session_start();


require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();



 $fac= $_SESSION['faculty1'];
 
 $state= $_SESSION['statename'];
 $RegNumb= $_SESSION['RegNum'];

 $query="SELECT stateid FROM tblstate WHERE statecode='?'";
 $stmt = $dbh->prepare($query);
$stmt->execute($state);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
    
 $stateid=$row['stateid'];
  //echo $stateid; exit;
 
                 

$query = "SELECT * FROM tbltestconfig WHERE testcodeid='1' && testtypeid='1' && semester =0 ORDER BY session DESC limit 1";
$stmt = $dbh->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$numrows = $stmt->rowCount();

$tid = 0;

if ($numrows == 0)
    header("Location: index.php?tconfig=0");
else {
    $tidrow = $stmt->fetch(PDO::FETCH_ASSOC);
    $tid = $tidrow['testid'];
//echo $tid; exit();

    $query = "SELECT * FROM tblexamsdate WHERE testid = '$tid' && date >=now() ORDER by date ASC limit 1";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $numrows = $stmt->rowCount();
    if ($numrows == 0)
        header("Location: index.php?tconfig=0");
    else {

       // $tid = $_SESSION['tid'];
        $tid = 38;

        $_SESSION = array();
        $_SESSION['tid'] = $tid;


        $query = "SELECT * from tbljamb 
                                JOIN tblstate
                                ON tblstate.statename=tbljamb.StateOfOrigin
                                WHERE tbljamb.RegNo='?' AND tblstate.stateid ='?'
                                ";
//echo $sql;exit();
                            $stmt = $dbh->prepare($query);
                            $stmt->execute(array($RegNumb,$stateid));
                            
                            $numrows = $stmt->rowCount();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $id = $row['id'];
            $DeptSn = $row['DeptSn'];
            $RegNo = $row['RegNo'];
            $CandName = $row['CandName'];
            $StateOfOrigin = $row['StateOfOrigin'];
            $Sex = $row['Sex'];
            $Age = $row['Age'];
            $EngScore = $row['EngScore'];
            $Subj2 = $row['Subj2'];
            $Subj2Score = $row['Subj2Score'];
            $Subj3 = $row['Subj3'];
            $Subj3Score = $row['Subj3Score'];
            $Subj4 = $row['Subj4'];
            $Subj4Score = $row['Subj4Score'];
            $TotalScore = $row['TotalScore'];
            $Faculty = $row['Faculty'];
            $Course = $row['Course'];
            $stateid = $row['stateid'];
            $statename = $row['statename'];
        }

        $_SESSION['RegNo'] = $RegNo;
        $_SESSION['CandName'] = $CandName;
        $_SESSION['StateOfOrigin'] = $StateOfOrigin;
        $_SESSION['Sex'] = $Sex;
        $_SESSION['Age'] = $Age;
        $_SESSION['EngScore'] = $EngScore;
        $_SESSION['Subj2'] = $Subj2;
        $_SESSION['Subj2Score'] = $Subj2Score;
        $_SESSION['Subj3'] = $Subj3;
        $_SESSION['Subj3Score'] = $Subj3Score;
        $_SESSION['Subj4'] = $Subj4;
        $_SESSION['Subj4Score'] = $Subj4Score;
        $_SESSION['TotalScore'] = $TotalScore;
        $_SESSION['Faculty'] = $Faculty;
        $_SESSION['Course'] = $Course;
        $_SESSION['stateid'] = $stateid;
        $_SESSION['statename'] = $statename;



        $cid = get_candidate_id($RegNumb, 1);
//echo $cid;exit();
        $schd = get_schedule_ids_as_array($tid);
//var_dump($schd);exit();
        $schds = trim(implode(",", $schd), ",");
//echo $schds;
        $query = "SELECT * from tblcandidatestudent WHERE candidateid= '$cid' && scheduleid IN ($schds)";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $numrows = $stmt->rowCount();

        if ($numrows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cschd = $row['scheduleid'];
            $cvenue = get_venue_id($cschd);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Computer Based Test</title>
        <?php javascriptTurnedOff(); ?>
        <link type="text/css" href="../assets/css/qconfig.css" rel="stylesheet"></link>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <?php //require_once("../partials/cssimports.php")  ?>
        <script type="text/javascript" src="../assets/js/jquery-1.7.2.min.js"></script>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type ="text/javascript">
            var firstload=false;
            var filename="";
            var uploaderr="";
            
            
            function reloadpix()
            {
                
                if(filename!=="")
                {
                    document.images['livepic'].src="../picts/"+filename;
                    document.getElementById("picupload").value=filename;
                    filename="";
                }
                else
                {
                    alert(uploaderr);
                    uploaderr="";
                }
             
            }
        </script>

    </head>
    <body>
    <center>
        <div id="container" class="container">
            <div class="header">
                <?php require_once 'schedule_header.php'; ?>


                <?php
                if (isset($cschd))
                    echo"<div class='center'>You have already been to scheduled. click <a href='putme_exam_slip.php?tid=" . $tid . "&vid=" . $cvenue . "'>here</a> to print your exams slip.</div>";
                else {
                    ?>
                    <div class ="span9" style="margin-top: 20px">
                        <form action="" method="post">


                            <table class="style-tbl" style="width: 1000px">

                                <th colspan="3" style="height: 40px"><h1>PERSONAL DETAILS</h1></th> 

                                <tr>
                                    <td width = "200"><b>Candidate's Name :</b></td>

                                    <?php
                                    echo "<td style='width: 400px'>$CandName</td>";
                                    ?>
                                    <td rowspan="6" style="padding-left: 100px">
                                        <div class ="span2">
                                            <div>
                                                <table class="style-tbl">
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <?php
                                                                embed_user_pic($RegNo);
                                                                ?>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                
                                                </table>
                                            </div>
                                        </div>


                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Jamb Reg.No :</b></td>
                                    <?php
                                    echo "<td>$RegNo</td>";
                                    ?>
                                </tr>
                                <tr>
                                    <td><b>State of Origin :</b></td>

                                    <?php
                                    echo "<td>$statename</td>";
                                    ?>

                                </tr>

                                <tr>
                                    <td><b>Faculty Applied :</b></td>
                                    <?php
                                    echo "<td>$Faculty</td>";
                                    ?>

                                </tr>
                                <tr>
                                    <td><b>Course of Study :</b></td>
                                    <?php
                                    echo "<td>$Course</td>";
                                    ?>

                                </tr>
                                <tr>
                                    <td><b>Jamb Total Score :</b></td>
                                    <?php
                                    echo "<td>$TotalScore</td>";
                                    ?>

                                </tr>
                                <tr>


                                </tr>



                            </table>

                        </form>
                    </div>            	
                    <br>
                    <br><br>

                    <div class="row">
                        <div class="span11">
                            <table class="style-tbl" style="width: 1000px">
                                <tr>
                                    <th colspan="3" style="height: 40px"><h1>UTME SUBJECTS DETAILS</h1></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>JAMB SUBJECTS</th>
                                    <th>SCORE</th>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>English</td>

                                    <?php
                                    echo " <td>$EngScore </td>";
                                    ?>

                                </tr>
                                <tr>
                                    <td>2.</td>

                                    <?php
                                    echo " <td>$Subj2 </td>";
                                    echo " <td>$Subj2Score </td>";
                                    ?>

                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <?php
                                    echo " <td>$Subj3 </td>";
                                    echo " <td>$Subj3Score </td>";
                                    ?>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <?php
                                    echo " <td>$Subj4 </td>";
                                    echo " <td>$Subj4Score </td>";
                                    ?>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>TOTAL SCORE</td>
                                    <?php
                                    echo "<td> $TotalScore</td>";
                                    ?>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br><br>
                    <div span="12">
                        <form action="" method="" class="style-frm">
                            <center><button type="button" class="btn btn-primary" id="btn-schedule">Schedule Your PUTME Test</button></center>
                        </form>
                    </div>
                    <br><br><br>
                    <div class="row" id="scheduling">

                    </div>

                </div>
                <?php
            }
            ?>
            <script type ="text/javascript">
            
                $(document).ready(function(){
                
                    $("#btn-schedule").live("click", function(){
                        $("#scheduling").html("Loading...");
                        $("#scheduling").load("scheduling.php");
                    });
                
          
                });    
        
                    

                
                $("#centre").live("change", function(){
                    $("#venue").load("getVenue_Scheduleinfo.php", {centreid: $(this).val()});
                });
                
                
                
                $("#continue_btn2").live("click", function(){
                    //alert("here"); return false;
            
                    if(($("#venue").val()== ""))
                    {
                        alert("Select a venue.");
                        return false;
                    }
                
                    if(($("#subj2").val()== "") || ($("#subj3").val()== "") || ($("#subj4").val() == "") )
                    {
                        alert("Subject selection not complete");
                        return false;
                    }
                
                    if(($("#subj2").val()== $("#subj3").val()) || ($("#subj2").val()== $("#subj4").val()) || ($("#subj3").val() == $("#subj4").val()) )
                    {
                        alert("Repetition of subject is not allowed.");
                        return false;
                    }
                    return true;
                });                      

            </script>
    </center>
</body>
</html>








