
<?php
if (!isset($_SESSION))
    session_start();

$tid = $_SESSION['tid'];
$_SESSION = array();
$_SESSION['tid'] = $tid;

//echo $_SESSION['tid'];
//echo 'welcome';

require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();

$matricnum = ((isset($_POST['matric'])) ? (clean($_POST['matric'])) : (""));
$pass = ((isset($_POST['pass'])) ? (clean($_POST['pass'])) : (""));

if ($matricnum == "") {
    header("Location:candidate_login_student.php?errmatric=matric");
    exit();
} else
if ($pass == "") {
    header("Location:candidate_login_student.php?errpassword=password");
    exit();
}

$query = "SELECT * FROM tblstudents 
                    JOIN tblstate
                    ON tblstudents.state=tblstate.stateid
                    WHERE tblstudents.matricnumber='$matricnum' AND loginpassword='$pass'";

$stmt = $dbh->prepare($query);
$result = $stmt->execute();
$numrows = $stmt->rowCount();


if ($numrows == 0) {

    header("Location:candidate_login_student.php?errcandid=invalid");

    exit();
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $studentid = $row['studentid'];
    $other_regnum = $row['other_regnum'];
    $matricnumber = $row['matricnumber'];
    $plancode = $row['plancode'];
    $surname = $row['surname'];
    $firstname = $row['firstname'];
    $othernames = $row['othernames'];
    $gender = $row['gender'];
    $dob = $row['dob'];
    $lga = $row['lga'];
    $state = $row['state'];
    $nationality = $row['nationality'];
    $entrylevel = $row['entrylevel'];
    $entrysession = $row['entrysession'];
    $modeofentry = $row['modeofentry'];
    $contactaddress = $row['contactaddress'];
    $homeaddress = $row['homeaddress'];
    $residency = $row['residency'];
    $gsmnumber = $row['gsmnumber'];
    $email = $row['email'];
    $programmeadmitted = $row['programmeadmitted'];
    $yearadmitted = $row['yearadmitted'];
    $loginpassword = $row['loginpassword'];
    $enablestd = $row['enablestd'];
    $passwordBK = $row['passwordBK'];
    $stateid = $row['stateid'];
    $statename = $row['statename'];
}


$_SESSION['studentid'] = $studentid;
$_SESSION['other_regnum'] = $other_regnum;
$_SESSION['matricnumber'] = $matricnumber;
$_SESSION['plancode'] = $plancode;
$_SESSION['surname'] = $surname;
$_SESSION['firstname'] = $firstname;
$_SESSION['othernames'] = $othernames;
$_SESSION['gender'] = $gender;
$_SESSION['dob'] = $dob;
$_SESSION['lga'] = $lga;
$_SESSION['state'] = $state;
$_SESSION['nationality'] = $nationality;
$_SESSION['entrylevel'] = $entrylevel;
$_SESSION['entrysession'] = $entrysession;
$_SESSION['modeofentry'] = $modeofentry;
$_SESSION['contactaddress'] = $contactaddress;
$_SESSION['homeaddress'] = $homeaddress;
$_SESSION['residency'] = $residency;
$_SESSION['gsmnumber'] = $gsmnumber;
$_SESSION['email'] = $email;
$_SESSION['programmeadmitted'] = $programmeadmitted;
$_SESSION['yearadmitted'] = $yearadmitted;
$_SESSION['loginpassword'] = $loginpassword;
$_SESSION['enablestd'] = $enablestd;
$_SESSION['passwordBK'] = $passwordBK;
$_SESSION['statename'] = $statename;
//$_SESSION['picupload'] = $FILES['picupload'];
//salis codes...

$deptid = get_dept_id($programmeadmitted);

$dept = get_dept_name($deptid);

$_SESSION['dept'] = $dept;

$fid = faculty_id($deptid);

$faculty = get_faculty_name($fid);

$_SESSION['faculty'] = $faculty;

$cid = get_candidate_id($matricnum, 3);

