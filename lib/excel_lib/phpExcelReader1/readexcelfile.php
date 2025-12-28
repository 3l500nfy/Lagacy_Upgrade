<?php

include ("connect.tpl");
$querydate="select current_date as dates";
$resultdate= mysql_query($querydate);
$date= mysql_result($resultdate, 0, 'dates');

// importing the data from the form


$filenames=$_REQUEST['filenames'];
$session=$_POST['session'];

$coursecode=$_POST['coursecode'];

//$imgfile_name2 = $_FILES['filenames'];
$imgfile_name = $_FILES['filenames']['name']; // get client side file name

 $imgfile = $_FILES['filenames']['tmp_name']; // temporary file at server side 

//print"client file name:$imgfile_name ";
//print"server file name: $sTempFileName";
//print"$filenames gg $imgfile_name $session $coursecode <p>"; 


if ($imgfile_name) // file uploaded 
{ $aFileNameParts = explode(".", $imgfile_name); 
$sFileExtension = end($aFileNameParts); // part behind last dot
 if ($sFileExtension != "xls")
{

print"<script language='javascript'>alert(\"This  format of file will not be accepted.Choose a 2003 excel format\")</script>"; 
//echo getcwd() . "1\n";
include"processresult.php";
die();
}
}
$imgfile_size = $_FILES['filenames']['size']; // size of uploaded file 
if ($imgfile_size == 0) {

print"<script language='javascript'> alert('Sorry. The upload of $imgfile_name has failed. Retry')</script>"; 
include"processresult.php";
die();

}

//begin to process file and transfer to server

$final_filename ="$coursecode.xls";
$newfile = $final_filename;


/*== do extra security check to prevent malicious abuse==*/
if (is_uploaded_file($imgfile))
{
/*== move file to proper directory ==*/
if (!copy($imgfile,"$newfile")) 
{
/*== if an error occurs the file could not
be written, read or possibly does not exist ==*/
print "<script language='javascript'> alert('Error Uploading File. Retry')</script>";

include"processresult.php";
}
}
$imgsize = GetImageSize($imgfile);
//print" after sizing: $imgsize[0]";


/*== delete the temporary uploaded file ==*/
//unlink($imgfile);






//end of processing file









//print"$newfile ok $session $coursecode <p>";


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
$matric="";
$ca="";
$exam="";
$total="";
$grade="";

$matric=$reader->sheets[0]["cells"][$i][1];
$matric=trim($matric);
$ca=$reader->sheets[0]["cells"][$i][2];
$ca=trim($ca);
$exam=$reader->sheets[0]["cells"][$i][3];
$exam=trim($exam);
$total=$reader->sheets[0]["cells"][$i][4];
$total=trim($total);
$grade=$reader->sheets[0]["cells"][$i][5];
$grade=trim($grade);
// gradepoint
$scores=$total;
if ($scores>0)
{
  switch($scores):
    case($scores>=70):
	$grade='A';
	$point=4;
	break;
   case($scores>=60):
	$grade='B';
	$point=3;
	break;
case($scores>=50):
	$grade='C';
	$point=2;
	break;
case($scores>=40):
	$grade='D';
	$point=1;
	break;
case($scores<40):
	$grade='F';
	$point=0;
	break;

default:
	$grade='';
	$point=0;
	break;
endswitch;
}
elseif($scores==0)
{
$grade='F';
$point=0;
}
elseif($scores==-1||$scores=="")
{
$grade='CF';
$point=0;
}


$query1="UPDATE enrolmenttb SET ca='$ca',exam='$exam',score='$total',grade='$grade',point='$point',enrolmenttb.date='$date'
 WHERE (coursecode='$coursecode' AND enrolmenttb.session='$session'AND matric='$matric')";
$result=mysql_query($query1);
//$nrows=mysql_numrows($result);

//	}
}
//delete the file from the result file server
unlink($newfile);
//echo getcwd() . "\n";

include"processresult.php";

?>