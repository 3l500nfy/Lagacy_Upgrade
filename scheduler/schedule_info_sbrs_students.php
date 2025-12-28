<?php
if (!isset($_SESSION))
    session_start();

$tid = $_SESSION['tid'];
$_SESSION = array();
$_SESSION['tid'] = $tid;

require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();

$matricnum = ((isset($_POST['matric'])) ? (clean($_POST['matric'])) : (""));
$pass = ((isset($_POST['pass'])) ? (clean($_POST['pass'])) : (""));

if ($matricnum == "") {
    header("Location:candidate_login_sbrs.php?errmatric=matric");
    exit();
} else if ($pass == "") {
    header("Location:candidate_login_sbrs.php?errpassword=password");
    exit();
}

// Convert SBRS number to uppercase for comparison
$matricnum = strtoupper($matricnum);

$query = "SELECT * FROM tblsbrsstudents WHERE UPPER(sbrsno) = ? AND loginpassword = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($matricnum, $pass));
$numrows = $stmt->rowCount();

if ($numrows == 0) {
    // Log the failed attempt for debugging
    error_log("Failed login attempt - SBRS: $matricnum");
    header("Location:candidate_login_sbrs.php?errcandid=invalid");
    exit();
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $studentid = isset($row['studentid']) ? $row['studentid'] : '';
    $oldsbrsno = isset($row['oldsbrsno']) ? $row['oldsbrsno'] : '';
    $matricnum = isset($row['sbrsno']) ? $row['sbrsno'] : '';
    $jambno = isset($row['jambno']) ? $row['jambno'] : '';
    $combination = isset($row['combination']) ? $row['combination'] : '';
    $surname = isset($row['surname']) ? $row['surname'] : '';
    $firstname = isset($row['firstname']) ? $row['firstname'] : '';
    $othernames = isset($row['othernames']) ? $row['othernames'] : '';
    $gender = isset($row['gender']) ? $row['gender'] : '';
    $dob = isset($row['dob']) ? $row['dob'] : '';
    $lga = isset($row['lga']) ? $row['lga'] : '';
    $lgac = isset($row['lgac']) ? $row['lgac'] : '';
    $state = isset($row['state']) ? $row['state'] : '';
    $statec = isset($row['statec']) ? $row['statec'] : '';
    $nationality = isset($row['nationality']) ? $row['nationality'] : '';
    $entrysession = isset($row['entrysession']) ? $row['entrysession'] : '';
    $entrylevel = isset($row['entrylevel']) ? $row['entrylevel'] : '';
    $contactaddress = isset($row['contactaddress']) ? $row['contactaddress'] : '';
    $gsmnumber = isset($row['gsmnumber']) ? $row['gsmnumber'] : '';
    $email = isset($row['email']) ? $row['email'] : '';
    $loginpassword = isset($row['loginpassword']) ? $row['loginpassword'] : '';
    $firstchoice = isset($row['firstchoice']) ? $row['firstchoice'] : '';
    $secondchoice = isset($row['secondchoice']) ? $row['secondchoice'] : '';
    $firstc = isset($row['firstc']) ? $row['firstc'] : '';
    $secondc = isset($row['secondc']) ? $row['secondc'] : '';
    $cgpa = isset($row['cgpa']) ? $row['cgpa'] : '';
    $enablestd = isset($row['enablestd']) ? $row['enablestd'] : '';
}


