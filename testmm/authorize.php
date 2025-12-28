<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("testfunctions.php");

//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    echo"3";
    exit();
}

if (!isset($_POST['keyz']) || clean($_POST['keyz']) == "") {
    echo"3";
    exit();
}

$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];
$schedule = get_candidate_schedule($candidateid, $testid);
if($schedule==null)
{
    echo"4";
    exit();
}
$testinvigilators = get_test_invigilators_as_array($testid, $schedule);
if(count($testinvigilators)==0)
{
    echo "5";
    exit();
}
$invigilators_passkey = get_test_invigilator_passkeys_as_array($testid, $schedule, $testinvigilators);

$keyz = clean($_POST['keyz']);
$keyz_arr = explode("-", $keyz);
$found = false;
$pk="";

foreach ($invigilators_passkey as $testkey) {
    //echo"Matching: ". count($testkey_arr)."  -- ". count($keyz_arr)." ";
    $testkey_arr = str_split($testkey);
    if (count($testkey_arr) != count($keyz_arr))
    {
        //echo"Matching: ". count($testkey_arr)."  -- ". count($keyz_arr)." ";
        continue;
    }
    $match = true;
    for ($i = 0; $i < count($testkey_arr); $i++) {
        $tka = $testkey_arr[$i];
        $ka = $keyz_arr[$i];
        //echo"Matching: $ka  -- $tka ";
        if (!stripos($ka, $tka) && stripos($ka, $tka)!== 0) {
            //echo"Matched: $ka  -- $tka ";
            $match = false;
            break;
        }
    }
    if ($match == false)
        continue;
    else {
        $found = true;
        $pk=$testkey;
        break;
    }
}
if($found==false)
{
    echo"0";
exit();
}
 else
{
     $sql="insert IGNORE into tblendorsement values ('$candidateid','$testid','$pk')";
     $stmt=$dbh->prepare($sql);
     $stmt->execute();
     $_SESSION['endorsed']='endorsed';
    echo"1";
    exit();
}
?>