
<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();

ini_set("memory_limit", "256M");
ini_set('max_execution_time', 300);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <style type="text/css">
            .treport{
                margin-left: 100px;
                width:80%;
            }
            .theading, .theading td{
                border-style: solid;
                border-width:2px;
                border-color:transparent;
                border-collapse: collapse;
            }

            .theading td{
                padding:3px;
            }

            .reportsumm{
                margin-left: 100px;
                width:80%;
            }
        </style>

    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <?php
        if (!isset($_FILES['file'])) {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>System cannot find any uploaded excel file!</div>";
            exit();
        }

        $list = $_FILES['file'];

//process the file
        $sFileExtension = "";
        $imgfile_name = $list['name']; // get client side file name
        $imgfile = $list['tmp_name']; // temporary file at server side 
        if ($imgfile_name) { // if file is uploaded 
            $aFileNameParts = explode(".", $imgfile_name);
            $sFileExtension = end($aFileNameParts); // part behind last dot
            if (($sFileExtension != "xls") && ($sFileExtension != "xlsx")) {
                echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Invalid file type. Require excel file with (.xls or .xlsx) extension!</div>";
                exit();
            }
        } else {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File not uploaded via browser!</div>";
            exit();
        }

        $imgfile_size = $list['size']; // size of uploaded file 
        if ($imgfile_size == 0) {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File not uploaded via browser!</div>";
            exit();
        }

