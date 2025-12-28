<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!isset($_POST['tid']))
    header("Location:" . siteUrl("403.php"));

$testid = clean($_POST['tid']);
if (!is_test_administrator_of($testid))
    header("Location:" . siteUrl("403.php"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet">
    <style type="text/css">
        .treport {
            margin-left: 100px;
            width: 80%;
        }

        .theading, .theading td {
            border-style: solid;
            border-width: 2px;
            border-color: transparent;
            border-collapse: collapse;
        }

        .theading td {
            padding: 3px;
        }

        .reportsumm {
            margin-left: 100px;
            width: 80%;

        }
    </style>
    <script type="text/javascript">
    </script>
</head>
<body style="background-image: url('../img/bglogo2.jpg');">
<?php
if (!isset($_FILES['candidate-list'])) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>System cannot find any uploaded excel file!</div>";
    exit();
}

if (!isset($_POST['sheet'])) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Invalid Sheet selection!</div>";
    exit();
}

if (!isset($_POST['schd'])) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Please Select at least one schedule!</div>";
    exit();
}

$sheetindex = clean($_POST['sheet']);
$column = clean($_POST['column']);
$schedules = $_POST['schd'];
$schedules = array_values($schedules);

$test_config = get_test_config_param_as_array($testid);

if (strtoupper(trim($test_config['testcodeid'])) == "1") {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Candidate list upload not supported on Post-UTME Test!</div>";
    exit();
}

$list = $_FILES['candidate-list'];

//process the file
$sFileExtension = "";
$imgfile_name = $list['name']; // get client side file name
$imgfile = $list['tmp_name']; // temporary file at server side
if ($imgfile_name) { // if file is uploaded
    $aFileNameParts = explode(".", $imgfile_name);
    $sFileExtension = end($aFileNameParts); // part behind last dot
    if (($sFileExtension != "xls") && ($sFileExtension != "xlsx")) {
        echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Invalid file type. Require excel file with (.xls or .xlsx) extension!</div>";
        exit();
    }
} else {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File not uploaded via browser!</div>";
    exit();
}

$imgfile_size = $list['size']; // size of uploaded file
if ($imgfile_size == 0) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File not uploaded via browser!</div>";
    exit();
}

//begin to process file and transfer to server
$final_filename = time() . $testid;
$final_filename = "$final_filename.$sFileExtension";
$newfile = $final_filename;

/* == do extra security check to prevent malicious abuse== */
if (!is_uploaded_file($imgfile)) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File not uploaded via browser!</div>";
    exit();
}
$output = "<table class='treport'><tr class='theading'><th>S/N</th><th>Registration No.</th><th>Cell</th><th>Remark</th></tr>";
$scheduled = 0;
$nonexistence = 0;
$alreadyscheduled = 0;
//determine if excel 2007 or 2003 format
$aFileNameParts = explode(".", $newfile);

$sFileExtension = end($aFileNameParts); // part behind last dot

if ($sFileExtension == "xls") {//2003 format
    require_once('../../lib/excel_lib/phpexcel/Classes/PHPExcel/Reader/Excel5.php');

    $objReader = new PHPExcel_Reader_Excel5();
} elseif ($sFileExtension == "xlsx") {//2007format
    require_once('../../lib/excel_lib/phpexcel/Classes/PHPExcel/Reader/Excel2007.php');

    $objReader = new PHPExcel_Reader_Excel2007();
}

$objReader->setReadDataOnly(true);

$objPHPExcel = $objReader->load($imgfile);

$totalsheet = count($objPHPExcel->getAllSheets());

$objPHPExcel->setActiveSheetIndex(($sheetindex - 1));
$sheet = $objPHPExcel->getActiveSheet();
$maxrows = $objPHPExcel->getActiveSheet()->getHighestRow();
if ($maxrows == 0) {
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File content is empty!</div>";
    exit();
}
$fullcount = 0;

$occupiedSchedules = array();
$activeSchedule = 0;
$test_subjects = get_test_subjects_as_array($testid);
$recordProcessed = 0;

if (count($test_subjects) == 0) {// found no subject in the test
    echo "<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test has no subject registered!</div>";
    exit();
}
$test_subjects_str = trim(implode(",", $test_subjects), " ,");
//candidates test type
$testtype = (($test_config['testcodeid'] != "2" && $test_config['testcodeid'] != "12" && $test_config['testcodeid'] != "1") ? ("REGULAR") : ($test_config['testname']));

//already scheduled candidates
$schds = array();
$schds = get_test_schedule_as_array($testid);
// $schds[] = 0;
$schds_str = trim(implode(",", $schds), " ,");

$stdtype = 'REGULAR';


if ($stdtype == "REGULAR")
    $sql = "select matricnumber as reg, studentid as candid from tblstudents inner join tblcandidatestudent on (tblstudents.studentid = tblcandidatestudent.candidateid) where scheduleid in ($schds_str)";
else
    if ($stdtype == "Post-UTME")
        $sql = "select RegNo as reg from tbljamb inner join tblcandidatestudent on (tbljamb.RegNo = tblcandidatestudent.matric) where scheduleid in ($schds_str)";
    else
        if ($stdtype == "SBRS")
            $sql = "select sbrsno as reg from tblsbrsstudents inner join tblcandidatestudent on (tblsbrsstudents.sbrsno = tblcandidatestudent.matric) where scheduleid in ($schds_str)";

        else
            if ($stdtype == "SBRS-NEW")

                $sql = "select RegNo as reg from tbljamb inner join tblcandidatestudent on (tbljamb.RegNo = tblcandidatestudent.matric) where scheduleid in ($schds_str)";