$schd = get_schedule_ids_as_array($tid);
//echo $schd;
$schds = trim(implode(",", $schd), ",");

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
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
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
                <div class ="row">
                <?php
                if (isset($cschd))
                    echo"<center><div class='center'>You have already been to scheduled. click <a href='regular_exam_slip.php?tid=" . $tid . "&vid=" . $cvenue . "'>here</a> to print your exams slip.</div></center>";
                else {
                    ?>
                        <div class ="span8">
                            <form action ="" method ="post">
                                <table id="infotb" class="style-tbl" style="width: 1000px">
                                    <tr>                                           
                                        <th colspan="3" style="height: 40px"><h1>PERSONAL DETAILS</h1></th> 
                                    </tr>
                                    <tr>
                                        <td width = "200">Full Name: </td>
    <?php
    echo "<td>" . strtoupper($firstname) . ", " . ucfirst(strtolower($surname)) . " " . ucfirst(strtolower($othernames)) . " </td>";
    ?>
                                        <td rowspan="5" style="padding-left: 180px">
                                            <div class ="span3">
                                                <img id="progpic" src="../assets/img/loading.gif" style="display:none;"/>
                                                <p style="color: red;">Upload your passport</p>
                                                <br /> 

                                                <div>
                                                    <table class="table table-striped">
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <img id ="livepic" src="../assets/img/photo.png"  width="150px" height="200px"/>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                      
                                                        <tr>
                                                            <td>
                                                                <!-- <button class="button" id="buttonUpload" onClick="return ajaxFileUpload();">Upload</button>-->
                                                                <input type="hidden" name="picupload" id="picupload" value="default.png"/>
                                                                <iframe frameborder="0" src="uploadfrm3.php" scrolling="no" style="width:150px; border:none; overflow-y: no-display; max-height: 50px; padding:0px; margin:0px; border-width: 0px; border-style: none;"></iframe>

                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            </div>



                                        </td>
                                    </tr>
                                    <tr>
                                        <td>State of Origin:</td>
    <?php
    echo "<td>" . ucwords(strtolower($statename)) . " </td>";
    ?>
                                    </tr>
                                    <tr>
                                        <td>Faculty:</td>
    <?php
    echo "<td>" . $faculty . "</td>";
    ?>
                                    </tr>
                                    <tr>
                                        <td>Department:</td>
    <?php
    echo "<td>" . $dept . "</td>";
    ?>
                                    </tr>
                                    <tr>
                                        <td>Course:</td>
    <?php
    echo "<td>" . get_programme_name($programmeadmitted) . "</td>";
    ?>
                                    </tr>

                                </table>
                            </form>
                        </div>
                <br><br> 
                    <div class="row">
                        <div class="span11">
                            <form action="schedule_summary_student.php" method="post" class="style-frm">
                                <table class="style-tbl" style="width: 1000px">
                                    <tr>                                           
                                        <th colspan="4" style="height: 40px"><h1>FILL SCHEDULE DETAILS</h1></th> 
                                    </tr>
                                    <tr>
                                        <td><b>Select test/exam:</b></td>
                                        <td>

                                            <select name="test" id="test">
                                                <option value ="">--Select test--</option>

    <?php
    $query = "SELECT * FROM tbltestconfig INNER JOIN tbltestcode ON(tbltestconfig.testcodeid = tbltestcode.testcodeid) INNER JOIN tbltesttype ON(tbltesttype.testtypeid = tbltestconfig.testtypeid) WHERE tbltestconfig.testcodeid<>1 && tbltestconfig.testcodeid<>2 ORDER BY session DESC limit 100";
    $stmt = $dbh->prepare($query);
    $result = $stmt->execute();
    $numrows = $stmt->rowCount();

    if ($numrows > 0) {
        while ($trow = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $tid = $trow['testid'];
            $query = "select * from tblscheduling where testid='?'"; // echo $sql2;
            $stmt = $dbh->prepare($query);
            $result = $stmt->execute($tid);
            $numrows = $stmt->rowCount();
            if ($numrows == 0)
                continue;
            $tsession = $trow['session'];
            $tcode = $trow['testname'];
            $ttype = $trow['testtypename'];
            $tsemester = (($trow['semester'] == 1) ? ("First") : (($trow['semester'] == 2) ? ("Second") : ("Third")));
            echo"<option value='$tid'>$tsession/$ttype/$tcode/$tsemester Semester</option>";
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
    $result = $stmt->execute();
    $numrows = $stmt->rowCount();
    if ($numrows > 0) {
        while ($ctid = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $centreid = $ctid['centreid'];
            $query = "SELECT * FROM tblcentres  where centreid=?";
            $stmt = $dbh->prepare($query);
            $result = $stmt->execute($centreid);
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
