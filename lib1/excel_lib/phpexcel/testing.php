<?php
// in order to use it you need to specify if the file is xls or xlsx. determine if excel 2007 or 2003 format. 
//here file name is the file you are importing
		$aFileNameParts = explode(".", $filename); 
			$sFileExtension = end($aFileNameParts); // part behind last dot
			if ($sFileExtension == "xls")
			{
			//2003 format
			require_once('phpexcel/classes/PHPExcel/Reader/Excel5.php');
			$objReader = new PHPExcel_Reader_Excel5();
			}
			elseif($sFileExtension == "xlsx")
			{
			//2007format
			require_once('phpexcel/classes/PHPExcel/Reader/Excel2007.php');
			$objReader = new PHPExcel_Reader_Excel2007();
			
			}
//
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load($filename);
$objPHPExcel->setActiveSheetIndex(1);//1 specify which excel sheet to read from. i normally allow the user to enter it from user input
//$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

$sheet = $objPHPExcel->getActiveSheet();
$max2=$objPHPExcel->getActiveSheet()->getHighestRow();
  
 //$rowIndex is where to start reading. i normally recieved it from user input
 // A,B,C,... areexcel column. i normally allow the user to specify them and just use the specified one than hardcoding in order to make it flexible
 FOR($rowIndex=1;$rowIndex<=$max2;$rowIndex++)
 {
   echo $sheet->getCell('A' . $rowIndex)->getCalculatedValue();
	echo $sheet->getCell('B' . $rowIndex)->getCalculatedValue();
	echo $sheet->getCell('C' . $rowIndex)->getCalculatedValue();
	echo $sheet->getCell('D' . $rowIndex)->getCalculatedValue();
   	echo"<br>";
 }
?>
