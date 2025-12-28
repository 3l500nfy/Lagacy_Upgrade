
<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once("../lib/candid_scheduling_func.php");
openConnection();

$venueid = $_GET['vid'];
$centerid = $_GET['centid'];
$tid = $_GET['tid'];
$RegNo = $_SESSION['RegNo'];
$sex = $_SESSION['Sex'];

$schd = get_schedule_id($tid, $venueid);
$cid = get_candidate_id($RegNo, 1);
$sbjs = get_subject_selection_as_array($schd, $cid);

$query = "SELECT * FROM tbljamb WHERE RegNo ='?'";
$stmt = $dbh->prepare($query);
$stmt->execute($RegNo);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$fullname = $row['CandName'];
$state = $row['StateOfOrigin'];
$fac = $row['Faculty'];
$course = $row['Course'];

$centre = get_centre_name($centerid);
$venue = get_venue_name($venueid);
$batch = get_curr_batch($schd, $cid);
$location= get_venue_location($venueid);
$time= get_exam_time($tid);
$date= get_exam_date($schd,$tid);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>POST UTME EXAMINATION SLIP</title>
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
                width:900px;
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

            #sliptb tr td:first-child
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
        <script type="text/javascript" src="jquery-1.7.2.min.js"></script>

    </head>
    <body>

        <div id="slipdiv">
            <div class="center">
                <img  src="../assets/img/dariya_logo1.png" alt="banner" style="width: 850px; height: 120px" >
            </div>
            
            <div>
                <table id="sliptb" style="width: 850px; height: 129px; padding-left: 30px">
                    <tr>
                        <th colspan="4" class="center"><h2>Computer Based Test-Test/Exam Slip</h2></th>
                    </tr>
                    
                    <tr>                                           
                        <th colspan="4" style="text-align:left"><h3>PERSONAL DETAILS</h3></th> 
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td colspan="2"><?php echo ucwords(strtolower($fullname)); ?></td>
                         <td  rowspan="6">
                            <?php
                             embed_user_pic($RegNo);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Reg No.:</td>
                        <td colspan="2"><?php echo strtoupper($RegNo); ?></td>
                    </tr>
                    <tr>
                        <td>State of Origin:</td>
                        <td colspan="2"><?php echo ucwords(strtolower($state)); ?></td>
                    </tr>
                    <tr>
                        <td>Sex:</td>
                        <td colspan="2"><?php echo ucwords(strtolower($sex)); ?></td>
                    </tr>
                    <tr>
                        <td>Faculty Applied:</td>
                        <td colspan="2"><?php echo ucwords(strtolower($fac)); ?></td>
                    </tr>
                    <tr>
                        <td>Proposed Course of Study:</td>
                        <td colspan="2"><?php echo ucwords(strtolower($course)); ?></td>
                    </tr>
                    
                     <tr>
                        <th colspan="4" style="text-align:left"><h3>SUBJECT</h3></th>
                    </tr>
                    
                    
                    <tr>
                        <td>Subject Combination:</td>
                        <td>
                            <ul>
                                <?php
                                for ($q = 0; $q < count($sbjs); $q++) {
                                    $sbjnm = get_subject_name($sbjs[$q]);
                                    echo"<li>$sbjnm</li>";
                                }
                                ?>
                            </ul>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <th colspan="4" style="text-align:left"><h3>SCHEDULE INFORMATION</h3></th>
                    </tr>
                    
                    <tr>
                        <td>Centre:</td>
                        <td><?php echo ucwords($centre); ?></td>
                        <td style="font-weight: bold">Venue:</td>
                        <td><?php echo ucwords($venue); ?></td>
                    </tr>
                    
                      <tr>
                        <td>Location:</td>
                        <td><?php echo ucwords($location);?></td>
                        <td style="font-weight: bold">Date:</td>
                        <td><?php echo $date; ?></td>
                    </tr>
                    <tr>
                        <td>Batch:</td>
                        <td><?php echo $batch; ?></td>
                         <td style="font-weight: bold">Time:</td>
                        <td><?php echo $time."am"; ?></td>
                        
                    </tr>
                    
                      <tr>

                        <td colspan="4" class="center"><button id="printslip" onclick="window.print();" class="btn-primary">Print Slip</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>