//begin to process file and transfer to server
        $final_filename = time();
        $final_filename = "$final_filename.$sFileExtension";
        $newfile = $final_filename;

        /* == do extra security check to prevent malicious abuse== */
        if (!is_uploaded_file($imgfile)) {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File not uploaded via browser!</div>";
            exit();
        }

        $output = "<table class='treport'><tr class='theading'><th>S/N</th><th colspan='2'>Registration No.</th><th>Remark</th></tr>";
        $imported = 0;
        $alreadyImported = 0;

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
        $sheetindex = 1;
        $objPHPExcel->setActiveSheetIndex(($sheetindex - 1));
        $sheet = $objPHPExcel->getActiveSheet();
        $maxrows = $objPHPExcel->getActiveSheet()->getHighestRow();

        if ($maxrows == 0) {
            echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>File content is empty!</div>";
            exit();
        }

        $recordProcessed = 0;

        for ($i = 2; $i <= $maxrows; $i++) {
            $recordProcessed++;

            // Use getValue() instead of getCalculatedValue() to avoid PHP 7.4 type checking issues
            // getValue() returns the raw cell value, getCalculatedValue() processes formulas
            $regnum = clean($sheet->getCell('A' . $i)->getValue());
            $surname = clean($sheet->getCell('B' . $i)->getValue());
            $firstname = clean($sheet->getCell('C' . $i)->getValue());
            $othernames = clean($sheet->getCell('D' . $i)->getValue());
            $gender = clean($sheet->getCell('E' . $i)->getValue());
            $dob_raw = $sheet->getCell('F' . $i)->getValue();
            $entry_level = clean($sheet->getCell('G' . $i)->getValue());
            $entry_session = clean($sheet->getCell('H' . $i)->getValue());
            $mode_of_entry = clean($sheet->getCell('I' . $i)->getValue());
            $contact_address = clean($sheet->getCell('J' . $i)->getValue());
            $home_address = clean($sheet->getCell('K' . $i)->getValue());
            $gsm_number = clean($sheet->getCell('L' . $i)->getValue());
            $email = clean($sheet->getCell('M' . $i)->getValue());
            $year_admitted = clean($sheet->getCell('N' . $i)->getValue());
            $login_password = clean($sheet->getCell('O' . $i)->getValue());

            // Validate required fields
            if ($regnum == "") {
                $recordProcessed--;
                continue;
            }
            
            // Ensure matricnumber and loginpassword are not empty (required fields)
            if (empty($login_password)) {
                $login_password = $regnum; // Default to registration number if empty
            }
            
            // Convert Excel date to MySQL DATE format
            $dob = null;
            if (!empty($dob_raw)) {
                $dob_cleaned = trim($dob_raw);
                
                // If it's an Excel serial date (numeric), convert it using PHPExcel
                if (is_numeric($dob_cleaned) && class_exists('PHPExcel_Shared_Date')) {
                    try {
                        $excelBaseDate = PHPExcel_Shared_Date::ExcelToPHP($dob_cleaned);
                        $dob = date('Y-m-d', $excelBaseDate);
                    } catch (Exception $e) {
                        // Fall through to string parsing
                    }
                }
                
                // If not converted yet, try to parse as date string
                if ($dob === null && !empty($dob_cleaned)) {
                    // Try common date formats
                    $date_formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d', 'Y-m-d H:i:s'];
                    foreach ($date_formats as $format) {
                        $parsed = DateTime::createFromFormat($format, $dob_cleaned);
                        if ($parsed !== false) {
                            $dob = $parsed->format('Y-m-d');
                            break;
                        }
                    }
                    // If parsing failed, try strtotime as last resort
                    if ($dob === null) {
                        $timestamp = strtotime($dob_cleaned);
                        if ($timestamp !== false) {
                            $dob = date('Y-m-d', $timestamp);
                        }
                    }
                }
            }
            
            // If date conversion failed, set to NULL (allows NULL in database)
            if ($dob === false || empty($dob)) {
                $dob = null;
            }
            
            $testtype = "REGULAR";

/*            if (candid_exist($regnum, $testtype)) {
                $alreadyImported++;
                $output.="<tr class='alert-error'><td>" . ($recordProcessed) . "</td> <td colspan='2'>" . strtoupper($regnum) . "</td><td>Already exist as Student!</td></tr>";
                continue;
            }
*/
            //Inserting into tblstudents

            $query = "INSERT IGNORE INTO tblstudents(other_regnum,matricnumber,surname,firstname,othernames,gender,dob,entrylevel,entrysession,modeofentry,contactaddress, 
homeaddress,gsmnumber,email,yearadmitted,loginpassword)VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $stmt=$dbh->prepare($query);
           $result = $stmt->execute(array($regnum,$regnum,$surname,$firstname,$othernames,$gender,$dob,$entry_level,$entry_session,$mode_of_entry,$contact_address,$home_address,$gsm_number,$email,$year_admitted,$login_password));

            // Check if INSERT was successful
            if ($result) {
                // Check if row was actually inserted (INSERT IGNORE returns success even if duplicate)
                if ($stmt->rowCount() > 0) {
                    $imported++;
                    $output.="<tr class='alert-success'><td>$recordProcessed</td><td colspan='2'>" . strtoupper($regnum) . "</td><td>Student was successfully imported!</td></tr>";
                } else {
                    $alreadyImported++;
                    $output.="<tr class='alert-error'><td>$recordProcessed</td><td colspan='2'>" . strtoupper($regnum) . "</td><td>Already exists (duplicate matric number)</td></tr>";
                }
            } else {
                $errorInfo = $stmt->errorInfo();
                $output.="<tr class='alert-error'><td>$recordProcessed</td><td colspan='2'>" . strtoupper($regnum) . "</td><td>Error: " . htmlspecialchars($errorInfo[2] ?? 'Unknown error', ENT_QUOTES) . "</td></tr>";
            }
        }

        if ($recordProcessed == 0) {
            $output.="<tr class='alert-error'><td colspan='4'>No Record Found!</td></tr></table>";
        } else {
            $output.="</table>";
        }
        $output = "<div class='alert-notice reportsumm'><h3>Summary</h3>"
                . "<b>Total Record Processed:</b> $recordProcessed, <br />"
                . "<b>Successful:</b> $imported, <br />"
                . "<b>Already Exit:</b> $alreadyImported, <br /></div>" . $output;
        echo $output;
        ?>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

    </body>
</html>
