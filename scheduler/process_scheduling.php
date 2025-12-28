<?php
if (!isset($_SESSION))
    session_start();
	
require_once("../lib/globals.php");
//require_once("../lib/candid_scheduling_func.php");
require_once("../lib/cbt_func.php");


//$fac = $_SESSION['faculty1'];
//$state = $_SESSION['statename'];
//$RegNum = $_SESSION['RegNum'];
if(!isset($_GET['faculty1']) || !isset($_GET['statename']) || !isset($_GET['RegNum']) || !isset($_GET['gsm']) || !isset($_GET['email'])){
    echo"<div>Invalid parameter!. Click here to go <a href='http://putme.abu.edu.ng'>back...</a></div>";
    exit();
}
define('ENCRYPTION_KEY', 'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282');
 
function mc_decrypt($decrypt, $key){
    //return  $decrypt; exit;
    //$decrypt= urldecode($decrypt);
$decrypt = explode('|', $decrypt);
$decoded = base64_decode($decrypt[0]);
$iv = base64_decode($decrypt[1]);
if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return FALSE; }
$key = pack('H*', $key);
$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
$mac = substr($decrypted, -64);
$decrypted = substr($decrypted, 0, -64);
$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
if($calcmac!==$mac){ return FALSE; }
$decrypted = unserialize($decrypted);
//echo $decrypted;exit;
return $decrypted;

}

 $fac= $_GET['faculty1'];
 $state= $_GET['statename'];
 $RegNum= $_GET['RegNum'];
 $gsm= $_GET['gsm'];
 $email= $_GET['email'];
//  $fac= mc_decrypt($_GET['faculty1'], ENCRYPTION_KEY);
// $state= mc_decrypt($_GET['statename'], ENCRYPTION_KEY);
// $RegNum= mc_decrypt($_GET['RegNum'], ENCRYPTION_KEY);
// $gsm= mc_decrypt($_GET['gsm'], ENCRYPTION_KEY);
// $email= mc_decrypt($_GET['email'], ENCRYPTION_KEY);
//get state id
 $link=openConnection();
 
$query = "SELECT stateid FROM tblstate WHERE statename='?'";
$stmt = $dbh->prepare($query);
$result = $stmt->execute($state);
$numrows = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stateid = $row['stateid'];
//echo $stateid; exit;

/*
 * Query the database to confirm that the candidate has passed payment process.
 * 
 */

openConnection();
//get faculty id
$query = "SELECT facultyid FROM tblfaculty WHERE name ='?'";
$stmt = $dbh->prepare($query);
$stmt->execute($fac);
$numrows = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$facid = $row['facultyid'];


//get testid
//$session=2017;
$session = date('Y');
$query = "SELECT * FROM tbltestconfig WHERE testcodeid='1' && testtypeid='1' && semester =0 && session=$session ORDER BY session DESC limit 1";
$stmt = $dbh->prepare($query);
$stmt->execute();
$numrows = $stmt->rowCount();
$tid = 0;
if ($numrows == 0) {
    //echo appropriate message
    echo "<div>No available test. Click here to go <a href='http://putme.abu.edu.ng'>back...</a></div>";
    exit;
} else {
    $tidrow = $stmt->fetch(PDO::FETCH_ASSOC);
    $tid = $tidrow['testid'];
}

