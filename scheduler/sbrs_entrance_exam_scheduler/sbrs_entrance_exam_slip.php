
<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
require_once("../../lib/candid_scheduling_func.php");
openConnection();

$venueid = $_GET['vid'];

$tid = $_GET['tid'];

$ApplicationID = $_SESSION['ApplicationID'];
$sex = $_SESSION['gender'];
$surname = $_SESSION['surname'];
$firstname = $_SESSION['firstname'];
$othernames = $_SESSION['lastname'];

$schd = get_schedule_id($tid, $venueid);
$cid = get_candidate_id($ApplicationID, 4);

$centreid = get_centre_id($venueid);

$date = get_exam_date($schd, $tid);
$time = get_exam_time($tid);

$query = "SELECT * FROM tblstudents WHERE matricnumber=?";
$stmt = $dbh->prepare($query);
$stmt->execute($ApplicationID);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$state = $row['StateOfOrigin'];

$centre = get_centre_name($centreid);
$venue = get_venue_name($venueid);
$location = get_venue_location($venueid);
$batch = get_curr_batch($schd, $cid);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>TEST/EXAMINATION SLIP</title>
        <script>
            function printpage(){
                
                window.print();
            }
            
        
        </script>
        <style>
            .modaldialog{
                display: none;
            }

            .center { text-align: center;}
            #slipdiv
            {
                margin-left: auto;
                margin-right: auto;
                width:1000px;
                border-style: solid;
                border-width: 1px;
                border-color: #cccccc;
                -moz-border-radius:3px;
                border-radius:3px;
                -webkit-border-radius:3px;
                padding:5px;
                -webkit-box-shadow:  0px 2px 4px rgba(0, 0, 0, 0.5);
                -moz-box-shadow:  0px 2px 4px rgba(0, 0, 0, 0.5);
                box-shadow:  0px 2px 4px rgba(0, 0, 0, 0.5);

            }

            #sliptb   
            {
                width: 975px;
            }

            #sliptb tr td:first-child 
            {
                font-weight: bold;
                vertical-align: top;
                padding-right: 15px;

            }
            .bold
            {
                font-weight: bold;
                vertical-align: top;
                padding-right: 15px;
            }

            @media print
            {
                #printslip{
                    display:none;
                }
            }
        </style>

        <?php include_once "../../partials/jsimports.php"; ?>

    </head>
    <body>

        <div id="slipdiv">
           <div class="row">
                <img  src="../assets/img/dariya_logo1.png" alt="banner" style="width: 968px; height: 129px" >
            </div>
            <div>
                <table id="sliptb" class="table table-bordered">
                    <tr>
                        <th colspan="4" class="center"><h2>SBRS Entrance Examination Slip</h2></th>
                    </tr>

                    <tr>                                           
                        <th colspan="4" style="text-align:left"><h3>PERSONAL DETAILS</h3></th> 
                    </tr>
                    <tr>
                        <td>Candidate's Full Name:</td>

                        <?php
                        echo "<td colspan='2'>" . strtoupper($firstname) . ", " . ucfirst(strtolower($surname)) . " " . ucfirst(strtolower($othernames)) . "</td>";
                        ?>

                        <td  rowspan="4">
                            <?php
                            echo "<img src='../../assets/img/photo.png'/>";
                            if (!isset($ApplicationID))
                                echo "<img src='../../assets/img/photo.png'/>";
                            else
                                echo "<img src='../../picts/" . $ApplicationID . ".jpeg" . "'" . "/>";
                            ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Application Form No.:</td>
                        <td colspan="2"><?php echo strtoupper($ApplicationID); ?></td>

                    </tr>
                       <tr>
                        <td>State of Origin:</td>
                        <td colspan="2">
                            <?php
                                $query="SELECT stateOforigin FROM tblsbrsshortlist WHERE ApplicationID=?";
                                $stmt = $dbh->prepare($query);
                                $stmt->execute($ApplicationID);
                                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $row[0];
                        ?>
                        </td>
                     
                        
                    </tr>
                    <tr>
                            <td>LGA:</td>
                        <td colspan="2">
                            <?php
                                $query="SELECT lga FROM tblsbrsshortlist WHERE ApplicationID=?";
                                $stmt = $dbh->prepare($query);
                                $stmt->execute($ApplicationID);
                                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $row[0];
                        ?>
                        </td>
                    </tr>
                     <tr>
                            <td>Gender:</td>
                        <td colspan="2">
                            <?php
                                $query="SELECT gender FROM tblsbrsshortlist WHERE ApplicationID=?";
                                $stmt = $dbh->prepare($query);
                                $stmt->execute($ApplicationID);
                                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $row[0];
                        ?>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="4" style="text-align:left"><h3>PREFERED SUBJECTS</h3></th>
                    </tr>
                    
                    <?php
                        $query="SELECT subjectid FROM tblcandidatestudent WHERE scheduleid=? AND candidateid=?";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute(array($schd,$cid);
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            
                        }
                            
                    ?>

                    <tr>
                        <td colspan="2" style="font-weight:normal"><?php echo 'Mathematics'?></td>
                        <td colspan="2" ><?php echo 'English Language'?></td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight:normal"><?php  echo 'Physics'?></td>
                        <td colspan="2"><?php  echo 'Chemistry'?></td>
                    </tr>
                    <tr>
                        <td colspan="4"  style="font-weight:normal"><?php  echo 'Geography'?></td>
                    </tr>

                    <tr>
                        <th colspan="4" style="text-align:left"><h3>SCHEDULE INFORMATION</h3></th>
                    </tr>


                    <tr>
                        <td>Centre:</td>
                        <td><?php echo ucwords($centre); ?></td>
                        <td class="bold">Venue:</td>
                        <td><?php echo ucwords($venue); ?></td>

                    </tr>

                    <tr>
                        <td>Location:</td>
                        <td><?php echo ucwords($location); ?></td>
                        <td class="bold">Date:</td>
                        <td><?php echo $date; ?></td>
                    </tr>

                    <tr>
                        <td>Batch:</td>
                        <td><?php echo $batch; ?></td>
                        <td class="bold">Time:</td>
                        <td><?php echo $time . "am"; ?></td>
                    </tr>


                    <tr>

                        <td colspan="4" class="center"><button id="printslip" onclick="window.print();" class="btn-primary">Print Test/Exam Slip</button></td>
                    </tr>
                </table>
            </div>

        </div>
    </body>
</html>


