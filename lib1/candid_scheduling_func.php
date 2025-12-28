<?php

require_once("globals.php");

openConnection();

function is_mapped_to_venue($testid) {
    global $dbh;
    $querybby = "select count(*) from tblscheduling where (testid = ?)";
	$stmt = $dbh->prepare($querybby);
  	$stmt->execute(array($testid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $row[0];
    if ($count > 0)
        return true;
    else
        return false;
}

function get_maximum_batch($schd)
{
    global $dbh;
    $query="select maximumBatch from tblscheduling where (schedulingid= ?)";
   $stmt = $dbh->prepare($query);
  	$stmt->execute(array($schd));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['maximumBatch'];
}

function get_venue_id($schd)
{
    global $dbh;
    $query="select venueid from tblscheduling where (schedulingid= ?)";
    $stmt = $dbh->prepare($query);
  	$stmt->execute(array($schd));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['venueid'];
}


function get_venue_name($venueid)
{
    global $dbh;
    $query="select name, location from tblvenue where (venueid= ?)";
     $stmt = $dbh->prepare($query);
  	$stmt->execute(array($venueid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($row['name']." (".$row['location'].")");
}

function get_centre_id($venueid)
{
    global $dbh;
    $query="select centreid from tblvenue where (venueid= ?)";
    $stmt = $dbh->prepare($query);
  	$stmt->execute(array($venueid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['venueid'];
}

function get_subject_code2($sbjid)
{
    global $dbh;
    $query="select subjectcode from tblsubject where (subjectid = ?)";
     $stmt = $dbh->prepare($query);
  	$stmt->execute(array($sbjid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['subjectcode'];
}
function get_subject_name($sbjid)
{
    global $dbh;
    $query="select subjectname from tblsubject where (subjectid = ?)";
     $stmt = $dbh->prepare($query);
  	$stmt->execute(array($sbjid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['subjectname'];
}

function get_centre_name($cid)
{
    global $dbh;
    $query="select name from tblcentres where (centreid=?)";
    $stmt = $dbh->prepare($query);
  	$stmt->execute(array($cid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['name'];
}

function get_maximum_per_batch($schd)
{
    global $dbh;
    $query="select noPerschedule from tblscheduling where (schedulingid=?)";
    $stmt = $dbh->prepare($query);
  	$stmt->execute(array($schd));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['noPerschedule'];
}

function get_scheduled_count($schd)
{
    global $dbh;
    $query="select distinct candidateid from tblcandidatestudent where (scheduleid=?) group by candidateid";
    $stmt = $dbh->prepare($query);
  	$stmt->execute(array($schd));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total= $stmt->rowCount();
    return $total;
}


function candid_exist2($matric, $stdtype="Regular")
{
    if($stdtype=="Regular")
    $query="select * from tblstudents where (matricnumber =?)";
    else
    if($stdtype=="PUTME")
    $query="select * from tbljamb where (RegNo = ?)";
    else
    if($stdtype=="SBRS")
    $query="select * from tblsbrsstudents where (sbrsno=? || oldsbrsno=?)";
    $result = $dbh->prepare($query);
  	$result->execute(array(trim($matric)));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
	if($result->rowCount()>0)
    {
        return true;
    }
    return false;
}

function get_batch($schd)
{
    
    $mpb=get_maximum_per_batch($schd);
    
    $mb=get_maximum_batch($schd);
    $tot=get_scheduled_count($schd);
    $bsofar=$tot/$mpb;
    $rm=$tot%$mpb;
    if($bsofar==$mb)
        return -1;
        return ($bsofar+1);
}

function get_curr_batch($schd, $cid)
{
    global $dbh;
    $mpb=get_maximum_per_batch($schd);
    
    $query="select * from tblcandidatestudent where (scheduleid=?) group by candidateid";
    $result = $dbh->prepare($query);
  	$result->execute(array($schd));
     $i=0;
    while($row = $result->fetch(PDO::FETCH_ASSOC))
    {
        $i++;
        if($row['candidateid']==$cid)
            break;
    }
    $bsofar=$i/$mpb;
    $rm=$i%$mpb;
    if($rm==0)
    {
        if($bsofar==0)
            return 1;
        else
            return ((int)$bsofar);
    }
        
        return ((int)($bsofar+1));
}


function get_venue_capacity($venueid)
{
    global $dbh;
    $query="select capacity from tblvenue where (venueid=?)";
    $result = $dbh->prepare($query);
  	$result->execute(array($venueid));
   
	if($result->rowCount()>0)
    {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['capacity'];
    }
    return 0;
}




function get_testschedule_as_array($tid) {
    global $dbh;
    $venue = array();
    $query = "select * from tblscheduling where (testid = ?)";
    $result = $dbh->prepare($query);
  	$result->execute(array($tid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $venue[$i] = array('scheduleid' => $row['schedulingid'], 'venueid' => $row['venueid'], 'maxbatch' => $row['maximumBatch'], 'maxperschedule' => $row['noPerschedule']);
        $i++;
    }
    return $venue;
}


function get_candidate_password($matric, $type='regular')
{
    global $dbh;
    if($type=='regular')
    {
        $query="select loginpassword  from tblstudents where (matricnumber =?)";
        $result = $dbh->prepare($query);
  		$result->execute(array($matric));
        if($result->rowCount()>0)
        {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['loginpassword'];
        }
        else return "";
    }
    else
            if($type=='sbrs')
    {
        $query="select loginpassword  from tblsbrsstudent where (sbrsno =? || oldsbrsno=?)";
        $result = $dbh->prepare($query);
  		$result->execute(array($matric));
     if($result->rowCount()>0)
        {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['loginpassword'];
        }
        else return "";
    }
	

else
        if($type=='putme')
    {
        $query="select StateOfOrigin  from tbljamb where (RegNo =?)";
        $result = $dbh->prepare($query);
  		$result->execute(array($matric));
        if($result->rowCount()>0)
        {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['StateOfOrigin'];
        }
        else return "";
    }

}

	
function get_candidate_id2($matric, $candtype=1) {
    global $dbh;
    if($candtype==1)
    $query = "select candidateid from tblscheduledcandidate where candidatetype='1' and (UPPER(RegNo) = ?)";
    elseif($candtype==2)
    $query = "select candidateid from tblscheduledcandidate where candidatetype='2' and (UPPER(RegNo) = ?)";
        elseif($candtype==3)
    $query = "select candidateid from tblscheduledcandidate where candidatetype='3' and (UPPER(RegNo) = ?)";

    $result = $dbh->prepare($query);
  	$result->execute(array($matric));
    if ($result->rowCount() == 1) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $cid = $row['candidateid'];
        return $cid;
    }
    else{
         if($candtype==1)
        $query="insert into tblscheduledcandidate (candidatetype, RegNo) values (1, ?)";
         elseif($candtype==2)
        $query="insert into tblscheduledcandidate (candidatetype, RegNo) values (2, ?)";
    }
    $result2 = $dbh->prepare($query);
	$result2->execute(array(UPPER('$matric')));
    if($result2)
        return get_candidate_id ($matric, $candtype);
    else
        return null;
}


    
	
function get_candidate_id($matric, $candtype=1) {
    global $dbh;
    if($candtype==1)
    $query = "select candidateid from tblscheduledcandidate where (candidatetype='1' and UPPER(RegNo) = ?)";
    elseif($candtype==2)
    $query = "select candidateid from tblscheduledcandidate where (candidatetype='2' and UPPER(RegNo) = ?)";
        elseif($candtype==3)
    $query = "select studentid from tblstudents where (candidatetype='3' and UPPER(matricnumber) = ?)";
     $result = $dbh->prepare($query);
	$result->execute(array(trim(strtoupper($matric))));
    if ($result->rowCount() == 1) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $cid = $row['candidateid'];
        return $cid;
    }
    else{
         if($candtype==1)
        $query="insert into tblscheduledcandidate (candidatetype, RegNo) values (1, ?)";
         else
              if($candtype==2)
        $query="insert into tblscheduledcandidate (candidatetype, RegNo) values (2, ?))";
              
    }
    $result2 = $dbh->prepare($query);
	$exec=$result2->execute(array($matric));
    if($exec)
        return get_candidate_id ($matric, $candtype);
    else
        return null;
}




function is_scheduled2($schd, $cid, $subs)
{
    global $dbh;
    //echo count($schd);
    $schds=implode(",", $schd); $schds= trim($schds, ",");
    //echo "en ".$schds."<br />";
    $subjs=implode(",", $subs); $subjs= trim($subjs, ",");
    $query="select * from tblcandidatestudent where (scheduleid in ? && subjectid in ? && candidateid=?) ";
    //echo $sql;
    $result = $dbh->prepare($query);
	$result->execute(array($schds, $subjs, $cid));
    if($result->rowCount()>0)
        return true;
    else return false;
}

function get_schedule_ids_as_array($tid) {
    global $dbh;
    $s=array();
    $query = "select schedulingid from tblscheduling where (testid = ?)";// echo $sql;
    $result = $dbh->prepare($query);
	$result->execute(array($tid));
    $i = 0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        
        $s[$i] = $row['schedulingid'];
       
        $i++;
    }
    return $s;
}
	
function get_schedule_id($tid, $venueid) {
    global $dbh;
    $s=array();
    $query = "select schedulingid from tblscheduling where (testid = ? && venueid=?)";// echo $sql;
    $result = $dbh->prepare($query);
	$result->execute(array($tid, $venueid));
$row = $result->fetch(PDO::FETCH_ASSOC);
        $s= $row['schedulingid'];
    return $s;
}

function get_subject_combination_as_array($tid)
{
    global $dbh;
    $s=array();
    $query = "select subjectid from tbltestsubject where (testid = ?)";
    $result = $dbh->prepare($query);
	$result->execute(array($tid));
    $i = 0;
    while ( $row = $result->fetch(PDO::FETCH_ASSOC)) {
        $s[] = $row['subjectid'];
        $i++;
    }
    return $s;
}


function get_subject_selection_as_array($schdid, $cid)
{
    global $dbh;
    $s=array();
    $query = "select subjectid from tblcandidatestudent where (scheduleid = ? && candidateid=?)";
    $result = $dbh->prepare($query);
	$result->execute(array($schdid, $cid));
    $i = 0;
    while ( $row = $result->fetch(PDO::FETCH_ASSOC)) {
        $s[$i] = $row['subjectid'];
        $i++;
    }
    return $s;
}

function get_subject_selection_as_array2($tid, $cid)
{
    global $dbh;
    $s=array();
    $query = "select subjectid from tblcandidatestudent where (scheduleid in (select scheduleid from tblscheduling where testid=?) && candidateid=?)";
    $result = $dbh->prepare($query);
	$result->execute(array($tid, $cid));
    $i = 0;
    while ( $row = $result->fetch(PDO::FETCH_ASSOC)) {
        $s[$i] = $row['subjectid'];
        $i++;
    }
    return $s;
}

function get_programme_name($progid)
{
    global $dbh;
    $query="select name from tblprogramme where (programmeid=?)";
    $result = $dbh->prepare($query);
	$result->execute(array($progid));
   if($result->rowCount()>0)
    {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['name'];
    }
    else return "";
}

function get_venue_location($venueid) {
    global $dbh;
    $query = "select location from tblvenue where (venueid=?)";
   $result = $dbh->prepare($query);
	$result->execute(array($venueid));
    $row = $result->fetch(PDO::FETCH_ASSOC); 
    return $row['location'];
}

function get_exam_time($tid){
    global $dbh;
    $query="select dailystarttime from tblscheduling where (testid=?)";
   $result = $dbh->prepare($query);
	$result->execute(array($tid));
    $row = $result->fetch(PDO::FETCH_ASSOC); 
    
    return $row['dailystarttime'];
}

function get_exam_date($schd,$tid){
    global $dbh;
    $query="select date from tblscheduling where (schedulingid=? and testid=?)";
   $result = $dbh->prepare($query);
	$result->execute(array($schd, $tid));
    $row = $result->fetch(PDO::FETCH_ASSOC); ;
    
    return $row['date'];
}

function embed_user_pic($regno) {
   
    $finalURL = get_current_photo($regno);

    echo $finalURL;
}



function get_current_photo($regno) {
    $finalURL = '';
    $r_v = '';
    
    $s = '../picts/';

    $r_v = $s . $regno . '.jpeg';
    $r_v1 = $s . $regno . '.jpg';
    $r_v2 = $s . $regno . '.png';
    $r_v3 = $s . $regno . '.gif';
    
    if (file_exists($r_v)) {
        $finalURL = "<img src='" . $r_v . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    } 
    else if(file_exists($r_v1)) {
        $finalURL = "<img src='" . $r_v1 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    }
    else if(file_exists($r_v2)) {
        $finalURL = "<img src='" . $r_v2 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    }
    else if(file_exists($r_v3)) {
        $finalURL = "<img src='" . $r_v3 . "' width='150px' height='170px' style='border:solid 0.5px #000000'>";
    }
    else{
        $finalURL = "<img src='../assets/img/photo.png' alt='image not uploaded' title='image not uploaded' width='149px' height='168px'  style='border:solid 0.5px #000000'>";
    }

    return $finalURL;
}

?>