if(is_scheduled($tid, $RegNum, "Post-UTME")){
    $schedule_detail= get_jamb_candidate_schedule_details($RegNum, $tid);
    $centername=$schedule_detail['centername'];
    $venuename=$schedule_detail['venuename'];
    $batch=$schedule_detail['batch'];
    $startdatetime=$schedule_detail['candidatestartdatetime'];
    $startdate=$startdatetime->format("l, jS F, Y");
    $starttime=$startdatetime->format("g:i a");
}else{
$schedules = get_schedules_mapped_to_faculty_as_array($facid, $tid);
//echo (count($schedules)); exit();

if (!count($schedules)) {
    //Notify candidate that there is no schedule available
    echo "<div>No schedule available. Click here to go <a href='http://putme.abu.edu.ng'>back...</a></div>";
    exit();
}

//search for available schedule
$candidateSchedule = null;
$freeslot = 0;
$schd_count = 0;

do {
    $randindex = mt_rand(0, (count($schedules) - 1));
    $freeslot = get_schedule_freeslot($schedules[$randindex]);
    $now = new DateTime();
    $schid_date = get_schedule_datetime($schedules[$randindex]);
    $intaval = ($now->getTimestamp()) - ($schid_date->getTimestamp());

    if ($freeslot == 0 ) {
        unset($schedules[$randindex]);
        $schedules = array_values($schedules);
        continue;
    } else {
        $schd_count = get_candidate_scheduled_count($schedules[$randindex]);
        $candidateSchedule = $schedules[$randindex];
        break;
    }
} while (count($schedules));

//if schedule is exausted
if (!$candidateSchedule) {
    //notify the candidate that all schedules are filled up
    echo "<div>All schedules filled up. Click here to go <a href='http://putme.abu.edu.ng'>back...</a></div>"; exit();
}

$schedule_config = get_schedule_config_param_as_array($candidateSchedule);
$test_config = get_test_config_param_as_array($schedule_config['testid']);

$timepadding = $test_config['time_padding'];
$duration = $test_config['duration'];
$starttime = $schedule_config['dailystarttime'];
$maximumbatch = $schedule_config['maximumbatch'];
$noperbatch = $schedule_config['noperbatch'];
$scheduledate = $schedule_config['date'];
$centername = $schedule_config['centername'];
$venuename = $schedule_config['venuename'];
$totaldur = $duration + $timepadding;

//calculate batch
$batch = floor($schd_count / $noperbatch) + 1;

//calculate starttime
$testdateobj = new DateTime($scheduledate . " " . $starttime);
//$stt = $totaldur * ($batch - 1) + 1;
$stt = $totaldur * ($batch - 1);
$testdateobj->add(new DateInterval("PT" . $stt . "M"));
$startdate = $testdateobj->format("l, jS F, Y");
$starttime = $testdateobj->format("g:i a");

//determine the candidates subject combination
//$subjects = get_jamb_subject_combination($RegNum);
$subjects = get_jamb_subject_combination($RegNum);
if (count($subjects) != 4) {
    //appropriate message
    echo "incomplete subject combination";
    exit();
}

$candidateid = get_candidate_id($RegNum, 1);
//perform scheduling
foreach ($subjects as $tsbj) {
    $query = "INSERT INTO tblcandidatestudent (scheduleid, candidateid, subjectid) VALUES ('$candidateSchedule','$candidateid','$tsbj')";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $numrows = $stmt->rowCount();
}
}
$candid_detail= get_jamb_candidate_details($RegNum);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Computer Based Test</title>
<?php javascriptTurnedOff(); ?>
        <link type="text/css" href="../assets/css/qconfig.css" rel="stylesheet"></link>

