<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!isset($_POST['tid']) || !isset($_POST['sbjid'])) {
    echo 5;
    exit();
}

$tid = clean($_POST['tid']);
if (!is_test_administrator_of($tid)) {
    echo 2;
    exit();
}

$test_config = get_test_config_param_as_array($tid);
$sbjid = clean($_POST['sbjid']);
$schedules = get_test_schedule_as_array($tid);
$schedules[]=0;
$schedules_str = trim(implode(",", $schedules), " ,");

if (is_registered_subject($tid, $sbjid)) {
    echo 6;
    exit();
}

if ($test_config['testcodeid'] == "1") {
    try {
        $query = "INSERT INTO tbltestsubject (testid, subjectid, title, instruction, totalmark) VALUES (?, ?, '', '', '100')";
        $stmt = $dbh->prepare($query);
        $result = $stmt->execute(array($tid, $sbjid));
        
        if (!$result) {
            error_log("Failed to insert test subject: " . print_r($stmt->errorInfo(), true));
            echo 0;
            exit();
        }
        
        echo 1;
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo 0;
        exit();
    }
} else {

    $candidates = get_candidate_scheduled_as_array($tid);
    $candidates[] = 0; // in case there are no scheduled candidates
    //PDO Connection
    /*try {
        $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, array(
                    PDO::ATTR_PERSISTENT => true
                ));
    } catch (PDOException $e) {

        echo 0;
        exit();
    }*/

    try {
        $dbh->beginTransaction();

        // Insert test subject
        $query = "INSERT INTO tbltestsubject (testid, subjectid, title, instruction, totalmark) VALUES (?, ?, '', '', '100')";
        $stmt = $dbh->prepare($query);
        $result = $stmt->execute(array($tid, $sbjid));
        
        if (!$result) {
            throw new PDOException("Failed to insert test subject");
        }

        // Process candidates
        foreach ($candidates as $candidate) {
            $query2 = "SELECT scheduleid FROM tblcandidatestudent WHERE candidateid = ? AND scheduleid IN ($schedules_str)";
            $stmt2 = $dbh->prepare($query2);
            $stmt2->execute(array($candidate));
            
            if ($stmt2->rowCount() == 0) {
                continue;
            }
            
            $row_schd = $stmt2->fetch(PDO::FETCH_ASSOC);
            $schd = $row_schd['scheduleid'];

            $query3 = "INSERT INTO tblcandidatestudent (scheduleid, candidateid, subjectid) VALUES (?, ?, ?)";
            $stmt3 = $dbh->prepare($query3);
            $result3 = $stmt3->execute(array($schd, $candidate, $sbjid));
            
            if (!$result3) {
                throw new PDOException("Failed to insert candidate student");
            }
        }

        $dbh->commit();
        echo 1;
        exit();
    } catch (PDOException $e) {
        $dbh->rollBack();
        error_log("Database error: " . $e->getMessage());
        echo 0;
        exit();
    }
}
?>