$_SESSION['studentid'] = $studentid;
$_SESSION['oldsbrsno'] = $oldsbrsno;
$_SESSION['matricnum'] = $matricnum;
$_SESSION['jambno'] = $jambno;
$_SESSION['combination'] = $combination;
$_SESSION['surname'] = $surname;
$_SESSION['firstname'] = $firstname;
$_SESSION['othernames'] = $othernames;
$_SESSION['gender'] = $gender;
$_SESSION['dob'] = $dob;
$_SESSION['lga'] = $lga;
$_SESSION['lgac'] = $lgac;
$_SESSION['state'] = $state;
$_SESSION['statec'] = $statec;
$_SESSION['nationality'] = $nationality;
$_SESSION['entrylevel'] = $entrylevel;
$_SESSION['entrysession'] = $entrysession;
$_SESSION['contactaddress'] = $contactaddress;
$_SESSION['gsmnumber'] = $gsmnumber;
$_SESSION['email'] = $email;
$_SESSION['loginpassword'] = $loginpassword;
$_SESSION['firstchoice'] = $firstchoice;
$_SESSION['secondchoice'] = $secondchoice;
$_SESSION['firstc'] = $firstc;
$_SESSION['secondc'] = $secondc;
$_SESSION['cgpa'] = $cgpa;
$_SESSION['enablestd'] = $enablestd;

//$_SESSION['picupload'] = $FILES['picupload'];
//salis codes...
$cid = get_candidate_id($matricnum, 2);

$schd = get_schedule_ids_as_array($tid);
if (empty($schd)) {
    header("Location:candidate_login_sbrs.php?errcandid=invalid");
    exit();
}

// Create placeholders for the IN clause
$placeholders = str_repeat('?,', count($schd) - 1) . '?';
$query = "SELECT * FROM tblcandidatestudent WHERE candidateid = ? AND scheduleid IN ($placeholders)";
$stmt = $dbh->prepare($query);

// Combine parameters for the query
$params = array_merge([$cid], $schd);
$stmt->execute($params);
$numrows = $stmt->rowCount();
//echo $query;
if ($numrows > 0) {
    $row = $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cschd = $row['scheduleid'];
    $cvenue = get_venue_id($cschd);
}

