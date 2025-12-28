<?php
require_once("lib/globals.php");
require_once("lib/security.php");
require_once("lib/cbt_func.php");
openConnection();

$testid = $_POST['testid'];
if (!(is_numeric($testid))) {
    echo "4";
    exit();
}

if (!isset($_POST['keyz']) || clean($_POST['keyz']) == "") {
    echo"3";
    exit();
}

$testkey = get_test_passkey($testid);

$keyz = clean($_POST['keyz']);
if ($keyz == "") {
    echo "3";
    exit();
}
$keyz_arr = explode("-", $keyz);
$found = false;
$pk = "";

//echo"Matching: ". count($testkey_arr)."  -- ". count($keyz_arr)." ";
$testkey_arr = str_split($testkey);
if (count($testkey_arr) == count($keyz_arr)) {
    $match = true;
    for ($i = 0; $i < count($testkey_arr); $i++) {
        $tka = $testkey_arr[$i];
        $ka = $keyz_arr[$i];
        if (!stripos($ka, $tka) && stripos($ka, $tka) !== 0) {
            $match = false;
            break;
        }
    }
    if ($match == true) {
        //setcookies
        //setcookie("registercomputer", $testid, 0);
        echo "1";
        exit();
    }
    echo "0";
    exit();
}
?>