<?php ?>
        <script type="text/javascript" src="../assets/js/jquery-1.7.2.min.js"></script>
          <script type="text/javascript">
            $(function() {
              //  window.print();
            });

        </script>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <!--
        <style type="text/css">
            
            .style1 {	font-size: x-large;
                      font-weight: bold;
            }
            .style2 {	font-size: 18px;
                      font-weight: bold;
            }
            .pie_rounded_corner {background:#fff;border:1px solid #363;width:90%;height:90%;padding-top:20px; margin:auto;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;-webkit-box-shadow:#ccc 0 2px 3px;-moz-box-shadow:#ccc 0 2px 3px;	box-shadow:#999 0 2px 3px;}

            
        
        </style>
-->
    </head>
    <body>
    <center>
        <div id="container" class="pie_rounded_corner" style="margin-top: 20px">
            <div class="header">
                <?php echo "<img alt='abu-logo' height='127' src='../assets/img/banner.png' style='width: 950px;'>"?>
            </div>

            <div style="width: 850px; margin: 30px auto;"><h2 style="text-align: center;"></h2>
                <table width="100%" border="1" align="center" cellpadding="1" cellspacing="0" bordercolor="#ffffff">
                    <tr bordercolor="#F0F0F0">
                        <td bordercolor="#FFFFFF"><div align="center"><span class="style1"><u>POST UTME ACKNOWLEDGEMENT/EXAMINATION SLIP</u></span></div></td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                        <td>&nbsp;</td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                        <td><fieldset>
                                <legend class="style2">Personal Details</legend>
                                <table width="100%" border="1" cellspacing="0" cellpadding="2" bordercolor="#ffffff">
                                    <tr>
                                        <td width="32%">JAMB Reg. Number: </td>
                                        <td width="68%"><?php echo $candid_detail['regno'];  ?></td>
                                        <td width="68%" rowspan="7"><img width='150px' height='170px' style='border:solid 0.5px;' src='<?php echo  "http://putme.abu.edu.ng/candpixs/".$RegNum.".JPG"; ?>'></td>
                                    </tr>
                                    <tr>
                                        <td>Name: </td>
                                        <td><?php echo $candid_detail['candname']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Gender: </td>
                                        <td><?php echo $candid_detail['sex']; ?></td>
                                    </tr>

                                    <tr>
                                        <td>Email:</td>
                                        <td><?php echo $email; ?></td>
                                    </tr>
                                    <tr>
                                        <td>GSM:</td>
                                        <td><?php echo $gsm; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Faculty applied</td>
                                        <td><?php echo $candid_detail['faculty'];?></td>
                                    </tr>
                                    <tr>
                                        <td>Course of Study: </td>
                                        <td><?php echo $candid_detail['course'];?></td>
                                    </tr>
                                    <tr>
                                        <td>JAMB Total Score: </td>
                                        <td><?php echo $candid_detail['totalscore'];?></td>
                                    </tr>
                                </table>
                            </fieldset></td>
                    </tr>

                    <tr bordercolor="#FFFFFF">
                        <td><fieldset>
                                <legend class="style2">Post UME Examination Subjects</legend>
                                <table width="100%" border="1" cellspacing="0" cellpadding="2" bordercolor="#ffffff">
                                    <tr>
                                        <td width="25%">1.</td>
                                        <td width="75%">English Language</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td><?php echo $candid_detail['subjcode2'];?></td>
                                    </tr>
                                    <tr>
                                        <td>3.</td>
                                        <td><?php echo $candid_detail['subjcode3']; ?></td>
                                    </tr>
                                    <tr>
                                        <td valign="top">4.</td>
                                        <td><?php echo $candid_detail['subjcode4']; ?></td>
                                    </tr>
                                </table>
                            </fieldset></td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                        <td><fieldset>
                                <legend class="style2">Post UME Test Schedule</legend>
                                <table width="100%" border="1" cellspacing="0" cellpadding="2" bordercolor="#ffffff">
                                    <tr>

                                        <td width="25%">Center:</td>
                                        <td><?php echo $centername;?></td>
                                    </tr>
                                    <tr>

                                        <td width="25%">Venue:</td>
                                        <td><?php echo $venuename;?></td>
                                    </tr>
                                    <tr>

                                        <td width="25%">Batch:</td>
                                        <td><?php echo $batch;?></td>
                                    </tr>
                                    <tr>

                                        <td width="25%">Date:</td>
                                        <td><?php echo $startdate; ?></td>
                                    </tr>
                                    <tr>

                                        <td width="25%">Time:</td>
                                        <td><?php echo $starttime;?></td>
                                    </tr>
                                </table>
                            </fieldset></td>
                    </tr>
                    <tr>
                    <tr>
                        <td bordercolor="#FFFFFF"><div align="justify"><strong>Please bring 2 copies of this slip with you on the day of your examination. You will not be allowed to write the Post UME Examination if you fail to  present this slip and will also forfeit your chance of being considered for admission.</strong></div></td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                        <td>&nbsp;</td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                        <td><div align="center">
                                <input name="print" type="button" class="btn" value="Print Slip" onClick="javascript:window.print(); location.href='menu.php'; " />
                                &nbsp;&nbsp;
                                <input name="close" type="button" class="btn" value="Close" onClick="javascript:location.href = 'http://putme.abu.edu.ng/index.php';" />
                            </div></td>
                    </tr>
                </table>
                <p style="text-align: center;">&nbsp;</p>
                <div class="footer" style="width: 100%; clear: both;">	
                    <span style="position: relative; float: right; height: 20px; border-bottom: 2px #3A5D00 solid; "> &copy;2011-Ahmadu Bello University </span></div>
            </div>

        </div>

    </center>
</body>
</html>
