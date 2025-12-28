<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator"))) {
    echo "Access Denied!";
    exit();
}
$tid = clean($_POST['tid']);
$char = clean($_POST['ch']);
$mode = clean($_POST['mode']);
$test_config=get_test_config_param_as_array($tid);

if (strtoupper(trim($test_config['testcodeid'])) == '1' || strtoupper(trim($test_config['testcodeid'])) == "12") {
    $query = "select * from tblsubject where subjectcategory =3";
} else
if (strtoupper(trim($test_config['testcodeid'])) == '2') {
    $query = "select * from tblsubject where subjectcategory =2";
} else {
    $query = "select * from tblsubject where subjectcategory =1";
}

$stmt=$dbh->prepare($query);
$stmt->execute();

if ($stmt->rowCount() == 0)
    echo"No subject to select from.";
$c=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sbj_code = $row['subjectcode'];

    $sbj_name = $row['subjectname'];

    $sbj_id = $row['subjectid'];
    if (is_registered_subject($tid, $sbj_id))
        continue;

    if ($mode == 'first') {
        if (strtoupper(substr($sbj_code, 0, 1)) != $char)
            continue;
    }
    else
    if ($mode == 'any') {
        if ($char!="" && !preg_match("~".$char."~i", $sbj_code) && !preg_match("~".$char."~i", $sbj_name))
            continue;
    }
    $c++;
    echo"<div class='sbj' data-sbjid='$sbj_id'>" . strtoupper(trim($sbj_code)) . " - " . intelligentStr(ucwords(strtolower($sbj_name)), 50) . "</div>";
}
if($c==0)
    echo"No subject match the criteria ($char)";
?>
