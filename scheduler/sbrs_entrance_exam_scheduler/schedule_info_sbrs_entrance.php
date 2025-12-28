<?php
if (!isset($_SESSION))
    session_start();
//echo $_SESSION['tid'];
$tid = $_SESSION['tid'];

$_SESSION = array();
$_SESSION['tid'] = $tid;
require_once("../../lib/globals.php");
require_once("../../lib/candid_scheduling_func.php");
openConnection();

$ApplicationID = clean($_POST['ApplicationID']);



$query = "SELECT * FROM tblsbrsshortlist WHERE ApplicationID=?";
$stmt = $dbh->prepare($query);
$stmt->execute($ApplicationID);

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $ApplicationID = $row['ApplicationID'];
    $Programmecode = $row['Programmecode'];
    $surname = $row['surname'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $gender = $row['gender'];
    $dob = $row['dob'];
    $stateOforigin = $row['stateOforigin'];
    $lga = $row['lga'];
    $admission_status = $row['admission_status'];
}

$_SESSION['ApplicationID'] = $ApplicationID;
$_SESSION['Programmecode'] = $Programmecode;
$_SESSION['surname'] = $surname;
$_SESSION['firstname'] = $firstname;
$_SESSION['lastname'] = $lastname;
$_SESSION['gender'] = $gender;
$_SESSION['dob'] = $dob;
$_SESSION['stateOforigin'] = $stateOforigin;
$_SESSION['lga'] = $lga;
$_SESSION['admission_status'] = $admission_status;




$cid = get_candidate_id($ApplicationID, 4);

$schd = get_schedule_ids_as_array($tid);
//echo $schd;
$schds = trim(implode(",", $schd), ",");
//echo $schds;
$query = "SELECT * from tblcandidatestudent where candidateid= ? && scheduleid IN (?)";
$stmt = $dbh->prepare($query);
$stmt->execute(array($cid,$schds));
$numrows = $stmt->rowCount();

if ($numrows > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cschd = $row['scheduleid'];

    $cvenue = get_venue_id($cschd);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Computer Based Test</title>
        <?php javascriptTurnedOff(); ?>
        <link type="text/css" href="../../assets/css/qconfig.css" rel="stylesheet"></link>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript" src="../../assets/js/jquery-1.7.2.min.js"></script>

        <script type ="text/javascript">
            var firstload=false;
            var filename="";
            var uploaderr="";
            
            
            function reloadpix()
            {
                
                if(filename!=="")
                {
                    document.images['livepic'].src="../../picts/"+filename;
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

                <?php require_once '../schedule_header.php'; ?>

                <div class ="row">
                    <?php
                    if (isset($cschd))
                        echo"<div class='center'>You have already been to scheduled. click <a href='sbrs_entrance_exam_slip.php?tid=" . $tid . "&vid=" . $cvenue . "'>here</a> to print your exams slip.</div>";
                    else {
                        ?>
                        <div class ="span9">
                            <form action="" method="post" class="style-frm">

                                <table class="style-tbl" style="width: 1000px">
                                    <caption><span style="text-align: center; font-weight: bold; font-size: 25px ">SBRS Extrance Examination Scheduling Platform</span></caption>
                                    <br><br>
                                    
                                    <tr>                                           
                                        <th colspan="3" style="height: 40px"><h1>PERSONAL DETAILS</h1></th> 
                                    </tr>
                                    <tr>
                                        <td width = "200"><b>Candidate's Name :</b></td>

                                        <?php
                                        echo "<td>$surname" . " " . "$firstname" . " " . "$lastname</td>";
                                        ?>

                                        <td rowspan="5" style="padding-left: 180px">

                                            <div class ="span2">
                                                <img id="progpic" src="../../assets/img/loading.gif" style="display:none;"/>
                                                <p style="color: red;">Upload your passport using the link below.</p>
                                                <br />                                    

                                                <div>
                                                    <table class="table table-striped">
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <img id ="livepic" src="../../assets/img/photo.png"  width="150px" height="200px"/>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                     
                                                        <tr>
                                                            <td>
                                                                <!-- <button class="button" id="buttonUpload" onClick="return ajaxFileUpload();">Upload</button>-->
                                                                <input type="hidden" name="picupload" id="picupload" value="default.png"/>
                                                                <iframe frameborder="0" src="uploadfrm.php" scrolling="no" style="width:150px; border:none; overflow-y: no-display; max-height: 50px; padding:0px; margin:0px; border-width: 0px; border-style: none;"></iframe>

                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                        </td>

                                    </tr>
                                    <tr>
                                        <td><b>Application ID :</b></td>
                                        <?php
                                        echo "<td>$ApplicationID</td>";
                                        ?>
                                    </tr>


                                    <tr>
                                        <td><b>Programme:</b></td>
                                        <?php
                                        echo "<td>$Programmecode</td>";
                                        ?>

                                    </tr>
                                    <tr>
                                        <td><b>State of Origin :</b></td>

                                        <?php
                                        echo "<td>$stateOforigin</td>";
                                        ?>

                                    </tr>
                                    <tr>
                                        <td><b>LGA:</b></td>
                                        <?php
                                        echo "<td>$lga</td>";
                                        ?>

                                    </tr>



                                </table>

                            </form>
                        </div>            	
                        
                    </div>

                    <div span="12">
                        <br><br>
                        <form action="" method="" class="style-frm">
                            <center><button type="button" class="btn btn-primary" id="btn-schedule">Click to Schedule</button></center>
                        </form>
                         <br><br>
                    </div>
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
                
                    /* if(($("#subj1").val()== "") || ($("#subj2").val()== "") || ($("#subj3").val() == "") || ($("#subj4").val() == "")|| ($("#subj5").val() == ""))
                    {
                        alert("Subject selection not complete");
                        return false;
                    }*/
                
               
                    return true;
                });                      

            </script>
        </center>
    </body>
</html>
