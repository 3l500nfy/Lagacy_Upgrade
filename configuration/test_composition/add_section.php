<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
//echo 1;exit;
global $dbh;
authorize();
if (!isset($_POST['tid'])) {
    echo 4;
    exit();
}
if (!isset($_POST['sbjid'])) {
    echo 4;
    exit();
}

$testid = clean($_POST['tid']);
$sbjid = clean($_POST['sbjid']);
if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, null, $sbjid)) {
    echo 2;
    exit();
}

if (!isset($_POST['section-title']) || !isset($_POST['section-mpq']) || !isset($_POST['section-nta']) || (!isset($_POST['section-noe']) && !isset($_POST['section-nom']) && !isset($_POST['section-nod']))) {
    echo 4;
    exit();
}

if (clean($_POST['section-title']) == "" || clean($_POST['section-mpq']) == "" || clean($_POST['section-nta']) == "" || (clean($_POST['section-noe']) == "" && clean($_POST['section-nom']) == "" && clean($_POST['section-nod']) == "")) {
    echo 4;
    exit();
}

$title = clean($_POST['section-title']);
$instr = ((isset($_POST['section-instr'])) ? (clean($_POST['section-instr'])) : (""));
$mpq = clean($_POST['section-mpq']);
$nta = clean($_POST['section-nta']);
$noe = ((isset($_POST['section-noe'])) ? (clean($_POST['section-noe'])) : (0));
$nom = ((isset($_POST['section-nom'])) ? (clean($_POST['section-nom'])) : (0));
$nod = ((isset($_POST['section-nod'])) ? (clean($_POST['section-nod'])) : (0));

if (!preg_match("~^[0-9]{0,}$~", $nta) || !preg_match("~^[0-9]{0,}$~", $noe) || !preg_match("~^[0-9]{0,}$~", $nom) || !preg_match("~^[0-9]{0,}$~", $nod) || !preg_match("~^[0-9]{0,}(\.){0,1}[0-9]{0,}$~", $mpq)) {
    echo 4;
    exit();
}

$query = "select testsubjectid from tbltestsubject where subjectid=? && testid = ?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($sbjid,$testid));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($stmt->rowCount() ==0) {
    echo 5;
    exit();
}

$tsbjid=$row['testsubjectid'];

$query1 = "select * from tbltestsection where testsubjectid=? && title = ?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($tsbjid,$title));
$row = $stmt1->fetch(PDO::FETCH_ASSOC);

if ($stmt1->rowCount() > 0) {
    echo 5;
    exit();
}

$query2 = "insert into tbltestsection (testsubjectid, title, instruction, markperquestion, num_toanswer, numofeasy, numofmoderate, numofdifficult) values (?,?,?,?,?,?,?,?)";
//echo $query2;exit;
$stmt2=$dbh->prepare($query2);
$stmt2->execute(array($tsbjid,$title,$instr,$mpq,$nta,$noe,$nom,$nod));

if ($stmt2->rowCount() != 1) {
    echo 0;
    exit();
}
echo 1;
exit();
?>