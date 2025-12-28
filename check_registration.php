<?php

if (!isset($_SESSION))
    session_start();

function getIpAddress() {
    header('Cache-Control: no-cache, must-revalidate');
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getmacaddress() {
    $sample_arr = array();
    $pingcommand = getIpAddress();
    $sample_str = shell_exec('arp -a ' . $pingcommand);
    $sample_arr = explode("\n", $sample_str);
    $sample_arr_ = explode(" ", $sample_arr[3]);
    $final_arr = array();
    foreach ($sample_arr_ as $val) {
        if ($val != "") {
            $final_arr[] = $val;
        }
    }
    $mac = $final_arr[1];
    return $mac;
}

function pc_registered() {
    $mac = getmacaddress();
    $query = "select * from tblvenuecomputers where(computermac=?)";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($mac));
    if ($stmt->rowCount() == 0) {
        return false;
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (trim($row['computermac']) == "")
        return false;
    return true;
}

require_once(dirname(__FILE__) . "/lib/globals.php");
require_once(dirname(__FILE__) . "/lib/security.php");

authorize();
if (!has_roles(array("Super Admin")) && !has_roles(array("PC Registrar"))) {
    echo"2";
    exit;
}
if (!pc_registered())
    echo"1";
else
    echo"0";
?>