<?php
require_once '../lib/globals.php';

openConnection(true);

$testtype=$_POST['testtype'];
$fullname=ucwords($_POST['username']);
$state=$_POST['state'];
$subject=trim($_POST['subject']);
$subject=trim(trim($subject,'-'));

$sub=explode('-',$subject);

$success=false;
if(isset($testtype)&&isset($fullname)&& isset($state)){
    if($testtype==1){
        //create the jamb number
        $num=0;
         $result="select max(id) as id from tbljamb";
        $stmt->$dbh->prepare($result);
        $stmt->execute(array($programmeid));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);

         if($stmt->rowCount() >0){
             $num=  $row[0]['id'];
         }
         $num=$num+1;
         $jambno="CAND".$num;
         //create student
        
        $query="INSERT into tbljamb (RegNo, CandName,StateOfOrigin)   values(?,?,?)";
        $stmt1->$dbh->prepare($query);
        $stmt1->execute(array($jambno,$fullname,$state));

        if($stmt->rowCount()>0){
            //schedule the student for today
            $query="INSERT into tblscheduledcandidate (candidatetype,RegNo)   values('1',?)";
            $stmt->$dbh->prepare($query);
            $stmt->execute(array($jambno));
            
            $result="select  candidateid from tblscheduledcandidate where(RegNo=?)";
            $stmt->$dbh->prepare($result);
            $stmt->execute(array($jambno));
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
         if($stmt->rowCount() >0){
             $candidateid=  $row[0]['candidateid'];
             //get the testcode and the schheduling of today. and enrols the student to that
             //type11=tutorial and code1=putme
              $result="select  schedulingid from tblscheduling inner join tbltestconfig
                  on tblscheduling.testid= tbltestconfig.testid    where(tblscheduling.date=curdate() 
                  and tbltestconfig.testtypeid=1 and tbltestconfig.testcodeid=1 )";
             $stmt->$dbh->prepare($result);
             $stmt->execute();
             $row=$stmt->fetch(PDO::FETCH_ASSOC);
                  if($stmt->rowCount()>0){
                 $schedulingid=  $row[0]['schedulingid'];
                 
                        for ($i=0;$i<count($sub);$i++){
                       $code=$sub[$i];

                        $query="insert into tblcandidatestudent (scheduleid, candidateid, subjectid) values(?,?,?)";
                            $stmt->$dbh->prepare($query);
                            $stmt->execute(array($schedulingid,$candidateid,$code));
                    }
                 $success=true;
                     echo"USERNAME:$jambno <br>PASSWORD:$state";
                     exit();
                  }
         }
        }
    }elseif($testtype==2){
        //another test type like ug
    }
}
if( $success==false){
    echo "false";
}

?>