$stmt = $dbh->prepare($sql);
$stmt->execute();

$scheduledcand = array();

while ($data1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $scheduledcand[] = $data1['reg'];
}

///list of candidates
$listofcand = array();
for ($i = 1; $i <= $maxrows; $i++) {
    $matric = $sheet->getCell($column . $i)->getCalculatedValue();
    $listofcand[] = strtoupper($matric);
}

$list = str_replace(",", "', '", "'" . implode(",", $listofcand) . "'");

// Process each candidate
for ($i = 1; $i <= $maxrows; $i++) {
    $matric = $sheet->getCell($column . $i)->getCalculatedValue();
    $matric = strtoupper(trim($matric));
    $activecell = strtoupper($column . $i);
    
    if (empty($matric)) {
        $output .= "<tr class='alert-error'><td>" . $i . "</td><td>" . $matric . "</td><td>[" . $activecell . "]</td><td>Empty cell</td></tr>";
        continue;
    }
    
    if (!candid_exist($matric, $testtype)) {
        $output .= "<tr class='alert-error'><td>" . $i . "</td><td>" . $matric . "</td><td>[" . $activecell . "]</td><td>Student does not exist!</td></tr>";
        $nonexistence++;
        continue;
    }
    
    if (is_scheduled($testid, $matric, $testtype)) {
        $output .= "<tr class='alert-notice'><td>" . $i . "</td><td>" . $matric . "</td><td>[" . $activecell . "]</td><td>Student is already scheduled!</td></tr>";
        $alreadyscheduled++;
        continue;
    }
    
    // Get the next available schedule
    if ($activeSchedule >= count($schedules)) {
        $output .= "<tr class='alert-error'><td>" . $i . "</td><td>" . $matric . "</td><td>[" . $activecell . "]</td><td>No available schedule!</td></tr>";
        continue;
    }
    
    $schedule = $schedules[$activeSchedule];
    
    // Get candidate ID
    $candidateid = get_candidate_id($matric, (($testtype == "REGULAR") ? (3) : (($testtype == "Post-UTME") ? (1) : (($testtype == "SBRS") ? (2) : (4)))));
    
    $success = true;
    foreach ($test_subjects as $tsbj) {
        // First insert into tblcandidatestudent and get the auto-increment id
        // Insert with placeholder candidateid=0 (required field, will be updated immediately)
        $query = "insert into tblcandidatestudent (scheduleid, subjectid, candidateid) values (?,?,0)";
        $stmt = $dbh->prepare($query);
        $result1 = $stmt->execute(array($schedule, $tsbj));
        
        if (!$result1) {
            $success = false;
            break;
        }
        
        // Get the auto-increment id
        $new_id = $dbh->lastInsertId();
        
        // Update the candidateid with the new id
        $query = "update tblcandidatestudent set candidateid = ? where id = ?";
        $stmt = $dbh->prepare($query);
        $result1 = $stmt->execute(array($new_id, $new_id));
        
        if (!$result1) {
            $success = false;
            break;
        }
        
        // Update the existing record in tblscheduledcandidate with the new id
        // Find the record by RegNo and candidatetype (created by get_candidate_id)
        $candtype_val = (($testtype == "REGULAR") ? (3) : (($testtype == "Post-UTME") ? (1) : (($testtype == "SBRS") ? (2) : (4))));
        $query = "update tblscheduledcandidate set scheduleid = ?, subjectid = ?, add_index = 0, testid = ?, candidateid = ? where UPPER(RegNo) = UPPER(?) and candidatetype = ?";
        $stmt = $dbh->prepare($query);
        
        $result2 = $stmt->execute(array(
            $schedule,       // scheduleid
            $tsbj,          // subjectid
            $testid,        // testid
            $new_id,        // candidateid (using the new id from tblcandidatestudent)
            $matric,        // RegNo (for WHERE clause)
            $candtype_val   // candidatetype (for WHERE clause)
        ));
        
        if (!$result2) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        $output .= "<tr class='alert-success'><td>" . $i . "</td><td>" . $matric . "</td><td>[" . $activecell . "]</td><td>Student was scheduled successfully!</td></tr>";
        $scheduled++;
        $recordProcessed++;
        
        // Move to next schedule if current one is full
        if ($recordProcessed >= get_schedule_capacity($schedule)) {
            $activeSchedule++;
            $recordProcessed = 0;
        }
    } else {
        $output .= "<tr class='alert-error'><td>" . $i . "</td><td>" . $matric . "</td><td>[" . $activecell . "]</td><td>Error during scheduling!</td></tr>";
    }
}

$output .= "</table>";

// Summary section with original styling
$output = "<div class='alert-notice reportsumm'><h3>Summary</h3>"
    . "<b>Total Record Processed:</b> " . $maxrows . ", <br />"
    . "<b>Successful:</b> " . $scheduled . ", <br />"
    . "<b>Already Scheduled:</b> " . $alreadyscheduled . ", <br />"
    . "<b>Invalid Candidate:</b> " . $nonexistence . "</div>" . $output;

echo $output;
?>
<a href="upload.php?tid=<?php echo $testid; ?>" target="contentframe">Back to candidate upload form.</a>

<script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
</body>
</html>