//end here...
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Computer Based Test</title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
        </style>

        <script type="text/javascript" src="../assets/js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="../assets/js/ajaxfileupload.js"></script>
        <script type="text/javascript">
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
        <style type="text/css">
            #infotb tr td:first-child{
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <center>
        <div id="container" class="container">
            <div class="header">

                <?php require_once 'schedule_header.php'; ?>


                <?php
                if (isset($cschd))
                    echo"<center><div class='center'>You have already been to scheduled. click <a href='sbrs_exam_slip.php?tid=" . $tid . "&vid=" . $cvenue . "'>here</a> to print your exams slip.</div></center>";
                else {
                    ?>
                    <div class ="span8">
                        <form action ="" method ="post" class="style-frm">
                            <table id="infotb" class="style-tbl" style="width: 1000px">
                                <th colspan="3" style="height: 40px"><h1>PERSONAL DETAILS</h1></th> 
                                <tr>
                                    <td width = "200">Full Name: </td>
                                    <?php
                                    echo "<td style='width: 400px'>" . strtoupper($firstname) . ", " . ucfirst(strtolower($surname)) . " " . ucfirst(strtolower($othernames)) . " </td>";
                                    ?>
                                    
                                     <td rowspan="4" style="padding-left: 100px">
                                        <div class ="span2">
                                            <img id="progpic" src="../assets/img/loading.gif" style="display:none;"/>
                                            <p style="color: red;">Upload your passport using the link below.</p>
                                                                              

                                            <div>
                                                <table class="style-tbl">
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <img id ="livepic" src="../assets/img/photo.png"  width="150px" height="200px"/>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                
                                                    <tr>
                                                        <td>
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
                                    <td>First Choice(Programme) :</td>
                                    <?php
                                    echo "<td>" . $_SESSION['firstc'] . "</td>";
                                    ?>
                                </tr>
                                <tr>
                                    <td>Second Choice(Programme):</td>
                                    <?php
                                    echo "<td>" . $_SESSION['secondc'] . "</td>";
                                    ?>
                                </tr>
                                <tr>
                                    <td>Combination</td>
                                    <?php
                                    $query="SELECT combination FROM tblsbrsstudents WHERE sbrsno='$matricnum'";
                                    $stmt = $dbh->prepare($query);
                                    $stmt->execute();
                                    $combination=  $stmt->fetchColumn();
                                   // echo $combination;
                                    echo "<td>". $combination."</td>";
                                    ?>
                                </tr>
                            </table>
                        </form>
                    </div>
                <br>
                <br>
                <div class="row">
                    <div class="span11">
                        <form action="schedule_summary_sbrs_student.php" method="post" class="style-frm">
                            <table class="style-tbl" style="width: 1000px">
                                <th colspan="4" style="height :50px"><h1>SCHEDULE INFORMATION</h1></th>
                                <tr>
                                    <td><b>Select test/exam:</b></td>
                                    <td>

                                        <select name="test" id="test">
                                            <option value ="">--Select test--</option>

                                            <?php
                                            $query = "SELECT * FROM tbltestconfig INNER JOIN tbltestcode ON(tbltestconfig.testcodeid = tbltestcode.testcodeid) inner join tbltesttype on(tbltesttype.testtypeid = tbltestconfig.testtypeid) where tbltestconfig.testcodeid<>1";

                                            $stmt = $dbh->prepare($query);
                                            $stmt->execute();
                                            $numrows = $stmt->rowCount();



                                            if ($numrows > 0) {
                                                while ($trow = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                                    $tid = $trow['testid'];
                                                    $query = "SELECT * FROM tblscheduling WHERE testid='$tid'"; // echo $sql2;
                                                    $stmt = $dbh->prepare($query);
                                                    $stmt->execute();
                                                    $numrows = $stmt->rowCount();
                                                    if ($numrows == 0)
                                                        continue;
                                                    $tsession = $trow['session'];
                                                    $tcode = $trow['testname'];
                                                    $ttype = $trow['testtypename'];
                                                    $tsemester = (($trow['semester'] == 1) ? ("First") : (($trow['semester'] == 2) ? ("Second") : ("Third")));
                                                    echo"<option value='$tid'>$ttype/$tcode/$tsemester/$tsession</option>";
                                                }
                                            }
                                            ?>
                                        </select>

                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td><b>Preferred Centre</b></td>
                                    <td>
                                        <select name="centre" id="centre">
                                            <option value ="">--Select Center--</option>
                                            <?php
                                            $query = "SELECT centreid FROM tblvenue WHERE venueid IN (SELECT venueid FROM tblscheduling WHERE testid= $tid)";
                                            $stmt = $dbh->prepare($query);
                                                    $stmt->execute();
                                                    $numrows = $stmt->rowCount();
                                            if ($numrows > 0) {
                                                while ($ctid = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $cid = $ctid['centreid'];
                                                    $query = "SELECT * FROM tblcentres  where centreid='$cid'";
                                                    $stmt = $dbh->prepare($query);
                                                    $stmt->execute();
                                                    $numrows = $stmt->rowCount();
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $centreid = $row['centreid'];
                                                        $name = $row['name'];
                                                        echo "<option value ='$centreid'>$name</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><b>Preferred Venue</b></td>
                                    <td>
                                        <select name="venue" id="venue">
                                            <option value ="">--Select Venue--</option>

                                        </select>
                                    </td>
                                </tr>

                                <tr>

                                    <td colspan="4">
                                <center><button type ="submit" name ="" class ="btn btn-primary" id ="continue_btn">Continue</button></center>
                                </td>
                                </tr>
                            </table>
                        </form>
                    </div>            	
                </div>     	
            </div>
        </div>
        <?php
    }
    ?>
    <script type ="text/javascript">
            
        $(document).ready(function(){
                
            $("#centre").live("change", function(){
                $("#venue").html("<option value=''>loading...</option>").load("getVenue_Scheduleinfo.php", {centreid: $(this).val()});
            });
                
                
                
            $("#continue_btn").live("click", function(){
                if($("#test").val()=="")
                {
                    alert("Select a test/exam");
                    return false;
                }
                 
                if(($("#venue").val()== ""))
                {
                    alert("Select a venue.");
                    return false;
                }
                return true;
                //window.location.href = "schedule_summary_student.php ";
            });
        });                        
    </script>
        </center>
</body>
</html>
