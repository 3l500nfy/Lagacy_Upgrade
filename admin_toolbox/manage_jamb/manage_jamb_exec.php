
<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();

ini_set("memory_limit", "256M") + ini_set('max_execution_time', 300);
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

           // $id = clean($sheet->getCell('A' . $i)->getCalculatedValue());
            $DeptSn = clean($sheet->getCell('A' . $i)->getCalculatedValue());
            $RegNumb = clean($sheet->getCell('B' . $i)->getCalculatedValue());
           // $RegNumb = mysql_real_escape_string($RegNumb);
            $CandName = clean($sheet->getCell('C' . $i)->getCalculatedValue());
            //$CandName = mysql_real_escape_string($CandName);
            $StateOfOrigin = clean($sheet->getCell('D' . $i)->getCalculatedValue());
           // $StateOfOrigin = mysql_real_escape_string($StateOfOrigin);
            $LGA = clean($sheet->getCell('E' . $i)->getCalculatedValue());
            //$LGA = mysql_real_escape_string($LGA);
            $Sex = clean($sheet->getCell('F' . $i)->getCalculatedValue());
            $Age = clean($sheet->getCell('G' . $i)->getCalculatedValue());
            $Engscore = clean($sheet->getCell('H' . $i)->getCalculatedValue());
            $Subj2 = clean($sheet->getCell('I' . $i)->getCalculatedValue());
            $Subj2Score = clean($sheet->getCell('J' . $i)->getCalculatedValue());
            $Subj3 = clean($sheet->getCell('K' . $i)->getCalculatedValue());
            $Subj3Score = clean($sheet->getCell('L' . $i)->getCalculatedValue());
            $Subj4 = clean($sheet->getCell('M' . $i)->getCalculatedValue());
            $Subj4Score = clean($sheet->getCell('N' . $i)->getCalculatedValue());
            $TotalScore = clean($sheet->getCell('O' . $i)->getCalculatedValue());
            $Faculty = clean($sheet->getCell('P' . $i)->getCalculatedValue());
            $Course = clean($sheet->getCell('Q' . $i)->getCalculatedValue());

            if ($RegNumb == "") {
                $recordProcessed--;
                continue;
            }
            $testtype = "Post-UTME";

            if (candid_exist($RegNumb, $testtype)) {
                $alreadyImported++;
                $output.="<tr class='alert-error'><td>" . ($recordProcessed) . "</td> <td colspan='2'>" . strtoupper($RegNumb) . "</td><td>Already exist in our Jamb Table!</td></tr>";
                continue;
            }

            //Inserting into tbljamb

            $query = "INSERT INTO tbljamb(DeptSn,RegNo,CandName,StateOfOrigin,LGA,Sex,Age,Engscore,Subj2,Subj2Score, 
Subj3,Subj3Score,Subj4,Subj4Score,TotalScore,Faculty, Course)VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $stmt=$dbh->prepare($query);
           $stmt->execute(array($DeptSn,$RegNumb,$CandName,$StateOfOrigin,$LGA,$Sex,$Age,$Engscore,$Subj2,$Subj2Score,$Subj3,$Subj3Score,$Subj4,$Subj4Score,$TotalScore,$Faculty,$Course));

            $imported++;
            $output.="<tr class='alert-success'><td>$recordProcessed</td><td colspan='2'>" . strtoupper($RegNumb) . "</td><td> Candidate was successfully imported!</td></tr>";
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