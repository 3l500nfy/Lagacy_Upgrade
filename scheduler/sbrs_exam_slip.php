
<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();

$venueid = $_GET['vid'];

$tid = $_GET['tid'];
//$RegNo = $_SESSION['RegNo'];
$matricnum = $_SESSION['matricnum'];
$sex = $_SESSION['gender'];
$surname = $_SESSION['surname'];
$firstname = $_SESSION['firstname'];
$othernames = $_SESSION['othernames'];
$combination= $_SESSION['combination'];
//$picupload = $_SESSION['pictureuploadpath'];


$schd = get_schedule_id($tid, $venueid);
$cid = get_candidate_id($matricnum, 2);
$sbjs = get_subject_selection_as_array($schd, $cid);
//salis code..
$centreid = get_centre_id($venueid);

$date= get_exam_date($schd,$tid);
$time= get_exam_time($tid);

//$course = get_programme_name($progid);



$query = "SELECT * FROM tblstudents WHERE matricnumber='$matricnum'";
$stmt = $dbh->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
//$fullname = <?php echo "<td>" . strtoupper($firstname) . ", " . ucfirst(strtolower($surname)) . " " . ucfirst(strtolower($othernames)) . " </td>"; 


$state = $row['StateOfOrigin'];
//$fac = $row['Faculty'];
//$course = $row['Course'];

$centre = get_centre_name($centreid);
$venue = get_venue_name($venueid);
$location= get_venue_location($venueid);
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
        
       <?php include_once "../partials/jsimports.php"; ?>

    </head>
    <body>

        <div id="slipdiv">
            <div class="row">
                <img  src="../assets/img/dariya_logo1.png" alt="banner" style="width: 968px; height: 129px" >
            </div>
            <div>
                <table id="sliptb" class="table table-bordered">
                    <tr>
                        <th colspan="4" class="center"><h2>SBRS Computer Based Test-Test/Exam Slip</h2></th>
                    </tr>

                    <tr>                                           
                        <th colspan="4" style="text-align:left"><h3>PERSONAL DETAILS</h3></th> 
                    </tr>
                    <tr>
                        <td>Candidate's Full Name:</td>

                        <?php
                        echo "<td colspan='2'>" . strtoupper($firstname) . ", " . ucfirst(strtolower($surname)) . " " . ucfirst(strtolower($othernames)) . "</td>";
                        ?>

                        <td  rowspan="6">
                            <?php
                            if (!isset($matricnum))
                                echo "<img src='../assets/img/photo.png'/>";
                            else
                                echo "<img src='../picts/".$matricnum.".jpeg"."'"."/>";
                            ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Reg No.:</td>
                        <td colspan="2"><?php echo strtoupper($matricnum); ?></td>
                   
                    </tr>

                    <tr>
                        <td>Sex:</td>
                        <td colspan="2"><?php echo ucwords(strtolower($sex)); ?></td>
                        
                    </tr>
                   
                   
                    <tr>
                        <td>Combination:</td>
                        <td colspan="2"><?php echo $combination; ?></td>
                        
                    </tr>

                    <tr>
                        <th colspan="4" style="text-align:left"><h3>SUBJECT</h3></th>
                    </tr>

                    <tr>
                        <td>Subject Title:</td>
                        <td>

                            <?php
                            for ($q = 0; $q < count($sbjs); $q++) {
                                $sbjnm = get_subject_name($sbjs[$q]);
                                echo"$sbjnm";
                            }
                            ?>

                        </td>
                        <td class="bold">Subject Code:</td>
                        <td>

                            <?php
                            for ($q = 0; $q < count($sbjs); $q++) {
                                $sbjnm = get_subject_code($sbjs[$q]);
                                echo"$sbjnm";
                            }
                            ?>

                        </td>
                        
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
                        <td><?php echo ucwords($location);?></td>
                        <td class="bold">Date:</td>
                        <td><?php echo $date; ?></td>
                    </tr>
                    
                    <tr>
                        <td>Batch:</td>
                        <td><?php echo $batch; ?></td>
                        <td class="bold">Time:</td>
                        <td><?php echo $time."am"; ?></td>
                    </tr>


                    <tr>

                        <td colspan="4" class="center"><button id="printslip" onclick="window.print();" class="btn-primary">Print Test/Exam Slip</button></td>
                    </tr>
                </table>
            </div>

        </div>
    </body>
</html>


