<?php

//$imgfile_name2 = $_FILES['filenames'];
$imgfile_name = $_FILES['filenames']['name']; // get client side file name
 $imgfile = $_FILES['filenames']['tmp_name']; // temporary file at server side 

 //print"client file name:$imgfile_name ";
//print"server file name: $sTempFileName";

//print"gg $imgfile_name $entrysession $entrylevel <p>"; 
//die();

if ($imgfile_name) // file uploaded 
{ $aFileNameParts = explode(".", $imgfile_name); 
$sFileExtension = end($aFileNameParts); // part behind last dot
 if ($sFileExtension != "xls")
{

print"<script language=javascript>alert(\"This  format of file will not be accepted.Choose a 2003 excel format\")</script>"; 
include"processresult.php";
die();
}
}
$imgfile_size = $_FILES['filenames']['size']; // size of uploaded file 
if ($imgfile_size == 0) {

print"<script language=javascript>alert('Sorry. The upload of $imgfile_name has failed. Retry')</script>"; 
//include"processresult.php";
die();

}

//begin to process file and transfer to server

$final_filename ="$entrylevel.xls";
$newfile = $final_filename;


/*== do extra security check to prevent malicious abuse==*/
if (is_uploaded_file($imgfile))
{
/*== move file to proper directory ==*/
if (!copy($imgfile,"$newfile")) 
{
/*== if an error occurs the file could not
be written, read or possibly does not exist ==*/
print "Error Uploading File.";
exit();
}
}
$imgsize = GetImageSize($imgfile);
//print" after sizing: $imgsize[0]";


/*== delete the temporary uploaded file ==*/
//unlink($imgfile);






//end of processing file



require_once 'Excel/reader.php';

$reader = new Spreadsheet_Excel_Reader();
$reader->setOutputEncoding("UTF-8");
//$cols=7;

//$reader->read("test.xls");

$reader->read($newfile);


for ($i = 4; $i <= $reader->sheets[0]["numRows"]; $i++)
{
	//for ($j = 1; $j <= $reader->sheets[0]["numCols"]; $j++)
	//{
		//print "\"".$reader->sheets[0]["cells"][$i][$j]."\",";
$h=$reader->sheets[0]["cells"][$i][$j];

// imp
  
    $matric="";
    $surname="";
    $othername="";
  
	$daybirth="";
    $monthbirth="";
    $yearbirth="";
  
	$gender="";
    $stateorigin="";
    $lga="";
    $nationality="";
 
    $homeaddress="";
    
    $phone="";
    $email="";
   $parentname="";
    $parentaddress="";


//end imp


    $matric=$reader->sheets[0]["cells"][$i][1];
$matric=trim($matric);

    $surname=$reader->sheets[0]["cells"][$i][2];
    $othername=$reader->sheets[0]["cells"][$i][3];
  
	$daybirth=$reader->sheets[0]["cells"][$i][4];
    $monthbirth="";
    $yearbirth="";
  
	$gender=$reader->sheets[0]["cells"][$i][5];
    $stateorigin=$reader->sheets[0]["cells"][$i][6];
    $lga=$reader->sheets[0]["cells"][$i][7];
    $nationality=$reader->sheets[0]["cells"][$i][8];
 
    $homeaddress=$reader->sheets[0]["cells"][$i][9];
    
    $phone=$reader->sheets[0]["cells"][$i][10];
    $email=$reader->sheets[0]["cells"][$i][11];
   $parentname=$reader->sheets[0]["cells"][$i][12];
    $parentaddress=$reader->sheets[0]["cells"][$i][13];

// including the query
$coursestatus="CSO";

$query="INSERT INTO studentbiodatatb VALUES('$matric','$department','$courseoption','$level','$entrymode','$entrylevel','$entrysession',
'$surname','$othername','$daybirth','$monthbirth','$yearbirth','$gender','$stateorigin','$lga','$maritalstatus','$nationality',
'$coursestatus','$homeaddress','$phone','$email','$parentname','$parentaddress')";
$result=mysql_query($query);

//	}
//	echo "<br>";
}
//delete the file from the result file server
unlink($newfile);

?>