<?php

if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");

require_once('../lib/security.php');
require_once("testfunctions.php");

openConnection();
global $dbh;
$errmsg_arr = array();  //Array to store validation errors
$errflag = false;       //Validation error flag
$failedmessage="";
$username = clean($_POST['username']);
$password = clean($_POST['password']);
$testid = clean($_POST['testid']);


//Input Validations
if ($username == '') {
    $errmsg_arr[] = 'Username is missing';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Password is missing';
    $errflag = true;
}

//If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: login.php");
    exit();
}

//check if the test is already opened or not
if(testopened($testid)==false){
 $_SESSION['LOGIN_FAILED'] = "The test is closed. Notify the invilgilator";
 session_write_close();
 header("location: login.php");
  exit();

}

//get the category of student in oreder to locate his login detail

$qry = "SELECT * FROM tblscheduledcandidate WHERE RegNo='$username' order by candidatetype desc";
$stmt = $dbh->prepare($qry);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($stmt->rowCount() >= 1) {
   $type=  $row['candidatetype'];
//$type=1;
}else{
    
   $_SESSION['LOGIN_FAILED'] = "Unknown student! verify if your Reg. No.: $username is correctly typed ";
    session_write_close();
    redirect(siteUrl("test/login.php"));
    exit();
}
     //echo $type; exit;
if($type==3){
    //regular student
    $query = "SELECT loginpassword FROM tblstudents WHERE matricnumber='$username' AND loginpassword='$password' limit 1";

    
}elseif($type==2){
    //sbrs regular students
 $query = "SELECT loginpassword FROM tblsbrsstudents WHERE sbrsno='$username' AND loginpassword='$password' limit 1";
    
}elseif($type==1){
 //jamb student
 $query = "SELECT StateOfOrigin FROM tbljamb WHERE RegNo='$username' AND StateOfOrigin='$password' limit 1";
//echo $qry; exit;
    
}else{
    // sbrs  entrance candidate. modify query when table available
     $query = "SELECT stateOforigin from tblsbrsshortlist where ApplicationID='$username' AND stateOforigin='$password' limit 1";
    
}

$stmt1=$dbh->prepare($query);
$stmt1->execute();

if ($stmt1->rowCount() == 1) {

    $member = $stmt1->fetch(PDO::FETCH_ASSOC);

   //check if the student registered for the exams he is trying to write

		$qry1 = "SELECT distinct tblscheduledcandidate.candidateid, venueid FROM  tblscheduledcandidate INNER JOIN tblcandidatestudent ON 
		tblscheduledcandidate.candidateid= tblcandidatestudent.candidateid
		 INNER JOIN tblscheduling ON tblcandidatestudent.scheduleid= tblscheduling.schedulingid
		 WHERE RegNo='$username' AND testid='$testid'";
		
		$stmt2=$dbh->prepare($qry1);
		$stmt2->execute();
		if ($stmt2->rowCount() <=0) {
		// if student did not register to write the exams he selected.
        $_SESSION['LOGIN_FAILED'] = "You did not Register for the Exams you Selected.";
		//echo "here"; exit;
        session_write_close();
		
        header("location: login.php");
        exit();
    }
	else{
		//authenticate that the student is at the right venue at a right date
		$data = $stmt2->fetch(PDO::FETCH_ASSOC);
		if(authenticatecandidate($data['candidateid'],$testid)==true){
		
			if (checkcompletion($testid,$data['candidateid'])==false){ //put condition to check if the student has completed the test
				 session_regenerate_id();
				$userid = $data['candidateid'];
				$_SESSION['MEMBER_FULLNAME'] = "";//$member['displayname'];
				$_SESSION['MEMBER_USERID'] = $data['candidateid'];
	//			$_SESSION['MEMBER_USERNAME'] = $member['username'];
				$_SESSION['candidateid'] = $data['candidateid'];
				$_SESSION['testid'] = $testid;
				session_write_close();
				redirect(siteUrl("test/index.php"));
				exit();
				}
				else//exams already taken
				{
				$_SESSION['LOGIN_FAILED'] = "You have already taken the selected exams.";
				//echo "here"; exit;
				session_write_close();
				
				header("location: login.php");
				exit();
				
				}
		}
		else
		{
			// if student did not register to write the exams he selected.
			$_SESSION['LOGIN_FAILED'] = $failedmessage;
			session_write_close();
			header("location: login.php");
			exit();

		}
	}
	

   
} else {
    $_SESSION['LOGIN_FAILED'] = "Invalid Username/Password";
    session_write_close();
    redirect(siteUrl("test/login.php"));
    exit();
}
?>