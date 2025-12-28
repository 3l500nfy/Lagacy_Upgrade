<?php

if (!isset($_SESSION))
    session_start();
include"../lib/globals.php";
openConnection();
$candidatetype = ((isset($_POST['candidatetype'])) ? ($_POST['candidatetype']) : (""));

if ($candidatetype == 1) { //PUTME
    $query = "SELECT * FROM tbltestconfig WHERE testcodeid='1' && testtypeid='1' && semester =0 ORDER BY session DESC limit 1";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $numrows = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    //echo $sql;
    
   if ($numrows == 0)
        header("Location: index.php?tconfig=0");
    else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $tid = $row['testid'];
      

        $query = "SELECT * FROM tblexamsdate WHERE testid = '$tid' && date >=now() ORDER BY date ASC limit 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $numrows = $stmt->rowCount();
        
        if ($numrows == 0)
            header("Location: index.php?tconfig=0");
        else {
            $_SESSION['tid'] = $tid;
            
            header("Location: candidate_login.php");
             
        }
    }
    exit();
} elseif ($candidatetype == 2) { //SBRS
     header("Location: candidate_login_sbrs.php");
        //}
    //}
    exit();
} elseif ($candidatetype == 3) { //REGULAR
    $query = "SELECT * FROM tbltestconfig WHERE (testcodeid<>'1' && testcodeid<>'2') && semester <>'0' ORDER BY session DESC limit 1";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $numrows = $stmt->rowCount();
    
    if ($numrows == 0)
        header("Location: index.php?tconfig=0");
    else {
        $tidrow = $stmt->fetch(PDO::FETCH_ASSOC);
        $tid = $tidrow['testid'];

        $query = "SELECT * FROM tblexamsdate WHERE testid = '$tid' && date >=curdate() ORDER BY date ASC limit 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $numrows = $stmt->rowCount();
      
        if ($numrows == 0)
            header("Location: index.php?tconfig=0");
        else {
            
            $_SESSION['tid'] = $tid;
            header("Location: candidate_login_student.php");
        }
    }
    exit();
} elseif ($candidatetype == 4) {
        $query = "SELECT * FROM tbltestconfig WHERE testcodeid='12' && testtypeid='1' && semester =0 ORDER BY session DESC limit 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $numrows = $stmt->rowCount();
    
    //echo $sql;
    
   if ($numrows == 0)
        header("Location: index.php?tconfig=0");
    else {
        $tidrow = $stmt->fetch(PDO::FETCH_ASSOC);
        $tid = $tidrow['testid'];
      

        $query = "SELECT * FROM tblexamsdate WHERE testid = '$tid' && date >=now() ORDER BY date ASC limit 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $numrows = $stmt->rowCount();
        
        if ($numrows == 0)
            header("Location: index.php?tconfig=0");
        else {
            $_SESSION['tid'] = $tid;
            
             header("Location: sbrs_entrance_exam_scheduler/index.php");
             
        }
    }
    exit();
}
?>